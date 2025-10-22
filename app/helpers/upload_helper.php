<?php
// app/helpers/upload_helper.php

function uploadFile($file, $prefix = '') {
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

    $uploadDir = ROOT_PATH . '/public/img/signup';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = $prefix . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'img/signup/' . $fileName; // Return web-accessible path
    }

    return false;
}