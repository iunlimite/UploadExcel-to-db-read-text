 <?php

// เชื่อมต่อกับฐานข้อมูล
function getDatabaseConnection() {
/*
    // ปรับค่าการเชื่อมต่อฐานข้อมูลตามที่ต้องการ
    $host = 'mysql-2afd1ff8-iunlimite.e.aivencloud.com';  // ชื่อโฮสต์หรือ IP
    $username = 'avnadmin';           // ชื่อผู้ใช้
    $password = 'AVNS_XgH4NLuddeADvDVG6QL';  // รหัสผ่าน
    $dbname = 'defaultdb';    // ชื่อฐานข้อมูล
    $port = '12073';                 // ระบุหมายเลข port
*/
    // ปรับค่าการเชื่อมต่อฐานข้อมูลตามที่ต้องการ
    $host = 'localhost';  // ชื่อโฮสต์หรือ IP
    $username = 'root';           // ชื่อผู้ใช้
    $password = '@passwordMy';  // รหัสผ่าน
    $dbname = 'myupload';    // ชื่อฐานข้อมูล
    $port = '3306';                 // ระบุหมายเลข port

     // สร้างการเชื่อมต่อ
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
    }

    return $conn;
 }

// ฟังก์ชันสร้างตาราง
function createRequiredTables() {
    $conn = getDatabaseConnection();

    // ตารางเก็บข้อความจากแถว
    $sql1 = "CREATE TABLE IF NOT EXISTS `row_texts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `row_text` TEXT,
        `row_number` INT,
        `filename` VARCHAR(255),
        `upload_status` ENUM('pending', 'processed') DEFAULT 'pending',
        `batch_id` VARCHAR(255),  // เพิ่มคอลัมน์ batch_id
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // ตารางเก็บไฟล์
    $sql2 = "CREATE TABLE IF NOT EXISTS `uploaded_files` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `filename` VARCHAR(255),
        `original_name` VARCHAR(255),
        `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // สร้างตารางในฐานข้อมูล
    $conn->query($sql1);
    $conn->query($sql2);
    $conn->close();
}


// ฟังก์ชันเพิ่มคอลัมน์ batch_id ถ้ายังไม่มี
function addBatchIdColumn($conn) {
    // ตรวจสอบว่าคอลัมน์ 'batch_id' มีอยู่ในตาราง 'row_texts' หรือไม่
    $checkColumnQuery = "SHOW COLUMNS FROM `row_texts` LIKE 'batch_id'";
    $result = $conn->query($checkColumnQuery);

    // หากคอลัมน์ไม่พบ, ให้เพิ่มคอลัมน์ใหม่
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE `row_texts` ADD COLUMN `batch_id` VARCHAR(255)";
        if ($conn->query($sql) === TRUE) {
            echo "คอลัมน์ 'batch_id' ได้รับการเพิ่มเรียบร้อยแล้ว!";
        } else {
            echo "เกิดข้อผิดพลาดในการเพิ่มคอลัมน์: " . $conn->error;
        }
    } else {
       // echo "คอลัมน์ 'batch_id' มีอยู่แล้ว!";
    }
}

// เชื่อมต่อกับฐานข้อมูล
$conn = getDatabaseConnection();

// เรียกใช้ฟังก์ชันเพิ่มคอลัมน์
addBatchIdColumn($conn);

?>
