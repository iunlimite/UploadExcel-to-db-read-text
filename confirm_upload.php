<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getDatabaseConnection();

    if ($_POST['action'] == 'reset') {
        $conn->query("DELETE FROM row_texts");
        $conn->query("DELETE FROM uploaded_files");
        header("Location: index.php");
        exit();
    }

    if ($_POST['action'] == 'confirm' && !empty($_POST['selected_rows'])) {
        $selectedRows = $_POST['selected_rows'];
        $placeholders = implode(',', array_fill(0, count($selectedRows), '?'));
        $stmt = $conn->prepare("UPDATE row_texts SET upload_status = 'processed' WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($selectedRows)), ...$selectedRows);
        $stmt->execute();

        header("Location: index.php?status=success");
        exit();
    }
}

header("Location: index.php");
exit();
?>
