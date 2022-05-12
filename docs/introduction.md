---
title: Introduction
sort: 1
---

If you have a YubiKey from [Yubico](https://yubico.com), you can add two-factor support for a security key to your Laravel applications. Your user accounts
will be able to register up to 5 security keys (configurable) to their account, and then use those keys as a form of two-factor authentication for your application.

> {note} This package only provides the backend code necessary for verifying and associating keys with users. You will need to make the UI necessary for this and also
add the logic to your authentication workflows for two-factor authentication.
