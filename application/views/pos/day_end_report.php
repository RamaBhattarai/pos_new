<div class="content-body">
    <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line text-primary me-2"></i> Day End Report</h4>
                    <p class="text-muted small mb-0">Monitor daily sales and payment transactions</p>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content">
                    <div id="notify" class="alert alert-success" style="display:none;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <div class="message"></div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Stats -->
                        <div class="row mb-4 g-3">
                            <?php 
                            $colors = [
                                'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                                'linear-gradient(135deg, #007bff 0%, #6610f2 100%)',
                                'linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%)',
                                'linear-gradient(135deg, #ffc107 0%, #fd7e14 100%)',
                                'linear-gradient(135deg, #6c757d 0%, #495057 100%)',
                                'linear-gradient(135deg, #dc3545 0%, #e83e8c 100%)',
                                'linear-gradient(135deg, #20c997 0%, #17a2b8 100%)',
                                'linear-gradient(135deg, #fd7e14 0%, #dc3545 100%)'
                            ];
                            $icons = [
                                'fas fa-money-bill-wave',
                                'fas fa-university',
                                'fas fa-mobile-alt',
                                'fas fa-wallet',
                                'fas fa-truck',
                                'fas fa-credit-card',
                                'fas fa-handshake',
                                'fas fa-coins'
                            ];
                            $color_index = 0;
                            foreach ($payment_methods as $method): 
                                $method_key = strtolower($method);
                                $amount = isset($totals[$method_key]) ? $totals[$method_key] : 0;
                                $color = $colors[$color_index % count($colors)];
                                $icon = $icons[$color_index % count($icons)];
                                $color_index++;
                            ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 mb-2">
                                <div class="card border-0 shadow-sm h-100" style="background: <?php echo $color; ?>;">
                                    <div class="card-body p-3 text-center text-white">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title text-uppercase fw-bold mb-1 small"><?php echo htmlspecialchars($method); ?></h6>
                                                <h4 class="fw-bold mb-0"><?php echo amountFormat($amount); ?></h4>
                                            </div>
                                            <div class="ms-2">
                                                <i class="<?php echo $icon; ?> fa-lg opacity-75"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 mb-2">
                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                                    <div class="card-body p-3 text-center text-white">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title text-uppercase fw-bold mb-1 small">Total</h6>
                                                <h4 class="fw-bold mb-0"><?php echo amountFormat($totals['grand_total']); ?></h4>
                                            </div>
                                            <div class="ms-2">
                                                <i class="fas fa-calculator fa-lg opacity-75"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="card card-block">
                            <?php if (!isset($selected_payment_method)) { $selected_payment_method = ''; } ?>
                            <form method="get" action="<?php echo base_url('pos_invoices/day_end_report'); ?>" class="form-horizontal">
                                <div class="grid_3 grid_4">
                                    <h6><?php echo $this->lang->line('Custom Range') ?></h6>
                                    <hr>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"
                                               for="payment_method"><?php echo $this->lang->line('Payment Method') ?></label>
                                        <div class="col-sm-6">
                                            <select name="payment_method" id="payment_method" class="form-control">
                                                <option value="" <?php echo ($selected_payment_method === '' || $selected_payment_method === null) ? 'selected' : ''; ?>><?php echo $this->lang->line('All') ?></option>
                                                <?php foreach ($payment_methods as $method): ?>
                                                    <option value="<?php echo $method; ?>" <?php echo ($selected_payment_method == $method) ? 'selected' : ''; ?>>
                                                        <?php echo $method; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
    <label class="col-sm-3 col-form-label" for="warehouse_id">Warehouse</label>
    <div class="col-sm-6">
        <select name="warehouse_id" id="warehouse_id" class="form-control">
            <option value="all" <?php echo (empty($selected_warehouse) || $selected_warehouse == 'all') ? 'selected' : ''; ?>>All</option>
            <?php foreach ($warehouses as $wh): ?>
                <option value="<?php echo $wh['id']; ?>" <?php echo ($selected_warehouse == $wh['id']) ? 'selected' : ''; ?>>
                    <?php echo $wh['title']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label"
                                               for="start_date"><?php echo $this->lang->line('From Date') ?></label>
                                        <div class="col-sm-4">
                                            <input type="date" class="form-control required"
                                                   name="start_date" id="start_date"
                                                   value="<?php echo $start_date; ?>" max="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label"
                                               for="end_date"><?php echo $this->lang->line('To Date') ?></label>
                                        <div class="col-sm-4">
                                            <input type="date" class="form-control required"
                                                   name="end_date" id="end_date"
                                                   value="<?php echo $end_date; ?>" max="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"></label>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-success d-inline-flex align-items-center  fs-6">
                                                <i class="fa fa-search fa-lg me-2" aria-hidden="true"></i>
                                                <span>Search</span>
                                            </button>
                                            <a href="<?php echo base_url('pos_invoices/day_end_report'); ?>" class="btn btn-outline-secondary d-inline-flex align-items-center fs-6 ms-3">
                                                <i class="fa fa-redo fa-lg me-2" aria-hidden="true"></i>
                                                <span>Reset</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-table text-primary me-2"></i> Transaction Details
                    </h5>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body p-0">
                        <?php if (!empty($report_data)): ?>
                            <!-- Pagination Controls -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <label for="entries_per_page" class="me-2 mb-0">Show:</label>
                                    <select id="entries_per_page" class="form-select form-select-sm" style="width: auto;">
                                        <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                                        <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                                        <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                                        <option value="100" <?php echo ($limit == 100) ? 'selected' : ''; ?>>100</option>
                                    </select>
                                    <span class="ms-2 text-muted">entries</span>
                                </div>
                                <div class="text-muted">
                                    Showing <?php echo ($total_records > 0) ? (($current_page - 1) * $limit + 1) : 0; ?> to <?php echo min($current_page * $limit, $total_records); ?> of <?php echo $total_records; ?> entries
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size: 1rem;">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="fw-bold text-muted px-4 py-3 border-0" style="width: 5%;">#</th>
                                            <th class="fw-bold text-muted px-4 py-3 border-0" style="width: 12%;">
                                                <i class="fas fa-file-invoice me-1"></i> Invoice
                                            </th>
                                            <th class="fw-bold text-muted px-4 py-3 border-0" style="width: 25%;">
                                                <i class="fas fa-user me-1"></i> Customer
                                            </th>
                                            <th class="fw-bold text-muted px-4 py-3 border-0" style="width: 20%;">
                                                <i class="fas fa-clock me-1"></i> Date
                                            </th>
                                            <th class="fw-bold text-muted px-4 py-3 border-0" style="width: 9%;">
                                                <i class="fas fa-credit-card me-1"></i> Payment Method
                                            </th>
                                            <th class="fw-bold text-muted px-4 py-3 border-0 text-end" style="width: 20%;">
                                                <i class="fas fa-dollar-sign me-1"></i> Amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $counter = ($current_page - 1) * $limit + 1; ?>
                                        <?php foreach ($report_data as $row): ?>
                                            <tr class="border-bottom border-light">
                                                <td class="px-4 py-3 text-center fw-semibold text-muted"><?php echo $counter++; ?></td>
                                                <td class="px-4 py-3">
                                                    <a href="<?php echo base_url('pos_invoices/view?id=' . $row['id']); ?>" class="text-primary text-decoration-none fw-semibold">
                                                        <i class="fas fa-external-link-alt me-1"></i> <?php echo $row['tid']; ?>
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <i class="fas fa-user-circle me-2 text-primary"></i>
                                                    <?php echo $row['customer_name'] ?: '<span class="text-muted fst-italic">Walk-in Customer</span>'; ?>
                                                </td>
                                                <td class="px-4 py-3 text-muted">
                                                    <i class="fas fa-calendar-day me-1"></i> <?php echo date('M d, Y', strtotime($row['invoicedate'])); ?>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="fs-6 text-dark fw-semibold">
                                                            <i class="fas fa-<?php
                                                                switch(strtolower($row['pmethod'])) {
                                                                    case 'cash': echo 'money-bill-wave'; break;
                                                                    case 'bank': echo 'university'; break;
                                                                    case 'e-sewa': echo 'mobile-alt'; break;
                                                                    case 'khalti': echo 'wallet'; break;
                                                                    case 'courier': echo 'truck'; break;
                                                                    default: echo 'credit-card'; break;
                                                                }
                                                            ?> me-1"></i>
                                                            <?php echo $row['pmethod']; ?>
                                                        </span>
                                                </td>
                                                <td class="px-4 py-3 text-end fw-bold text-success fs-5">
                                                    <i class="fas fa-dollar-sign me-1 text-muted"></i><?php echo amountFormat($row['total']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination and Summary Footer -->
                            <div class="card-footer bg-light border-top py-3">
                                <div class="row align-items-center">
                                    <!-- Left side: Entries info and pagination -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-muted small">
                                                <i class="fas fa-list-ol me-1"></i>
                                                Showing <?php echo ($total_records > 0) ? (($current_page - 1) * $limit + 1) : 0; ?> to <?php echo min($current_page * $limit, $total_records); ?> of <?php echo $total_records; ?> entries
                                            </div>
                                            <?php if ($total_pages > 1): ?>
                                            <nav aria-label="Transaction pagination" class="ms-4 d-flex justify-content-center">
                                                <ul class="pagination pagination-sm mb-0 small-pagination">
                                                    <!-- Previous Button -->
                                                    <?php if ($current_page > 1): ?>
                                                        <li class="page-item">
                                                            <a class="page-link px-2 py-1 small" href="<?php echo $this->input->get('payment_method') || $this->input->get('start_date') || $this->input->get('end_date') || $this->input->get('warehouse_id') ?
                                                                base_url('pos_invoices/day_end_report?page=' . ($current_page - 1) . '&limit=' . $limit .
                                                                ($this->input->get('payment_method') ? '&payment_method=' . $this->input->get('payment_method') : '') .
                                                                ($this->input->get('start_date') ? '&start_date=' . $this->input->get('start_date') : '') .
                                                                ($this->input->get('end_date') ? '&end_date=' . $this->input->get('end_date') : '') .
                                                                ($this->input->get('warehouse_id') ? '&warehouse_id=' . $this->input->get('warehouse_id') : '')) :
                                                                base_url('pos_invoices/day_end_report?page=' . ($current_page - 1) . '&limit=' . $limit); ?>">
                                                                <i class="fas fa-chevron-left"></i>
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li class="page-item disabled">
                                                            <span class="page-link px-2 py-1 small"><i class="fas fa-chevron-left"></i></span>
                                                        </li>
                                                    <?php endif; ?>

                                                    <!-- Page Numbers -->
                                                    <?php
                                                    $start_page = max(1, $current_page - 1);
                                                    $end_page = min($total_pages, $current_page + 1);

                                                    if ($start_page > 1): ?>
                                                        <li class="page-item">
                                                            <a class="page-link px-2 py-1 small" href="<?php echo $this->input->get('payment_method') || $this->input->get('start_date') || $this->input->get('end_date') || $this->input->get('warehouse_id') ?
                                                                base_url('pos_invoices/day_end_report?page=1&limit=' . $limit .
                                                                ($this->input->get('payment_method') ? '&payment_method=' . $this->input->get('payment_method') : '') .
                                                                ($this->input->get('start_date') ? '&start_date=' . $this->input->get('start_date') : '') .
                                                                ($this->input->get('end_date') ? '&end_date=' . $this->input->get('end_date') : '') .
                                                                ($this->input->get('warehouse_id') ? '&warehouse_id=' . $this->input->get('warehouse_id') : '')) :
                                                                base_url('pos_invoices/day_end_report?page=1&limit=' . $limit); ?>">1</a>
                                                        </li>
                                                        <?php if ($start_page > 2): ?>
                                                            <li class="page-item disabled">
                                                                <span class="page-link px-2 py-1 small">...</span>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                            <a class="page-link px-2 py-1 small" href="<?php echo $this->input->get('payment_method') || $this->input->get('start_date') || $this->input->get('end_date') || $this->input->get('warehouse_id') ?
                                                                base_url('pos_invoices/day_end_report?page=' . $i . '&limit=' . $limit .
                                                                ($this->input->get('payment_method') ? '&payment_method=' . $this->input->get('payment_method') : '') .
                                                                ($this->input->get('start_date') ? '&start_date=' . $this->input->get('start_date') : '') .
                                                                ($this->input->get('end_date') ? '&end_date=' . $this->input->get('end_date') : '') .
                                                                ($this->input->get('warehouse_id') ? '&warehouse_id=' . $this->input->get('warehouse_id') : '')) :
                                                                base_url('pos_invoices/day_end_report?page=' . $i . '&limit=' . $limit); ?>"><?php echo $i; ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <?php if ($end_page < $total_pages): ?>
                                                        <?php if ($end_page < $total_pages - 1): ?>
                                                            <li class="page-item disabled">
                                                                <span class="page-link px-2 py-1 small">...</span>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li class="page-item">
                                                            <a class="page-link px-2 py-1 small" href="<?php echo $this->input->get('payment_method') || $this->input->get('start_date') || $this->input->get('end_date') || $this->input->get('warehouse_id') ?
                                                                base_url('pos_invoices/day_end_report?page=' . $total_pages . '&limit=' . $limit .
                                                                ($this->input->get('payment_method') ? '&payment_method=' . $this->input->get('payment_method') : '') .
                                                                ($this->input->get('start_date') ? '&start_date=' . $this->input->get('start_date') : '') .
                                                                ($this->input->get('end_date') ? '&end_date=' . $this->input->get('end_date') : '') .
                                                                ($this->input->get('warehouse_id') ? '&warehouse_id=' . $this->input->get('warehouse_id') : '')) :
                                                                base_url('pos_invoices/day_end_report?page=' . $total_pages . '&limit=' . $limit); ?>"><?php echo $total_pages; ?></a>
                                                        </li>
                                                    <?php endif; ?>

                                                    <!-- Next Button -->
                                                    <?php if ($current_page < $total_pages): ?>
                                                        <li class="page-item">
                                                            <a class="page-link px-2 py-1 small" href="<?php echo $this->input->get('payment_method') || $this->input->get('start_date') || $this->input->get('end_date') || $this->input->get('warehouse_id') ?
                                                                base_url('pos_invoices/day_end_report?page=' . ($current_page + 1) . '&limit=' . $limit .
                                                                ($this->input->get('payment_method') ? '&payment_method=' . $this->input->get('payment_method') : '') .
                                                                ($this->input->get('start_date') ? '&start_date=' . $this->input->get('start_date') : '') .
                                                                ($this->input->get('end_date') ? '&end_date=' . $this->input->get('end_date') : '') .
                                                                ($this->input->get('warehouse_id') ? '&warehouse_id=' . $this->input->get('warehouse_id') : '')) :
                                                                base_url('pos_invoices/day_end_report?page=' . ($current_page + 1) . '&limit=' . $limit); ?>">
                                                                <i class="fas fa-chevron-right"></i>
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li class="page-item disabled">
                                                            <span class="page-link px-2 py-1 small"><i class="fas fa-chevron-right"></i></span>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </nav>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Right side: Grand Total -->
                                    <div class="col-md-6 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <a href="<?php echo base_url('export/day_end_report_pdf?payment_method=' . urlencode($selected_payment_method) . '&start_date=' . $start_date . '&end_date=' . $end_date . '&warehouse_id=' . urlencode($selected_warehouse)); ?>" 
                                               class="btn btn-danger btn-sm me-2" title="Export to PDF">
                                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                                            </a>
                                            <a href="<?php echo base_url('export/day_end_report_excel?payment_method=' . urlencode($selected_payment_method) . '&start_date=' . $start_date . '&end_date=' . $end_date . '&warehouse_id=' . urlencode($selected_warehouse)); ?>" 
                                               class="btn btn-success btn-sm me-3" title="Export to Excel">
                                                <i class="fas fa-file-excel me-1"></i> Export Excel
                                            </a>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted me-2">
                                                    <i class="fas fa-calculator me-1"></i>
                                                    Grand Total:
                                                </span>
                                                <span class="fw-bold fs-5">
                                                    <?php echo amountFormat($totals['grand_total']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-5">
                                <div class="mb-4">
                                    <i class="fas fa-inbox fa-5x text-muted opacity-25"></i>
                                </div>
                                <h4 class="text-muted mb-3">No Transactions Found</h4>
                                <p class="text-muted mb-4 fs-6">There are no transactions matching your current filters. Try adjusting your search criteria.</p>
                                <a href="<?php echo base_url('pos_invoices/day_end_report'); ?>" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-redo-alt me-2"></i> Reset All Filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}
.badge {
    font-weight: 500;
}
.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}
.btn-group .btn:last-child {
    margin-right: 0;
}

/* Page-specific adjustments to reduce left gap shown in sidebar layouts */
#day-end-report-page .content-body > .card {
     margin-left: 0 !important;
     margin-right: 0 !important;
     max-width: none !important;
}

#day-end-report-page .content-body {
     padding-left: 0 !important;
     padding-right: 0 !important;
}

