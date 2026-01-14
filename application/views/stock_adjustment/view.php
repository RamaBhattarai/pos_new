<div id="stock-adjustment-report">
    <link rel="stylesheet" href="<?= base_url('assets/css/stock_adjustment_report.css') ?>">

    <!-- Company Info -->
    <div class="report-header">
        <h2><?= $company['cname'] ?></h2>
        <p><strong>Phone:</strong> <?= $company['phone'] ?></p>
        <p><strong>Email:</strong> <?= $company['email'] ?></p>
        <p><strong>Address:</strong> <?= $company['address'] ?>, <?= $company['city'] ?>, <?= $company['region'] ?>,
            <?= $company['country'] ?> - <?= $company['postbox'] ?></p>
    </div>

    <!-- Adjustment Summary -->
    <div class="section-title">Adjustment Details</div>
    <p><strong>Reason:</strong> <?= $adjustment['reason'] ?></p>
    

    <table class="report-table">
        <thead>
        <tr>
            <th>Adjustment No</th>
            <th>Reason</th>
            <th>Date</th>
            <th>Note</th>
            <th>Status</th>
            
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= $adjustment['adjustment_no'] ?></td>
            <td><?= $adjustment['reason'] ?></td>
            <td>
                <?php if (!empty($adjustment['adjustment_date'])): ?>
                    <?php 
                    // Check if format information is available
                    $originalFormat = $adjustment['adjustment_date_format'] ?? 'english';
                    $rawDate = $adjustment['adjustment_date'];
                    ?>
                    <span class="date-display" data-raw="<?= $rawDate ?>" data-format="<?= $originalFormat ?>">
                        <?= date('Y-m-d', strtotime($rawDate)) ?>
                    </span>
                    <span class="english-date" style="display:none; color:#666; font-size:0.9em;"></span>
                <?php endif; ?>
            </td>
            <td><?= $adjustment['note'] ?></td>
            <td><?= ucfirst($adjustment['status']) ?></td>
            
        </tr>
        </tbody>
    </table>

    <!-- Adjusted Products -->
    <div class="section-title">Adjusted Products</div>
    <table class="report-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Code</th>
            <th>Name</th>
            <th>Purchase Price</th>
            <th>Quantity</th>
            <th>Adjustment Type</th>
            <th>Warehouse</th> <!-- Add this -->
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($adjusted_products as $product): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $product['product_code'] ?></td>
<td><?= $product['product_name'] ?></td>
<td>NRS <?= number_format($product['product_price'], 2) ?></td>

                <td>
                    <?= $product['adjustment_type'] === 'decrement' ? '-' : '+' ?>
                    <?= $product['quantity'] ?> Pcs
                </td>
                <td><?= $product['adjustment_type'] ?></td>
                <td><?= htmlspecialchars($product['warehouse_name'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
