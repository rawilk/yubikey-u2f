---
title: Authorization
sort: 2
---

## Introduction

In your application UI, you should make it possible to rename a security key and also delete (disassociate) them for the user. If you're using the model and table provided by the package, you can do this via the `\Rawilk\Yubikey\Models\YubikeyIdentity` model. For convenience, we have provided a model policy for authorizing a user to make these changes. You are free to add your own authorization logic, but what we've provided in the policy should be fine in most applications.

## Rename
To authorize a user for renaming a security key, you can call the `rename` method on the model's policy:

```php
$this->authorize('rename', $yubikeyIdentity);

// Or
\Illuminate\Support\Facades\Gate::allows('rename', $yubikeyIdentity);
```

## Delete
To authorize a user for deleting a security key, you can call the `delete` method on the model's policy:

```php
$this->authorize('delete', $yubikeyIdentity);

// Or
\Illuminate\Support\Facades\Gate::allows('delete', $yubikeyIdentity);
```

In all authorizations, the policy is checking to ensure the user id on the security key matches the current user's id.
