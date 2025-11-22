<?php
include '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $nameshop = trim($_POST['storeName'] ?? '');

    if ($nameshop == '') {
        echo "<script>alert('Vui lòng nhập tên cửa hàng'); history.back();</script>";
        exit;
    }

    // Truy vấn kiểm tra nameshop
    $stmt = $conn->prepare("SELECT id FROM users WHERE nameshop = ?");
    $stmt->bind_param("s", $nameshop);
    $stmt->execute();
    $result = $stmt->get_result();

    // Nếu tìm thấy cửa hàng
    if ($result->num_rows === 1) {
        header("Location: ../View/login.php");
        exit;

    } else {
        echo "<script>alert('Tên cửa hàng không tồn tại!'); history.back();</script>";
        exit;
    }
}
?>
