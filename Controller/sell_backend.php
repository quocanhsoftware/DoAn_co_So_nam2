<?php  
session_start();  

// =========================
// 1. Kiểm tra đăng nhập
// =========================
if (!isset($_SESSION['fullname'])) {     
    die("Bạn chưa đăng nhập!"); 
}  
$employee_name = $_SESSION['fullname']; 
$employee_phone = $_SESSION['phone'];

// =========================
// 2. Kết nối cơ sở dữ liệu
// =========================
include '../Model/db.php';  

// =========================
// 3. Hàm định dạng tiền VND
// =========================
function vnd($n){
    return number_format((float)$n,0,',','.');
}

// =========================
// 4. Khởi tạo giỏ hàng nếu chưa có
// =========================
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// =========================
// 5. Hàm tự động phát hiện bảng sản phẩm
// =========================
function detectProductTable($conn){
    $tbl = 'products'; // mặc định là bảng 'products'
    $sql = "SELECT table_name, COUNT(*) AS cnt 
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() 
              AND column_name IN ('product_code','name','image','cost_price','sale_price','stock','updated_at') 
            GROUP BY table_name ORDER BY cnt DESC LIMIT 1";
    if ($res = $conn->query($sql)) {
        if ($row = $res->fetch_assoc()) {
            // Nếu bảng có >= 5 cột chuẩn, dùng bảng đó
            if (intval($row['cnt']) >= 5) $tbl = $row['table_name'];
        }
    }
    return $tbl;
}

$PRODUCT_TABLE = detectProductTable($conn);
$clearSearch = false;

// =========================
// 6. Xử lý các hành động POST
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---------- Thêm sản phẩm vào giỏ ----------
    if ($action === 'add') {
        $pid = intval($_POST['product_id'] ?? 0);
        $qty = max(1, intval($_POST['qty'] ?? 1));

        if ($stmt = $conn->prepare("SELECT id, product_code, name, sale_price, image FROM `$PRODUCT_TABLE` WHERE id=?")) {
            $stmt->bind_param('i', $pid);
            if ($stmt->execute()) {
                $rs = $stmt->get_result();
                if ($p = $rs->fetch_assoc()) {
                    // Nếu chưa có trong giỏ thì tạo mới
                    if (!isset($_SESSION['cart'][$pid])) {
                        $_SESSION['cart'][$pid] = [
                            'id' => $pid,
                            'code' => $p['product_code'],
                            'name' => $p['name'],
                            'price' => (float)$p['sale_price'],
                            'qty' => 0,
                            'image' => $p['image']
                        ];
                    }
                    // Cộng dồn số lượng
                    $_SESSION['cart'][$pid]['qty'] += $qty;
                    $clearSearch = true;
                }
            }
            $stmt->close();
        }

    // ---------- Cập nhật số lượng ----------
    } elseif ($action === 'update') {
        $pid = intval($_POST['product_id'] ?? 0);
        $qty = max(0, intval($_POST['qty'] ?? 0));

        if (isset($_SESSION['cart'][$pid])) {
            if ($qty === 0) {
                unset($_SESSION['cart'][$pid]); // xóa sản phẩm nếu qty=0
            } else {
                $_SESSION['cart'][$pid]['qty'] = $qty; // cập nhật số lượng
            }
        }

    // ---------- Xóa sản phẩm ----------
    } elseif ($action === 'remove') {
        $pid = intval($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$pid]);

    // ---------- Thanh toán / tạo đơn ----------
    } elseif ($action === 'checkout') {
        $discount = (float)($_POST['discount'] ?? 0);
        $payment_method = trim($_POST['payment_method'] ?? '');

        $total_qty = 0; 
        $total_amount = 0.0;

        // Tính tổng số lượng và tổng tiền
        foreach ($_SESSION['cart'] as $it) {
            $total_qty += (int)$it['qty'];
            $total_amount += (float)$it['price'] * (int)$it['qty'];
        }

        $final_amount = max(0, $total_amount - $discount); // áp dụng giảm giá
        $order_code = 'OD'.date('ymdHis').substr(strval(mt_rand()),0,3); // mã đơn tự động

        // Thêm đơn vào DB
        if ($stmt = $conn->prepare("INSERT INTO orders (order_code,total_quantity,total_amount,discount,final_amount,payment_method,created_at) VALUES (?,?,?,?,?,?,NOW())")) {
            $stmt->bind_param('siddds', $order_code, $total_qty, $total_amount, $discount, $final_amount, $payment_method);
            if ($stmt->execute()) {
                $_SESSION['cart'] = []; // xóa giỏ sau khi tạo đơn
                $checkout_msg = "Tạo đơn thành công: $order_code";
            } else {
                $checkout_msg = 'Tạo đơn thất bại';
            }
            $stmt->close();
        }
        $clearSearch = true;
    }
}

// =========================
// 7. Tìm kiếm sản phẩm
// =========================
$q = trim($_GET['q'] ?? '');
if ($clearSearch) $q = '';
$products = [];

if ($q !== '') {
    // Tìm kiếm theo tên hoặc mã sản phẩm
    $like = "%$q%";
    if ($stmt = $conn->prepare("SELECT id,product_code,name,image,sale_price,stock FROM `$PRODUCT_TABLE` WHERE name LIKE ? OR product_code LIKE ? ORDER BY updated_at DESC, id DESC")) {
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
    // Nếu không tìm kiếm, lấy 40 sản phẩm mới nhất
    $res = $conn->query("SELECT id,product_code,name,image,sale_price,stock FROM `$PRODUCT_TABLE` ORDER BY updated_at DESC, id DESC LIMIT 40");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $products[] = $r;
        }
    }
}
?>
