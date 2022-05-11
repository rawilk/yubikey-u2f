<?php

namespace Rawilk\Yubikey;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Rawilk\Yubikey\Enums\YubicoResponseStatus;
use Rawilk\Yubikey\Exceptions\YubikeyParseException;
use Rawilk\Yubikey\Exceptions\YubikeyVerifyException;

class Yubikey
{
    protected readonly string $secretKey;

    /**
     * Flag to request timestamp and session counter information in a verify response.
     */
    protected bool $useTimestamp = false;

    /**
     * A value of 0 to 100 indicating percentage of syncing required by client.
     * If absent, let the server decide.
     *
     * Also accepts `fast` or `secure` to use server-configured values.
     */
    protected ?string $sl = null;

    /**
     * Number of seconds to wait for sync responses. If absent,
     * let the server decide.
     */
    protected ?int $timeout = null;

    protected readonly array $urls;

    public function __construct(
        protected readonly string $clientId,
        string $secretKey
    )
    {
        $this->secretKey = base64_decode($secretKey);
        $this->urls = config('yubikey-u2f.urls', [
            'api.yubico.com/wsapi/2.0/verify',
        ]);
    }

    public function useTimestamp(): self
    {
        $this->useTimestamp = true;

        return $this;
    }

    public function sl(string $sl): self
    {
        $this->sl = $sl;

        return $this;
    }

    public function timeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Verify the given security key OTP token is valid for a given user account.
     *
     * @param string $token The OTP token provided from the YubiKey
     * @return array<string, string>
     * @throws \Rawilk\Yubikey\Exceptions\YubikeyParseException
     * @throws \Rawilk\Yubikey\Exceptions\YubikeyVerifyException
     */
    public function verify(string $token): array
    {
        $this->ensureValidApiCredentials();

        $parsedOtp = $this->parseOtpToken($token);
        if (! $parsedOtp) {
            throw YubikeyParseException::invalidToken();
        }

        $request = [
            'id' => $this->clientId,
            'otp' => $parsedOtp['otp'],
            'nonce' => bin2hex(random_bytes(16)),
        ];

        if ($this->useTimestamp) {
            $request['timestamp'] = 1;
        }

        if ($this->sl) {
            $request['sl'] = $this->sl;
        }

        if ($this->timeout) {
            $request['timeout'] = $this->timeout;
        }

        $request['h'] = $this->generateSignature($request);

        $responses = Http::pool(function (Pool $pool) use ($request) {
            foreach ($this->urls as $url) {
                $pool->get($url, $request);
            }
        });

        return $this->handleResponses($responses);
    }

    /**
     * Process the responses returned from the Yubico servers.
     * Typically, we should only need to process the first response
     * if all the servers are online and working correctly.
     *
     * @param array<int, \Illuminate\Http\Client\Response> $responses
     * @return array<string, string>
     * @throws \Rawilk\Yubikey\Exceptions\YubikeyVerifyException
     */
    protected function handleResponses(array $responses): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = array_filter($responses, fn (Response $response) => $response->ok())[0] ?? null;
        if (! $response) {
            throw YubikeyVerifyException::backendError();
        }

        $body = $this->parseResponse($response->body());

        // First we need to verify a valid signature was returned.
        $this->verifyResponseSignature($body);

        // Now we need to check if the response status is ok.
        // If it is, we can return the authentication as being successful.
        $this->ensureResponseStatusIsOk($body['status']);

