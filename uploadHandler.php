<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Configuration
$uploadDir = 'uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$maxTotalFiles = 5;

// Create upload directory if needed
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Check existing files
$metadataFile = $uploadDir . session_id() . '_metadata.json';
$mediaFiles = [];

if (file_exists($metadataFile)) {
    $mediaFiles = json_decode(file_get_contents($metadataFile), true) ?: [];
}

// Check if user has reached max files
if (count($mediaFiles) >= $maxTotalFiles) {
    echo json_encode(['success' => false, 'message' => 'You have reached the maximum number of files (5)']);
    exit();
}

$response = ['success' => false, 'message' => '', 'files' => []];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_FILES['mediaFiles'])) {
        throw new Exception('No files uploaded');
    }

    // Check if new files would exceed limit
    $newFileCount = count($_FILES['mediaFiles']['tmp_name']);
    if ((count($mediaFiles) + $newFileCount) > $maxTotalFiles) {
        throw new Exception("Uploading these files would exceed your limit of $maxTotalFiles files");
    }

    foreach ($_FILES['mediaFiles']['tmp_name'] as $i => $tmpName) {
        $fileName = $_FILES['mediaFiles']['name'][$i];
        $fileSize = $_FILES['mediaFiles']['size'][$i];
        $fileType = $_FILES['mediaFiles']['type'][$i];
        $fileError = $_FILES['mediaFiles']['error'][$i];

        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading $fileName");
        }

        if ($fileSize > $maxFileSize) {
            throw new Exception("$fileName exceeds maximum file size (5MB)");
        }

        // In the file validation section, add:
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            throw new Exception("$fileName has invalid file extension. Only JPG, PNG, and GIF are allowed");
        }

        // Add image dimension validation if needed
        $imageInfo = getimagesize($tmpName);
        if (!$imageInfo) {
            throw new Exception("$fileName is not a valid image file");
        }

        

        // Generate safe filename
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $fileName);
        $destination = $uploadDir . $safeName;

        // Move file
        if (!move_uploaded_file($tmpName, $destination)) {
            throw new Exception("Failed to save $fileName");
        }

        // Add to metadata
        $mediaFiles[] = [
            'name' => $safeName,
            'original' => $fileName,
            'type' => $fileType,
            'size' => $fileSize,
            'uploaded' => date('Y-m-d H:i:s')
        ];

        $response['files'][] = [
            'original' => $fileName,
            'saved' => $safeName
        ];
    }

    // Add this after successful upload to limit total files per user
    $maxFilesPerUser = 10; // Keep last 10 files per user
    if (count($mediaFiles) > $maxFilesPerUser) {
        $filesToDelete = array_slice($mediaFiles, 0, count($mediaFiles) - $maxFilesPerUser);
        foreach ($filesToDelete as $file) {
            $oldFilePath = $uploadDir . $file['name'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
        $mediaFiles = array_slice($mediaFiles, -$maxFilesPerUser);
    }

    // Save metadata
    file_put_contents($metadataFile, json_encode($mediaFiles));

    $response['success'] = true;
    $response['message'] = count($_FILES['mediaFiles']['name']) . ' files uploaded successfully';

} catch (Exception $e) {
    // Cleanup any uploaded files if error occurred
    foreach ($response['files'] as $file) {
        if (file_exists($uploadDir . $file['saved'])) {
            unlink($uploadDir . $file['saved']);
        }
    }
    $response['message'] = $e->getMessage();
}

echo json_encode($response);