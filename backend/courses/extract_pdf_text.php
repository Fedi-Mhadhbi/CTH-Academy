<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    echo json_encode(['status'=>'error','message'=>'No course ID provided']);
    exit;
}

try {
    // Get course PDF path
    $stmt = $pdo->prepare("SELECT id, file_path, pdf_extracted FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo json_encode(['status'=>'error','message'=>'Course not found']);
        exit;
    }
    
    // Check if already extracted
    if ($course['pdf_extracted'] == 1) {
        echo json_encode(['status'=>'success','message'=>'PDF already extracted']);
        exit;
    }
    
    $pdf_path = __DIR__ . '/../../' . $course['file_path'];
    
    if (!file_exists($pdf_path)) {
        echo json_encode(['status'=>'error','message'=>'PDF file not found: ' . $pdf_path]);
        exit;
    }
    
    // Extract text using pdftotext (from poppler-utils)
    $output_file = sys_get_temp_dir() . '/pdf_text_' . $course_id . '.txt';
    $command = "pdftotext " . escapeshellarg($pdf_path) . " " . escapeshellarg($output_file) . " 2>&1";
    
    exec($command, $output, $return_code);
    
    if ($return_code !== 0) {
        // If pdftotext not installed, try alternative method
        if (!file_exists($output_file)) {
            echo json_encode([
                'status'=>'error',
                'message'=>'PDF text extraction failed. Install poppler-utils: sudo apt install poppler-utils',
                'debug' => implode("\n", $output)
            ]);
            exit;
        }
    }
    
    if (!file_exists($output_file)) {
        echo json_encode(['status'=>'error','message'=>'Failed to extract text from PDF']);
        exit;
    }
    
    // Read extracted text
    $text = file_get_contents($output_file);
    unlink($output_file); // Clean up
    
    if (empty(trim($text))) {
        echo json_encode(['status'=>'error','message'=>'No text found in PDF (might be scanned images)']);
        exit;
    }
    
    // Clean up text
    $text = preg_replace('/\s+/', ' ', $text); // Remove extra whitespace
    $text = trim($text);
    
    // Limit to reasonable size (100,000 chars ~ 25,000 words)
    if (strlen($text) > 100000) {
        $text = substr($text, 0, 100000) . '...';
    }
    
    // Save to database
    $updateStmt = $pdo->prepare("
        UPDATE courses 
        SET pdf_text = ?, pdf_extracted = 1 
        WHERE id = ?
    ");
    $updateStmt->execute([$text, $course_id]);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'PDF text extracted successfully',
        'text_length' => strlen($text),
        'word_count' => str_word_count($text)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>