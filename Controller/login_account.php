<?php
include '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy thông tin từ form
    $phone    = trim($_POST['phone'] ?? '');  
    $password = trim($_POST['password'] ?? '');
    $action   = $_POST['action'] ?? '';  

    // Kiểm tra rỗng
    if ($phone === '' || $password === '') {
        echo "<script>alert('Vui lòng nhập đầy đủ số điện thoại và mật khẩu!'); history.back();</script>";
        exit;
    }

    // Truy vấn lấy thông tin user theo phone
    $stmt = $conn->prepare("SELECT id, phone, fullname, password FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    // Không tồn tại số điện thoại
    if ($result->num_rows !== 1) {
        echo "<script>alert('Số điện thoại không tồn tại!'); history.back();</script>";
        exit;
    }

    // Lấy dữ liệu user
    $user = $result->fetch_assoc();
    $hashed_password = $user['password'];

    // So sánh password với hash
    if (!password_verify($password, $hashed_password)) {
        echo "<script>alert('Mật khẩu không chính xác!'); history.back();</script>";
        exit;
    }
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['phone'] = $user['phone'];
    // Điều hướng theo action
    if ($action === 'manage') {
        header("Location: ../View/manage.php");
        exit;
    }

    if ($action === 'sell') {
        header("Location: ../View/sell.php");
        exit;
    }

    echo "<script>alert('Không xác định hành động, vui lòng thử lại.'); history.back();</script>";
    exit;
}
?>
