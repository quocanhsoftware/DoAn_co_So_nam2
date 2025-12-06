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

<?php include '../Controller/sell_backend.php'; ?>

    <div class="pos-wrap">
        <div class="pos-topbar">
            <div class="left">
                <form class="search-box" method="get">
                    <i class="bi bi-search" style="color: #00aaff;"></i>
                    <input type="text" name="q" placeholder="Tìm hàng hóa (F3)" value="<?php echo htmlspecialchars($q); ?>">
                </form>
                <?php if ($q !== '' && count($products) > 0) { ?>
                <div class="search-suggest">
                    <?php foreach (array_slice($products,0,6) as $p) { ?>
                        <div class="suggest-item">
                            <div class="si-left">
                                <?php if (!empty($p['image'])) { ?><img src="<?php echo htmlspecialchars($p['image']); ?>" class="si-thumb" alt=""><?php } ?>
                                <div>
                                    <div class="si-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div class="si-meta"><?php echo htmlspecialchars($p['product_code']); ?> · Tồn: <?php echo intval($p['stock']); ?></div>
                                </div>
                            </div>
                            <div class="si-right">
                                <div class="si-price"><?php echo vnd($p['sale_price']); ?></div>
                                <form method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo intval($p['id']); ?>">
                                    <input type="number" name="qty" value="1" min="1" class="si-qty">
                                    <button type="submit" class="si-add">Chọn</button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="suggest-more">+ Thêm mới hàng hóa</div>
                </div>
                <?php } ?>
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
                <div class="cart-list">
                    <?php foreach ($_SESSION['cart'] as $it) { ?>
                        <div class="cart-row">
                            <div class="cart-left">
                                <?php if (!empty($it['image'])) { ?><img src="<?php echo htmlspecialchars($it['image']); ?>" class="cart-thumb" alt=""><?php } ?>
                                <div>
                                    <div class="cart-name"><?php echo htmlspecialchars($it['name']); ?></div>
                                    <div class="cart-code"><?php echo htmlspecialchars($it['code']); ?></div>
                                </div>
                            </div>
                            <div class="cart-mid">
                                <form method="post" class="cart-qty">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo intval($it['id']); ?>">
                                    <input type="number" name="qty" value="<?php echo intval($it['qty']); ?>" min="0">
                                    <button type="submit" class="cart-btn">Cập nhật</button>
                                </form>
                                <form method="post" class="cart-remove">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo intval($it['id']); ?>">
                                    <button type="submit" class="cart-btn danger">Xóa</button>
                                </form>
                            </div>
                            <div class="cart-right">
                                <div class="cart-price"><?php echo vnd($it['price']); ?></div>
                                <div class="cart-sub"><?php echo vnd($it['price']*(int)$it['qty']); ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
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

                <?php $total_qty=0; $total_amount=0.0; foreach($_SESSION['cart'] as $it){ $total_qty += (int)$it['qty']; $total_amount += (float)$it['price']*(int)$it['qty']; } ?>
                

                <form method="post" class="summary">
                    <div class="row">
                        <span>Tổng tiền hàng</span>
                        <span class="value"><?php echo vnd($total_amount); ?></span>
                    </div>
                    <div class="row">
                        <span>Giảm giá</span>
                        <span class="value"><input type="number" name="discount" step="0.01" value="0" class="discount-input"></span>
                    </div>
                    <div class="row">
                        <span>Phương thức thanh toán</span>
                        <span class="value">
                            <select name="payment_method" class="form-select form-select-sm">
                                <option value="cash">Tiền mặt</option>
                                <option value="bank">Chuyển khoản</option>
                                <option value="card">Thẻ</option>
                            </select>
                        </span>
                    </div>
                    <input type="hidden" name="action" value="checkout">
                    <div class="bank-card">
                    <div class="note">Thanh toán: tiền mặt/thẻ/chuyển khoản</div>
                    <button class="link" disabled>+ Thêm tài khoản</button>
                </div>

                <button class="pay-btn">THANH TOÁN</button>
                </form>
                <?php if (!empty($checkout_msg)) { ?><div class="alert-info" style="margin:10px 0;"><?php echo htmlspecialchars($checkout_msg); ?></div><?php } ?>

               
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
