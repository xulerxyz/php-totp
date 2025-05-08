<?php

require_once 'vendor/autoload.php';

use Contipay\PhpTotp\Totp;

$secret = 'JBSWY3DPEHPK3PXP';
$totp = new Totp($secret);


// return json http response
header('Content-Type: application/json');

echo json_encode(['current_code' => $totp->now()]);
