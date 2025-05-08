# PHP TOTP Library

A simple and lightweight PHP library for generating Time-based One-Time Passwords (TOTP) according to RFC 6238.

## Installation

You can install the library using Composer:

```bash
composer require contipay/php-totp
```

## Usage

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use Contipay\PhpTotp\Totp;

// Initialize with your secret key
$secret = 'JBSWY3DPEHPK3PXP';
$totp = new Totp($secret);

// Get current TOTP code
$code = $totp->now();
echo $code; // Outputs a 6-digit code
```

### Custom Interval

By default, the library uses a 30-second interval. You can customize this when initializing the TOTP object:

```php
// Initialize with a custom interval (in seconds)
$totp = new Totp($secret, 60); // 60-second interval
```

## Features

- Generates TOTP codes according to RFC 6238
- Supports custom time intervals
- Handles Base32 decoding internally
- Lightweight and easy to use
- No external dependencies

## Requirements

- PHP 7.1 or higher

## Security Notes

- Always keep your secret key secure and never expose it in client-side code
- The secret key should be stored securely in your application
- Consider using environment variables or a secure configuration management system for storing secrets

## License

This project is licensed under the MIT License - see the LICENSE file for details. 