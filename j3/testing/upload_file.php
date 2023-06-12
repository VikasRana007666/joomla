<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('JPATH_BASE', realpath(dirname(__FILE__, 2) . '/../'));

if (isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] === UPLOAD_ERR_OK) {
    $tmpFilePath = $_FILES['uploadFile']['tmp_name'];
    $targetDir = JPATH_BASE . '/images/';
    $fileName = uniqid() . '_' . $_FILES['uploadFile']['name'];
    $destination = $targetDir . $fileName;
    move_uploaded_file($tmpFilePath, $destination);
} else {
    echo 'No file uploaded or an error occurred.';
}

header("Location: index.php");
