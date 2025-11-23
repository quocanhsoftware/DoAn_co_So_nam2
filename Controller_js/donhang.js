// Load dữ liệu hóa đơn khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    loadInvoices();
    
    // Xử lý tìm kiếm
    document.getElementById('searchInvoice').addEventListener('input', debounce(loadInvoices, 500));
    
    // Xử lý filter thời gian
    document.querySelectorAll('input[name="timeFilter"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                document.getElementById('customDateRange').style.display = 'block';
            } else {
                document.getElementById('customDateRange').style.display = 'none';
            }
            loadInvoices();
        });
    });
    
    // Xử lý filter loại hóa đơn
    document.getElementById('typeNoDelivery').addEventListener('change', loadInvoices);
    document.getElementById('typeDelivery').addEventListener('change', loadInvoices);
    
    // Xử lý filter trạng thái
    document.querySelectorAll('input[id^="status"]').forEach(checkbox => {
        checkbox.addEventListener('change', loadInvoices);
    });
    
    // Xử lý custom date range
    document.getElementById('startDate').addEventListener('change', loadInvoices);
    document.getElementById('endDate').addEventListener('change', loadInvoices);
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:not(#selectAll)');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});

// Hàm load danh sách hóa đơn
function loadInvoices() {
    const params = new URLSearchParams();
    
    // Thời gian
    const timeFilter = document.querySelector('input[name="timeFilter"]:checked').value;
    params.append('time', timeFilter);
    
    if (timeFilter === 'custom') {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
    }
    
    // Loại hóa đơn
    const invoiceTypes = [];
    if (document.getElementById('typeNoDelivery').checked) {
        invoiceTypes.push('no_delivery');
    }
    if (document.getElementById('typeDelivery').checked) {
        invoiceTypes.push('delivery');
    }
    if (invoiceTypes.length === 1) {
        params.append('invoice_type', invoiceTypes[0]);
    }
    
    // Trạng thái
    const statuses = [];
    document.querySelectorAll('input[id^="status"]:checked').forEach(checkbox => {
        statuses.push(checkbox.value);
    });
    if (statuses.length === 1) {
        params.append('status', statuses[0]);
    } else if (statuses.length > 1) {
        // Nếu có nhiều trạng thái được chọn, không gửi filter này
        // Hoặc có thể gửi tất cả các status được chọn
    }
    
    // Tìm kiếm
    const search = document.getElementById('searchInvoice').value.trim();
    if (search) {
        params.append('search', search);
    }
    
    // Gọi API
    fetch('../Controller/get_invoices.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderInvoices(data.invoices);
                updateTotalSummary(data.total_amount);
            } else {
                console.error('Lỗi:', data.message);
                renderInvoices([]);
                updateTotalSummary('0');
            }
        })
        .catch(error => {
            console.error('Lỗi kết nối:', error);
            renderInvoices([]);
            updateTotalSummary('0');
        });
}

// Hàm render danh sách hóa đơn
function renderInvoices(invoices) {
    const tbody = document.getElementById('invoiceTableBody');
    
    // Xóa các dòng cũ (trừ dòng tổng)
    const summaryRow = tbody.querySelector('.summary-row');
    tbody.innerHTML = '';
    if (summaryRow) {
        tbody.appendChild(summaryRow);
    }
    
    if (invoices.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="8" class="text-center text-muted py-4">Không có dữ liệu</td>';
        tbody.insertBefore(row, summaryRow);
        return;
    }
    
    // Render từng hóa đơn
    invoices.forEach(invoice => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input" data-invoice-id="${invoice.id}">
            </td>
            <td>
                <i class="bi bi-star" style="color: #ddd; cursor: pointer;"></i>
            </td>
            <td>${invoice.code}</td>
            <td>${invoice.created_at}</td>
            <td>${invoice.return_code || ''}</td>
            <td>${invoice.customer_code}</td>
            <td>${invoice.customer_name}</td>
            <td class="text-end">${invoice.total_amount} đ</td>
        `;
        tbody.insertBefore(row, summaryRow);
    });
}

// Hàm cập nhật tổng tiền
function updateTotalSummary(total) {
    const totalElement = document.getElementById('totalSummary');
    if (totalElement) {
        totalElement.textContent = total === '0' ? '0' : total + ' đ';
    }
}

// Hàm debounce để tránh gọi API quá nhiều lần
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

