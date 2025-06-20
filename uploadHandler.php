<?php
header('Content-Type: application/json');
session_start();

// Check authentication
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Configuration
$uploadDir = 'uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
$maxFileSize = 50 * 1024 * 1024; // 50MB

// Create upload directory if needed
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = ['success' => false, 'message' => '', 'files' => []];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_FILES['mediaFiles'])) {
        throw new Exception('No files uploaded');
    }

    $title = $_POST['title'] ?? 'Untitled';
    $description = $_POST['description'] ?? '';

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
            throw new Exception("$fileName exceeds maximum file size");
        }

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("$fileName has invalid file type");
        }

        // Generate safe filename
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $fileName);
        $destination = $uploadDir . $safeName;

        // Move file
        if (!move_uploaded_file($tmpName, $destination)) {
            throw new Exception("Failed to save $fileName");
        }

        // Here you would typically save to database
        // saveToDatabase($title, $description, $safeName, $fileType);

        $response['files'][] = [
            'original' => $fileName,
            'saved' => $safeName,
            'type' => strpos($fileType, 'image/') === 0 ? 'image' : 'video'
        ];
    }

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