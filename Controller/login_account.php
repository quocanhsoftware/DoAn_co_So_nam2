<?php
include '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $action   = $_POST['action'] ?? '';
    $shop_id  = intval($_POST['shop_id'] ?? 0);

    if ($phone === '' || $password === '' || $shop_id === 0) {
        echo "<script>alert('Thiếu thông tin!'); history.back();</script>";
        exit;
    }
    // Kiểm tra phone + password của đúng shop_id
    $stmt = $conn->prepare("SELECT id, fullname, phone, password FROM users WHERE id = ? AND phone = ?");
    $stmt->bind_param("is", $shop_id, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo "<script>alert('Số điện thoại hoặc mật khẩu không đúng!'); history.back();</script>";
        exit;
    }

    $user = $result->fetch_assoc();

    // So sánh mật khẩu hash
    if (!password_verify($password, $user['password'])) {
        echo "<script>alert('Số điện thoại hoặc mật khẩu không đúng!'); history.back();</script>";
        exit;
    }
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['phone'] = $user['phone'];
    // Chuyển trang theo action
    if ($action === 'manage') {
        header("Location: ../View/manage.php?shop_id=$shop_id");
        exit;
    }
    if ($action === 'sell') {
        header("Location: ../View/sell.php?shop_id=$shop_id");
        exit;
    }

    echo "<script>alert('Hành động không hợp lệ!'); history.back();</script>";
    exit;
}
?>
