<?php

namespace Rawilk\Yubikey\Commands;

use Illuminate\Console\Command;

class YubikeyCommand extends Command
{
    public $signature = 'yubikey-u2f';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
