<?php
// app/helpers/upload_helper.php
/**
 * Sends an OTP to the user's email
 *
 * @param string $path the path to upload the file (starting with a '/')
 * @param array $file The file information from the $_FILES superglobal
 * @param string $prefix A prefix to add to the uploaded file name
 */

function uploadFile($file, $path, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    $fileType = $file['type'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, $allowedExtensions)) {
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    $uploadDir = ROOT_PATH .'/public/uploads' . $path;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = $prefix . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $path . '/' . $fileName; // Return web-accessible path
    }

    return false;
}