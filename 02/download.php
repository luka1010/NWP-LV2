<?php
session_start();

$file = $_GET['file'];

//decrypt
$decryption_key = md5('encryptionkey');
$cipher = "AES-128-CTR";
$options = 0;
$decryption_iv = $_SESSION['iv'];
$encryptedData = file_get_contents("uploads/$file.txt");

$decryptedData = base64_decode($encryptedData);
$data = openssl_decrypt($decryptedData, $cipher, $decryption_key, $options, $decryption_iv);

//put file in directory
$file = "uploads/$file";
file_put_contents($file, $data);

clearstatcache();

//download
if(file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file, true);

    unlink($file);

    die();
}
