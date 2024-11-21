<?php
require_once 'config.php';

$conn = getDatabaseConnection();
$result = $conn->query("SELECT * FROM row_texts WHERE upload_status = 'pending'");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบข้อความที่ได้จาก Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">ข้อความที่ได้จากไฟล์ Excel</h2>
            </div>
            <div class="card-body">
                <form method="post" action="confirm_upload.php" id="processForm">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" checked>
                                    </th>
                                    <th>แถวที่</th>
                                    <th>ข้อความ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_rows[]"
                                               value="<?php echo $row['id']; ?>" checked>
                                    </td>
                                    <td><?php echo $row['row_number']; ?></td>
                                    <td><?php echo htmlspecialchars($row['row_text']); ?>
                                        <textarea class="form-control" style="text-align: left" rows="2" readonly hidden>
                                            <?php echo htmlspecialchars($row['row_text']); ?>
                                        </textarea>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" name="action" value="confirm" class="btn btn-success me-2">
                            ยืนยันการอัปโหลด
                        </button>
                        <button type="submit" name="action" value="reset" class="btn btn-danger">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_rows[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    </script>
</body>
</html>
