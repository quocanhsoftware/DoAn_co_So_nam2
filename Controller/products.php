<?php
// =========================
// 1. Kết nối cơ sở dữ liệu
// =========================
include '../Model/db.php';

// =========================
// 2. Khởi tạo biến
// =========================
$msg = '';              // Thông báo trạng thái (thêm/sửa/xóa)
$product_table = 'products'; // Bảng sản phẩm mặc định
$q = '';                // Từ khóa tìm kiếm
$editing = null;        // Dữ liệu sản phẩm đang chỉnh sửa

// =========================
// 3. Tự động phát hiện bảng sản phẩm trong DB
// =========================
$res = $conn->query("SELECT table_name, COUNT(*) AS cnt 
                     FROM information_schema.columns 
                     WHERE table_schema = DATABASE() 
                       AND column_name IN ('product_code','name','image','cost_price','sale_price','stock','updated_at') 
                     GROUP BY table_name 
                     ORDER BY cnt DESC LIMIT 1");

if ($res && ($row = $res->fetch_assoc()) && intval($row['cnt']) >= 7) { 
    $product_table = $row['table_name']; 
}

// Kiểm tra tên bảng hợp lệ để tránh SQL Injection
if (!preg_match('/^[A-Za-z0-9_]+$/', $product_table)) { 
    die('Tên bảng không hợp lệ'); 
}

// =========================
// 4. Xử lý POST (thêm, sửa, xóa sản phẩm)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---------- 4a. Thêm sản phẩm mới ----------
    if ($action === 'create') {
        // Lấy mã sản phẩm lớn nhất hiện tại
        $res = $conn->query("SELECT product_code FROM `$product_table` WHERE product_code LIKE 'SP%' ORDER BY id DESC LIMIT 1");
        $last_code = '';
        if ($res && ($row = $res->fetch_assoc())) {
            $last_code = $row['product_code']; // ví dụ 'SP007'
        }

        // Tạo mã mới: SP001, SP002, ...
        if ($last_code) {
            $num = intval(substr($last_code, 2)) + 1; // lấy phần số +1
        } else {
            $num = 1; // nếu chưa có SP nào
        }
        $product_code = 'SP' . str_pad($num, 3, '0', STR_PAD_LEFT);

        // Lấy dữ liệu từ form
        $name = trim($_POST['name'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $cost_price = (float)($_POST['cost_price'] ?? 0);
        $sale_price = (float)($_POST['sale_price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);

        // Thêm vào DB
        if ($stmt = $conn->prepare("INSERT INTO `$product_table` (product_code,name,image,cost_price,sale_price,stock,updated_at) VALUES (?,?,?,?,?,?,NOW())")) {
            $stmt->bind_param('sssddi', $product_code, $name, $image, $cost_price, $sale_price, $stock);
            $ok = $stmt->execute();
            $stmt->close();
            $msg = $ok ? "Thêm sản phẩm thành công: $product_code" : 'Thêm sản phẩm thất bại';
        }

    // ---------- 4b. Cập nhật sản phẩm ----------
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $product_code = trim($_POST['product_code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $cost_price = (float)($_POST['cost_price'] ?? 0);
        $sale_price = (float)($_POST['sale_price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);

        if ($stmt = $conn->prepare("UPDATE `$product_table` SET product_code=?, name=?, image=?, cost_price=?, sale_price=?, stock=?, updated_at=NOW() WHERE id=?")) {
            $stmt->bind_param('sssddii', $product_code, $name, $image, $cost_price, $sale_price, $stock, $id);
            $ok = $stmt->execute();
            $stmt->close();
            $msg = $ok ? 'Cập nhật sản phẩm thành công' : 'Cập nhật sản phẩm thất bại';
            if ($ok) {
                // Chuyển về trang quản lý sau khi sửa xong
                header("Location: quanly_sanpham.php");
                exit;
            }
        }

    // ---------- 4c. Xóa sản phẩm ----------
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($stmt = $conn->prepare("DELETE FROM `$product_table` WHERE id=?")) {
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            $msg = $ok ? 'Xóa sản phẩm thành công' : 'Xóa sản phẩm thất bại';
        }
    }
}

// =========================
// 5. Lấy dữ liệu sản phẩm để sửa
// =========================
if (isset($_GET['edit_id'])) {
    $eid = intval($_GET['edit_id']);
    if ($stmt = $conn->prepare("SELECT id, product_code, name, image, cost_price, sale_price, stock FROM `$product_table` WHERE id=?")) {
        $stmt->bind_param('i', $eid);
        if ($stmt->execute()) { 
            $rs = $stmt->get_result(); 
            $editing = $rs->fetch_assoc(); // dữ liệu sản phẩm đang sửa
        }
        $stmt->close();
    }
}

// =========================
// 6. Tìm kiếm / hiển thị sản phẩm
// =========================
$q = trim($_GET['q'] ?? '');
$products = [];

if ($q !== '') {
    // Tìm kiếm theo tên hoặc mã sản phẩm
    $like = "%$q%";
    if ($stmt = $conn->prepare("SELECT id, product_code, name, image, cost_price, sale_price, stock, updated_at 
                                FROM `$product_table` 
                                WHERE name LIKE ? OR product_code LIKE ? 
                                ORDER BY updated_at DESC, id DESC")) {
        $stmt->bind_param('ss', $like, $like);
        if ($stmt->execute()) { 
            $rs = $stmt->get_result(); 
            while ($r = $rs->fetch_assoc()) { 
                $products[] = $r; 
            } 
        }
        $stmt->close();
    }
} else {
    // Nếu không tìm kiếm, lấy tất cả sản phẩm mới nhất
    $res = $conn->query("SELECT id, product_code, name, image, cost_price, sale_price, stock, updated_at 
                         FROM `$product_table` 
                         ORDER BY updated_at DESC, id DESC");
    if ($res) { 
        while ($r = $res->fetch_assoc()) { 
            $products[] = $r; 
        } 
    }
}

// =========================
// 7. Hàm định dạng tiền VND
// =========================
function currency_vnd($n){ 
    return number_format((float)$n,0,',','.'); 
}
?>
