<?php

require_once 'vendor/autoload.php';

use Contipay\PhpTotp\Totp;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Example 1: Generate a new secret and get current TOTP code
$secret = Totp::generateSecret();
$totp = new Totp($secret);

// Example 2: Generate a TOTP URL for Google Authenticator
$totpUrl = $totp->getTotpUrl('user@example.com', 'ContiPay');

// Example 3: Generate a QR code URL
$qrCode = new QrCode($totpUrl);
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Example 4: Validate a code
$currentCode = $totp->now();
$isValid = $totp->validateCode($currentCode);

// return json http response
header('Content-Type: application/json');

echo json_encode([
    'secret' => $secret,
    'current_code' => $currentCode,
    'totp_url' => $totpUrl,
    'is_valid' => $isValid
]);


