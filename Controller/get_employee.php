<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../Model/db.php"); // dùng file DB sẵn có

// Lấy giá trị (nếu không có thì để rỗng)
$search     = $_GET['search'] ?? '';
$department = $_GET['department'] ?? '';
$position   = $_GET['position'] ?? '';
$status     = $_GET['status'] ?? '';

$sql = "SELECT * FROM employees WHERE 1=1";

// Tìm kiếm theo tên hoặc mã
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (fullname LIKE '%$search%' OR employee_code LIKE '%$search%')";
}

// Lọc trạng thái (working / retired)
if (!empty($status)) {
    $status = $conn->real_escape_string($status);
    $sql .= " AND status = '$status'";
}

// Lọc phòng ban
if (!empty($department)) {
    $department = $conn->real_escape_string($department);
    $sql .= " AND department_id = '$department'";
}

// Lọc chức danh
if (!empty($position)) {
    $position = $conn->real_escape_string($position);
    $sql .= " AND position_id = '$position'";
}

$result = $conn->query($sql);

$employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Trả về JSON
echo json_encode([
    "status" => "success",
    "data" => $employees
]);

$conn->close();
?>
