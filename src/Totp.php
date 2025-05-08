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
}