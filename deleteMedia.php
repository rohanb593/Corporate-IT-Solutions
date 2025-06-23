<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$filename = $_POST['filename'] ?? null;
if (!$filename) {
    echo json_encode(['success' => false, 'message' => 'No filename provided']);
    exit();
}

// Security check - prevent directory traversal
$filename = basename($filename);
$filePath = 'uploads/' . $filename;

// Check if file exists
if (!file_exists($filePath)) {
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit();
}

// Update metadata file
$metadataFile = 'uploads/' . session_id() . '_metadata.json';
$mediaFiles = [];

if (file_exists($metadataFile)) {
    $mediaFiles = json_decode(file_get_contents($metadataFile), true) ?: [];
}

// Remove file from metadata
$mediaFiles = array_filter($mediaFiles, function($file) use ($filename) {
    return $file['name'] !== $filename;
});

// Delete the file
if (unlink($filePath)) {
    // Save updated metadata
    file_put_contents($metadataFile, json_encode(array_values($mediaFiles)));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not delete file']);
}
?>