<?php
require_once 'config.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    $file = $_FILES['excelFile'];
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (!in_array($file['type'], $allowedTypes)) {
        die("กรุณาอัพโหลดไฟล์ Excel เท่านั้น");
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        die("ไม่สามารถสร้างโฟลเดอร์สำหรับอัปโหลดไฟล์ได้");
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $conn = getDatabaseConnection();
        $batchId = uniqid();

        // บันทึกข้อมูลไฟล์ในฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO `uploaded_files` (`filename`, `original_name`) VALUES (?, ?)");
        $stmt->bind_param("ss", $filename, $file['name']);
        $stmt->execute();
        $stmt->close();

        try {
            // โหลดไฟล์ Excel
            $spreadsheet = IOFactory::load($destination);
            $worksheet = $spreadsheet->getSheetByName('auditeeReq'); // เลือก Sheet ชื่อ 'auditeeReq'

            if (!$worksheet) {
                throw new Exception("ไม่พบ Sheet ชื่อ 'auditeeReq' ในไฟล์ที่อัปโหลด");
            }

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // อ่านข้อมูลในแต่ละแถว
            for ($row = 2; $row <= $highestRow; $row++) { // เริ่มที่แถวที่ 2 (ข้าม header)
                $rowText = '';

                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();
                    if ($cellValue !== null) {
                        $rowText .= trim($cellValue) . ' ';
                    }
                }

                $rowText = preg_replace('/\s+/', ' ', trim($rowText));
                if (!empty($rowText)) {
                    $stmt = $conn->prepare(
                        "INSERT INTO `row_texts` (`row_text`, `row_number`, `filename`, `batch_id`) VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("siss", $rowText, $row, $filename, $batchId);
                    $stmt->execute();
                }
            }

            $conn->close();
            header("Location: process.php"); // ไปยังหน้าต่อไป
            exit();

        } catch (Exception $e) {
            die("เกิดข้อผิดพลาด: " . $e->getMessage());
        }
    } else {
        die("การอัปโหลดไฟล์ล้มเหลว");
    }
}
?>

