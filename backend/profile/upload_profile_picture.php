<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['id'];

// Check if file was uploaded
if (!isset($_FILES['profile_picture'])) {
    echo json_encode(['status'=>'error','message'=>'No file uploaded']);
    exit;
}

$file = $_FILES['profile_picture'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'Upload error: ' . $file['error']]);
    exit;
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['status'=>'error','message'=>'Invalid file type. Only JPG, PNG, GIF, WEBP allowed']);
    exit;
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status'=>'error','message'=>'File too large. Maximum 5MB']);
    exit;
}

try {
    // Define upload directory (relative to this PHP file)
    // This file is at: /var/www/html/projet/backend/profile/upload_profile_picture.php
    // We want: /var/www/html/projet/uploads/profiles/
    $upload_dir = __DIR__ . '/../../uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo json_encode(['status'=>'error','message'=>'Failed to create upload directory']);
            exit;
        }
    }

    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        echo json_encode(['status'=>'error','message'=>'Upload directory is not writable. Please run: sudo chmod -R 755 /var/www/html/projet/uploads && sudo chown -R www-data:www-data /var/www/html/projet/uploads']);
        exit;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['status'=>'error','message'=>'Failed to move uploaded file. Check permissions.']);
        exit;
    }

    // Delete old profile picture if exists
    $oldStmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $oldStmt->execute([$user_id]);
    $old_picture = $oldStmt->fetch(PDO::FETCH_ASSOC)['profile_picture'];
    
    if ($old_picture) {
        $old_file_path = __DIR__ . '/../../' . $old_picture;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }

    // Update database with relative path
    $db_path = 'uploads/profiles/' . $filename;
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->execute([$db_path, $user_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile picture updated successfully',
        'picture_url' => $db_path
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>