<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อัพโหลดและสกัดข้อความจาก Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">อัพโหลดไฟล์ Excel</h2>
            </div>
            <div class="card-body">
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">เลือกไฟล์ Excel</label>
                        <input type="file" class="form-control" id="excelFile" name="excelFile" 
                               accept=".xls,.xlsx" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">อัพโหลดและประมวลผล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
