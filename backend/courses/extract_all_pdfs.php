<?php
// Standalone script to extract all PDFs - No login required
// Run this once to extract all existing PDFs

require_once __DIR__ . '/../config/db.php';

echo "Starting PDF extraction for all courses...\n\n";

try {
    // Get all courses that haven't been extracted yet
    $stmt = $pdo->query("SELECT id, title, file_path, pdf_extracted FROM courses WHERE pdf_extracted = 0");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($courses) === 0) {
        echo "✅ All PDFs already extracted!\n";
        exit;
    }
    
    echo "Found " . count($courses) . " courses to process...\n\n";
    
    foreach ($courses as $course) {
        echo "Processing: {$course['title']} (ID: {$course['id']})\n";
        
        $pdf_path = __DIR__ . '/../../' . $course['file_path'];
        
        if (!file_exists($pdf_path)) {
            echo "  ❌ PDF file not found: {$pdf_path}\n\n";
            continue;
        }
        
        // Extract text using pdftotext
        $output_file = sys_get_temp_dir() . '/pdf_text_' . $course['id'] . '.txt';
        $command = "pdftotext " . escapeshellarg($pdf_path) . " " . escapeshellarg($output_file) . " 2>&1";
        
        exec($command, $output, $return_code);
        
        if ($return_code !== 0 || !file_exists($output_file)) {
            echo "  ❌ Extraction failed\n\n";
            continue;
        }
        
        // Read extracted text
        $text = file_get_contents($output_file);
        unlink($output_file); // Clean up
        
        if (empty(trim($text))) {
            echo "  ⚠️  No text found (might be scanned images)\n\n";
            continue;
        }
        
        // Clean up text
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Limit size
        if (strlen($text) > 100000) {
            $text = substr($text, 0, 100000) . '...';
        }
        
        // Save to database
        $updateStmt = $pdo->prepare("
            UPDATE courses 
            SET pdf_text = ?, pdf_extracted = 1 
            WHERE id = ?
        ");
        $updateStmt->execute([$text, $course['id']]);
        
        $word_count = str_word_count($text);
        echo "  ✅ Extracted {$word_count} words\n\n";
    }
    
    echo "\n🎉 Extraction complete!\n";
    
    // Show summary
    $summary = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(pdf_extracted) as extracted,
            COUNT(*) - SUM(pdf_extracted) as pending
        FROM courses
    ")->fetch(PDO::FETCH_ASSOC);
    
    echo "\nSummary:\n";
    echo "  Total courses: {$summary['total']}\n";
    echo "  Extracted: {$summary['extracted']}\n";
    echo "  Pending: {$summary['pending']}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>