        // We will return the OTP identity for the security key so the application
        // can either associate it with a user account, or verify it belongs to
        // a given user.
        return array_merge($body, [
            'identity' => $this->getOtpIdentity($body['otp']),
        ]);
    }

    protected function verifyResponseSignature(array $response): void
    {
        $params = [
            'nonce',
            'otp',
            'sessioncounter',
            'sessionuse',
            'sl',
            'status',
            't',
            'timeout',
            'timestamp',
        ];

        sort($params);

        $check = '';

        foreach ($params as $param) {
            if (array_key_exists($param, $response)) {
                $check .= "&{$param}={$response[$param]}";
            }
        }

        $check = ltrim($check, '&');

        $checkSignature = base64_encode(hash_hmac('sha1', utf8_encode($check), $this->secretKey, true));

        if (! hash_equals($response['h'], $checkSignature)) {
            throw YubikeyVerifyException::invalidSignature();
        }
    }

    protected function ensureResponseStatusIsOk(string $status): bool
    {
        $enum = YubicoResponseStatus::tryFrom($status);
        if (! $enum) {
            throw YubikeyVerifyException::unknownStatus();
        }

        return match ($enum) {
            YubicoResponseStatus::OK => true,
            YubicoResponseStatus::BACKEND_ERROR => throw YubikeyVerifyException::backendError(),
            YubicoResponseStatus::BAD_SIGNATURE => throw YubikeyVerifyException::badSignature(),
            YubicoResponseStatus::BAD_OTP => throw YubikeyVerifyException::badOtp(),
            YubicoResponseStatus::MISSING_PARAMETER => throw YubikeyVerifyException::missingParameter(),
            YubicoResponseStatus::NO_SUCH_CLIENT => throw YubikeyVerifyException::noSuchClient($this->clientId),
            YubicoResponseStatus::NOT_ENOUGH_ANSWERS => throw YubikeyVerifyException::notEnoughAnswers(),
            YubicoResponseStatus::OPERATION_NOT_ALLOWED => throw YubikeyVerifyException::operationNotAllowed(),
            YubicoResponseStatus::REPLAYED_OTP => throw YubikeyVerifyException::replayedOtp(),
            YubicoResponseStatus::REPLAYED_REQUEST => throw YubikeyVerifyException::replayedRequest(),
        };
    }

    protected function generateSignature(array $parameters): string
    {
        ksort($parameters);

        $signature = base64_encode(hash_hmac('sha1', http_build_query($parameters), $this->secretKey, true));

        return preg_replace('/\+/', '%2B', $signature);
    }

    protected function getOtpIdentity(string $otp): string
    {
        // According to the docs (https://developers.yubico.com/yubikey-val/Getting_Started_Writing_Clients.html),
        // we should always be able to strip off the last 32 characters of the otp string, and the remaining value
        // is the identity of that security key.
        return strtolower(substr($otp, 0, -32));
    }

    /**
     * Parse a given YubiKey token into an array of OTP, password, prefix, and ciphertext.
     *
     * @param string $token
     * @param string $delimiter Optional delimiter re-class; default should work in most cases.
     * @return bool|array<string, string>
     */
    protected function parseOtpToken(string $token, string $delimiter = '[:]'): bool|array
    {
        if (! preg_match("/^((.*)" . $delimiter . ")?(([cbdefghijklnrtuv]{0,16})([cbdefghijklnrtuv]{32}))$/i", $token, $matches)) {
            if (! preg_match("/^((.*)" . $delimiter . ")?(([jxe\.uidchtnbpygk]{0,16})([jxe\.uidchtnbpygk]{32}))$/i", $token, $matches)) {
                return false;
            }

            $otp = strtr($matches[3], "jxe.uidchtnbpygk", "cbdefghijklnrtuv");
        } else {
            $otp = $matches[3];
        }

        return [
            'otp' => $otp,
            'password' => $matches[2],
            'prefix' => $matches[4],
            'ciphertext' => $matches[5],
        ];
    }

    protected function parseResponse(string $response): array
    {
        $rows = explode("\r\n", trim($response));

        $data = [];
        foreach ($rows as $row) {
            $row = preg_replace('/=/', '#', $row, 1);
            [$key, $value] = explode('#', $row);

            $data[$key] = $value;
        }

        return $data;
    }

    protected function ensureValidApiCredentials(): void
    {
        if (! $this->clientId) {
            throw YubikeyVerifyException::missingClientId();
        }

        if (! $this->secretKey) {
            throw YubikeyVerifyException::missingClientSecret();
        }
    }
}
