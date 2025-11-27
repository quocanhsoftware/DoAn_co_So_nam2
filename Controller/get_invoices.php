<?php
include '../Model/db.php';
header('Content-Type: application/json; charset=utf-8');

// Lấy tham số từ query string
$time_filter = $_GET['time'] ?? 'this_month'; // this_month, custom
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$invoice_type = $_GET['invoice_type'] ?? ''; // no_delivery, delivery
$status = $_GET['status'] ?? ''; // processing, completed, undeliverable, cancelled
$search = $_GET['search'] ?? ''; // Tìm theo mã hóa đơn

// Xây dựng điều kiện WHERE
$where_conditions = [];

// Lọc theo thời gian
if ($time_filter === 'custom' && $start_date && $end_date) {
    $where_conditions[] = "DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
} else if ($time_filter === 'this_month') {
    $where_conditions[] = "MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
}

// Lọc theo loại hóa đơn
if ($invoice_type && in_array($invoice_type, ['no_delivery', 'delivery'])) {
    $where_conditions[] = "invoice_type = '$invoice_type'";
}

// Lọc theo trạng thái
if ($status && in_array($status, ['processing', 'completed', 'undeliverable', 'cancelled'])) {
    $where_conditions[] = "status = '$status'";
}

// Tìm kiếm theo mã hóa đơn
if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $where_conditions[] = "code LIKE '%$search_escaped%'";
}

// Tạo câu SQL
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Truy vấn danh sách hóa đơn
$sql = "SELECT 
            id,
            code,
            employee_code,
            employee_name,
            total_amount,
            invoice_type,
            status,
            return_code,
            DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
            created_at
        FROM invoices 
        $where_clause
        ORDER BY created_at DESC
        LIMIT 100";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $conn->error]);
    exit;
}

$invoices = [];
while ($row = $result->fetch_assoc()) {
    $invoices[] = [
        'id' => $row['id'],
        'code' => $row['code'],
        'employee_code' => $row['employee_code'],
        'employee_name' => $row['employee_name'],
        'total_amount' => number_format($row['total_amount'], 0, ',', '.'),
        'invoice_type' => $row['invoice_type'],
        'status' => $row['status'],
        'return_code' => $row['return_code'] ?? '',
        'created_at' => $row['created_at_formatted']
    ];
}

// Tính tổng tiền
$total_sql = "SELECT SUM(total_amount) as total FROM invoices $where_clause";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_amount = $total_row['total'] ?? 0;

echo json_encode([
    'success' => true,
    'invoices' => $invoices,
    'total_amount' => number_format($total_amount, 0, ',', '.'),
    'count' => count($invoices)
]);
?>

