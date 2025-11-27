<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn - KiotViet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../donhang.css">
</head>
<body>
<?php 
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['fullname'])) {
    header("Location: ../View/login.php");
    exit;
}
$shop_id = $_GET['shop_id'] ?? $_SESSION['shop_id'];


$user_name = $_SESSION['fullname'] ?? 'User';
 
?>

<!-- Top Header -->
<div class="top-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="logo-brand d-flex align-items-center me-4">
                <div class="brand-dot-small"></div>
                <span class="brand-text">KiotViet</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="icon-btn" title="Giao hàng">
                <i class="bi bi-truck"></i>
            </button>
            <button class="icon-btn" title="Tin nhắn">
                <i class="bi bi-chat-dots"></i>
            </button>
            <div class="dropdown">
                <button class="icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <img src="../picture/vietnamflag.png" alt="VN" style="width: 20px; height: 20px;">
                    <span class="ms-1">Tiếng Việt</span>
                    <i class="bi bi-chevron-down ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Tiếng Việt</a></li>
                    <li><a class="dropdown-item" href="#">English</a></li>
                </ul>
            </div>
            <button class="icon-btn position-relative" title="Thông báo">
                <i class="bi bi-bell"></i>
                <span class="badge-notification">3</span>
            </button>
            <button class="icon-btn" title="Cài đặt">
                <i class="bi bi-gear"></i>
            </button>
            <div class="user-profile">
                <div class="avatar-circle">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="ms-2"><?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Bar -->
<div class="nav-bar">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav-menu d-flex gap-4 mb-0">
                <li><a href="#" class="nav-menu-link">Tổng quan</a></li>
                <li><a href="#" class="nav-menu-link">Hàng hóa</a></li>
                <li><a href="#" class="nav-menu-link active">Đơn hàng</a></li>
                <li><a href="#" class="nav-menu-link">Khách hàng</a></li>
                <li><a href="#" class="nav-menu-link">Nhân viên</a></li>
                <li><a href="#" class="nav-menu-link">Sổ quỹ</a></li>
                <li><a href="#" class="nav-menu-link">Báo cáo</a></li>
                <li><a href="#" class="nav-menu-link">Bán online</a></li>
                <li><a href="#" class="nav-menu-link">Thuế & Kế toán <span class="badge-new-small">Mới</span></a></li>
            </ul>
            <a class="btn-sell" href="../View/sell.php">
    <i class="bi bi-cart-plus me-2"></i>
    Bán hàng
</a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3 sidebar-filter">
                <div class="filter-section">
                    <h6 class="filter-title">Thời gian</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="timeFilter" id="timeThisMonth" value="this_month" checked>
                        <label class="form-check-label" for="timeThisMonth">
                            Tháng này
                            <i class="bi bi-chevron-right float-end"></i>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="timeFilter" id="timeCustom" value="custom">
                        <label class="form-check-label" for="timeCustom">
                            <i class="bi bi-calendar3 me-1"></i>
                            Tùy chỉnh
                        </label>
                    </div>
                    <div id="customDateRange" class="mt-2" style="display: none;">
                        <input type="date" class="form-control form-control-sm mb-2" id="startDate">
                        <input type="date" class="form-control form-control-sm" id="endDate">
                    </div>
                </div>

                <div class="filter-section">
                    <h6 class="filter-title">Loại hóa đơn</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="typeNoDelivery" value="no_delivery">
                        <label class="form-check-label" for="typeNoDelivery">Không giao hàng</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="typeDelivery" value="delivery">
                        <label class="form-check-label" for="typeDelivery">Giao hàng</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h6 class="filter-title">Trạng thái hóa đơn</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="statusProcessing" value="processing">
                        <label class="form-check-label" for="statusProcessing">Đang xử lý</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="statusCompleted" value="completed" checked>
                        <label class="form-check-label" for="statusCompleted">Hoàn thành</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="statusUndeliverable" value="undeliverable">
                        <label class="form-check-label" for="statusUndeliverable">Không giao được</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="statusCancelled" value="cancelled">
                        <label class="form-check-label" for="statusCancelled">Đã hủy</label>
                    </div>
                </div>
                <?php
                    // Lấy danh sách trạng thái từ DB
                    include '../Model/db.php';
                    
                $sqlShop = "SELECT  nameshop FROM users WHERE id = '$shop_id'";
                $resultShop = $conn->query($sqlShop);
                $rowShop = $resultShop->fetch_assoc();
                $shopName = $rowShop['nameshop'];   // ví dụ 'doancoso3'

                $sql = "SELECT id,fullname FROM users WHERE nameshop='$shopName'";
                $result = $conn->query($sql);
                    ?>
                <select name="employee_name" class="form-select">
                    <option value="">Chọn nhân viên</option>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id']; ?>">
                            <?= $row['fullname']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                
            </div>
            

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <div class="page-header mb-4">
                        <h2 class="page-title">Hóa đơn</h2>
                    </div>

                    <!-- Action Bar -->
                    <div class="action-bar mb-3">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="search-box-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" class="form-control search-input" id="searchInvoice" placeholder="Theo mã hóa đơn">
                            </div>
                            <button class="btn-icon" title="Lọc">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <button class="btn-icon" title="Sắp xếp">
                                <i class="bi bi-sort-down"></i>
                            </button>
                            <button class="btn-primary-action">
                                <i class="bi bi-plus-lg me-1"></i>
                                Tạo mới
                            </button>
                            <button class="btn-secondary-action">
                                <i class="bi bi-upload me-1"></i>
                                Import file
                            </button>
                            <div class="dropdown">
                                <button class="btn-secondary-action dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-download me-1"></i>
                                    Xuất file
                                    <i class="bi bi-chevron-down ms-1"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                    <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                                </ul>
                            </div>
                            <button class="btn-icon" title="Thêm">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <button class="btn-icon" title="Lưới">
                                <i class="bi bi-grid-3x3-gap"></i>
                            </button>
                            <button class="btn-icon" title="Cài đặt">
                                <i class="bi bi-gear"></i>
                            </button>
                            <button class="btn-icon" title="Trợ giúp">
                                <i class="bi bi-question-circle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="table-wrapper">
                        <table class="table invoice-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>
                                        <i class="bi bi-star"></i>
                                    </th>
                                    <th>Mã hóa đơn</th>
                                    <th>Thời gian</th>
                                    <th>Mã trả hàng</th>
                                    <th>Mã KH</th>
                                    <th>Khách hàng</th>
                                    <th class="text-end">Tổng tiền hàng</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTableBody">
                                <tr class="summary-row">
                                    <td colspan="7" class="fw-bold">Tổng tiền hàng</td>
                                    <td class="text-end fw-bold total-summary" id="totalSummary">0</td>
                                </tr>
                                <!-- Dữ liệu sẽ được load bằng JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Support Button -->
<div class="support-button">
    <button class="btn-support">
        <i class="bi bi-telephone-fill me-2"></i>
        1900 6522
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../Controller_js/donhang.js"></script>
</body>
</html>

