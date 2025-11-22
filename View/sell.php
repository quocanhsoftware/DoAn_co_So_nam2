<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bán hàng</title>
    <link rel="stylesheet" href="../sale.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php 
session_start();

if (!isset($_SESSION['fullname'])) {
    die("Bạn chưa đăng nhập!");
}

$employee_name = $_SESSION['fullname'];
$employee_phone = $_SESSION['phone'];
?>


    <div class="pos-wrap">
        <div class="pos-topbar">
            <div class="left">
                <div class="search-box">
                    <i class="bi bi-search" style="color: #00aaff;"></i>
                    <input type="text" placeholder="Tìm hàng hóa (F3)">
                </div>
                <div class="invoice-tab">
                    <i class="bi bi-arrow-left-right" style="color: #0d0d0eff;"></i>
                    <span>Hóa đơn 1</span>
                    <button class="tab-plus"><i class="bi bi-plus-lg"></i></button>
                </div>
            </div>
            <div class="right-tools">
                <button class="icon-btn"><i class="bi bi-bag"></i></button>
                <button class="icon-btn"><i class="bi bi-arrow-clockwise"></i></button>
                <div class="user">
                    <span><?php echo htmlspecialchars($employee_phone); ?></span>
                </div>
                <button class="icon-btn"><i class="bi bi-list"></i></button>
            </div>
        </div>

        <div class="pos-main">
            <div class="items-area">
            </div>
            <div class="bill-area">
                <div class="employee-bar">
                   <div class="employee-select">
                        <input type="text" name="employee" value="<?php echo htmlspecialchars($employee_name); ?>" readonly>

                        <button class="dropdown"><i class="bi bi-chevron-down"></i></button>
                    </div>

                    <div class="date-time" id="dateTime"></div>
                </div>
                <div class="employee-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Tìm nhân viên (F4)">
                    <button class="add"><i class="bi bi-plus-lg"></i></button>
                </div>

                <div class="summary">
                    <div class="row">
                        <span>Tổng tiền hàng</span>
                        <span class="value">0</span>
                    </div>
                    <div class="row">
                        <span>Giảm giá</span>
                        <span class="value">0</span>
                    </div>
                    <div class="row highlight">
                        <span>Khách cần trả</span>
                        <span class="value accent">0</span>
                    </div>
                    <div class="row">
                        <span>Tiền thừa trả khách</span>
                        <span class="value">0</span>
                    </div>
                </div>

                <div class="bank-card">
                    <div class="note">Bạn chưa có tài khoản ngân hàng</div>
                    <button class="link">+ Thêm tài khoản</button>
                </div>

                <button class="pay-btn">THANH TOÁN</button>
            </div>
        </div>

        <div class="pos-bottom">
            <div class="note-box">
                <i class="bi bi-pencil"></i>
                <input type="text" placeholder="Ghi chú đơn hàng">
            </div>
            <div class="sale-modes">
                <button class="mode active" data-mode="fast">Bán nhanh</button>
                <button class="mode" data-mode="normal">Bán thường</button>
                <button class="mode" data-mode="delivery">Bán giao hàng</button>
            </div>
            <div class="support">
                <span class="phone">1900 6522</span>
                <button class="chat"><i class="bi bi-chat-dots"></i></button>
            </div>
        </div>
    </div>

    <script src="../Controller_js/sale.js"></script>
    <script src="../Controller_js/Get_Time.js"></script>
</body>
</html>

