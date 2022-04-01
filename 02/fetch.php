<?php
session_start();

$txtFile = function ($value) {
    return (pathinfo($value, PATHINFO_EXTENSION) === 'txt');
};

if (!is_dir("uploads/")) {
    echo "<p>No directory.</p>";
    die();
}

//get .txt files
$files = array_diff(scandir("uploads/"), array('..', '.'));
$files = array_filter($files, $txtFile);

if (count($files) === 0) {
    echo "<p>No files in directory.</p>";
} else {
    echo "<ul>";
    //go through every file and render them for download
    foreach ($files as $file) {
        $nameWithoutExtension = substr($file, 0, strlen($file) - 4);

        echo "<li><a href=\"download.php?file=$nameWithoutExtension\">$nameWithoutExtension</a></li>";
    }
    echo "</ul>";
}