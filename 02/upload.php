<?php
session_start();

//getting file
$fileName = $_FILES['file']['name'];

$location = "uploads/" . $fileName;
$imageFileType = pathinfo($location, PATHINFO_EXTENSION);
$validExtensions = array("jpg", "jpeg", "png", "pdf");

if (!in_array(strtolower($imageFileType), $validExtensions)) {
    echo "<p>Invalid file format ($imageFileType).</p>";
    die();
}

$content = file_get_contents($_FILES['file']['tmp_name']);

//encrypting
$encryptionKey = md5('encryptionkey');
$cipher = "AES-128-CTR";
$iv_length = openssl_cipher_iv_length($cipher);
$options = 0;

$encryption_iv = random_bytes($iv_length);
$encrypted = openssl_encrypt($content, $cipher, $encryptionKey, $options, $encryption_iv);
$encryptedData = base64_encode($encrypted);
$_SESSION['iv'] = $encryption_iv;

$fileNameWithoutExtension = substr($fileName, 0, strpos($fileName, "."));

//make directory on server
if (!is_dir("uploads/")) {
    if (!mkdir("uploads/", 0777, true)) {
        die("<p>Unable to create directory $dir.</p>");
    }
}

//put encrypted date into directory
$fileNameOnServer = "uploads/${fileNameWithoutExtension}.$imageFileType.txt";
file_put_contents($fileNameOnServer, $encryptedData);

echo "<p>File uploaded successfully</p>";