/* Adjust card size and style to match dashboard */
.card.border-0.shadow-sm.h-100 {
    padding: 0 !important;
    height: auto;
    width: auto;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.card.border-0.shadow-sm.h-100 .card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
}
.card.border-0.shadow-sm.h-100 .card-body h4 {
    font-size: 1.5rem;
    margin: 0;
}
.card.border-0.shadow-sm.h-100 .card-body h6 {
    font-size: 0.875rem;
    margin: 0;
}

/* Adjust card height to be smaller */
.card.border-0.shadow-sm.h-100 {
    height: 120px !important; /* Set a smaller fixed height */
}
.card.border-0.shadow-sm.h-100 .card-body {
    padding: 0.75rem; /* Adjust padding to fit smaller height */
}

/* Center align text and icon in cards */
.card.border-0.shadow-sm.h-100 {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    height: 150px; /* Maintain smaller height */
}
.card.border-0.shadow-sm.h-100 .card-body {
    padding: 0.75rem; /* Adjust padding */
}
.card.border-0.shadow-sm.h-100 .card-body .d-flex {
    flex-direction: column;
    align-items: center;
}
.card.border-0.shadow-sm.h-100 .card-body h4,
.card.border-0.shadow-sm.h-100 .card-body h6 {
    margin: 0;
}



