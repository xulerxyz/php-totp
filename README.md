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

### Generate New Secret Key

```php
// Generate a new random secret key
$secret = Totp::generateSecret();
$totp = new Totp($secret);
```

### Google Authenticator Integration

```php
// Generate a TOTP URL for Google Authenticator
$totpUrl = $totp->getTotpUrl('user@example.com', 'Your Company Name');

// The URL can be used to generate a QR code
// Format: otpauth://totp/{label}?secret={secret}&issuer={issuer}
```

### Generate a Google QR Code URL

```php
// Generate a Google Charts QR code URL for the TOTP
$qrCodeUrl = $totp->getQrCodeUrl('user@example.com', 'Your Company Name');

// You can embed this URL in an <img> tag in your HTML:
// <img src="$qrCodeUrl" alt="Scan this QR code with Google Authenticator">
```

### Validate TOTP Code

```php
// Validate a TOTP code
$code = '123456'; // The code to validate
$isValid = $totp->validateCode($code);

// You can also specify a time window (default is 1, meaning current and previous interval)
$isValid = $totp->validateCode($code, 2); // Check current, previous, and next interval
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
- Generates Google Authenticator compatible TOTP URLs
- Generates Google QR code URLs for easy scanning
- Includes secure random secret key generation
- Validates TOTP codes with configurable time window
- Lightweight and easy to use
- No external dependencies

## Requirements

- PHP 7.1 or higher

## Security Notes

- Always keep your secret key secure and never expose it in client-side code
- The secret key should be stored securely in your application
- Consider using environment variables or a secure configuration management system for storing secrets
- When generating QR codes, ensure the TOTP URL is transmitted securely
- Use constant-time comparison (hash_equals) for code validation to prevent timing attacks

## License

This project is licensed under the MIT License - see the LICENSE file for details. 