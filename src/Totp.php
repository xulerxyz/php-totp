<?php

namespace Contipay\PhpTotp;

class Totp
{
    private $secret;
    private $interval;

    public function __construct(string $secret, int $interval = 30)
    {
        $this->secret = $secret;
        $this->interval = $interval;
    }

    public function now(): string
    {
        $timestamps = floor(time() / $this->interval);

        return $this->generateCode($this->base32Decode($this->secret), $timestamps);
    }

    public function generateCode(string $key, int $counter): string
    {
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord($hash[19]) & 0x0F;

        $value = (ord($hash[$offset + 0]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF);

        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    public function base32Decode(string $base32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32 = strtoupper($base32);
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        foreach (str_split($base32) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $pos;
            $bitsLeft += 5;

            while ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Generate a Google Authenticator compatible TOTP URL
     * 
     * @param string $label The label to display in the authenticator app (e.g., "user@example.com")
     * @param string $issuer The issuer name (e.g., "Your Company Name")
     * @return string The TOTP URL that can be used to generate a QR code
     */
    public function getTotpUrl(string $label, string $issuer): string
    {
        $label = rawurlencode($label);
        $issuer = rawurlencode($issuer);
        $secret = $this->secret;

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Generate a Google Charts QR code URL for the TOTP
     * 
     * @param string $label The label to display in the authenticator app (e.g., "user@example.com")
     * @param string $issuer The issuer name (e.g., "Your Company Name")
     * @param int $size The size of the QR code in pixels (default: 200)
     * @return string The URL to the QR code image
     */
    public function getQrCodeUrl(string $label, string $issuer, int $size = 200): string
    {
        $totpUrl = $this->getTotpUrl($label, $issuer);
        $encodedUrl = rawurlencode($totpUrl);
        
        return "https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chl={$encodedUrl}";
    }

    /**
     * Validate a TOTP code
     * 
     * @param string $code The code to validate
     * @param int $window The time window to check (default: 1, meaning current and previous interval)
     * @return bool True if the code is valid, false otherwise
     */
    public function validateCode(string $code, int $window = 1): bool
    {
        $currentTimestamp = floor(time() / $this->interval);
        $key = $this->base32Decode($this->secret);

        // Check current and previous intervals
        for ($i = -$window; $i <= $window; $i++) {
            $timestamp = $currentTimestamp + $i;
            $generatedCode = $this->generateCode($key, $timestamp);
            
            if (hash_equals($generatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a new random secret key
     * 
     * @param int $length The length of the secret key (default: 16)
     * @return string A new random secret key
     */
    public static function generateSecret(int $length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }
        
        return $secret;
    }
}