/* Adjust card layout back to 3 cards per row */
.row.mb-4.g-3 .col-xl-4 {
    flex: 0 0 33.333%; /* Set width to one-third for 3 cards per row */
    max-width: 33.333%;
}

/* Small pagination styling */
.small-pagination .page-link {
    font-size: 0.75rem !important;
    padding: 0.25rem 0.5rem !important;
    line-height: 1.2;
}
.small-pagination .page-item {
    margin: 0 1px;
}
</style>

<script>
$(document).ready(function() {
    $('#start_date, #end_date').on('change', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        if (startDate && endDate && startDate > endDate) {
            alert('Start date cannot be greater than end date');
            $(this).val('');
        }
    });

    // Handle entries per page change
    $('#entries_per_page').on('change', function() {
        var limit = $(this).val();
        var currentUrl = window.location.href;
        var url = new URL(currentUrl);
        
        // Update or add the limit parameter
        url.searchParams.set('limit', limit);
        // Reset to page 1 when changing limit
        url.searchParams.set('page', '1');
        
        // Redirect to the new URL
        window.location.href = url.toString();
    });

    // Add smooth animations
    $('.card').css('opacity', '0').animate({opacity: 1}, 600);
    $('.table-responsive').css('opacity', '0').delay(300).animate({opacity: 1}, 600);
});
</script>