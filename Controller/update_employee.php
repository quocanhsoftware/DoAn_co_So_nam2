<?php
// Controller/update_employee.php
header('Content-Type: application/json; charset=utf-8');
require_once("../Model/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy và kiểm tra ID nhân viên
    $employee_id = $_POST['employee_id'] ?? null;

    if (empty($employee_id) || !is_numeric($employee_id)) {
        echo json_encode(["status" => "error", "message" => "ID nhân viên không hợp lệ hoặc bị thiếu."]);
        $conn->close();
        exit;
    }
    
    // Lấy dữ liệu từ form
    $id_safe = (int)$employee_id;
    $employee_code  = $_POST['employee_code'] ?? '';
    $timekeeping_code = $_POST['timekeeping_code'] ?? '';
    $fullname       = $_POST['fullname'] ?? '';
    $phone          = $_POST['phone'] ?? '';
    $cccd           = $_POST['cccd'] ?? '';
    $department_id  = $_POST['department'] ?? '';
    $position_id    = $_POST['position'] ?? '';
    
    // Làm sạch dữ liệu đầu vào
    $employee_code  = $conn->real_escape_string($employee_code);
    $timekeeping_code = $conn->real_escape_string($timekeeping_code);
    $fullname       = $conn->real_escape_string($fullname);
    $phone          = $conn->real_escape_string($phone);
    $cccd           = $conn->real_escape_string($cccd);
    $department_id_db = $department_id ? "'" . $conn->real_escape_string($department_id) . "'" : "NULL";
    $position_id_db = $position_id ? "'" . $conn->real_escape_string($position_id) . "'" : "NULL";

    $avatar_update_clause = ""; // Mặc định không cập nhật ảnh

    // 2. Xử lý upload ảnh (nếu có file mới)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/'; 
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $avatar_update_clause = ", avatar = '" . $conn->real_escape_string($file_name) . "'";
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi lưu ảnh nhân viên"]);
            $conn->close();
            exit;
        }
    }


    // 3. Chuẩn bị và thực thi câu lệnh SQL UPDATE
    $sql = "UPDATE employees SET 
                employee_code = '$employee_code',
                timekeeping_code = '$timekeeping_code',
                fullname = '$fullname',
                phone = '$phone',
                cccd = '$cccd',
                department_id = {$department_id_db},
                position_id = {$position_id_db}
                {$avatar_update_clause}
            WHERE id = $id_safe";

    if ($conn->query($sql)) {
        echo json_encode(["status"=>"success","message"=>"Cập nhật nhân viên **$fullname** thành công!"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Lỗi khi cập nhật nhân viên: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Phương thức yêu cầu không hợp lệ"]);
}
?>