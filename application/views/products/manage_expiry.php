<div class="expiry-table-container">
    <h2>Expiring Products Notifications</h2>
    <table class="expiry-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Warehouse</th>
                <th>Expiry Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <tr class="<?= $product['expiry_alert_seen'] ? '' : 'table-warning' ?>">
                    <td data-label="Product Name"><?= htmlspecialchars($product['product_name']) ?></td>
                    <td data-label="Warehouse"><?= htmlspecialchars($product['warehouse_name']) ?></td>
                    <td data-label="Expiry Date">
    <?= date('F j, Y', strtotime($product['expiry'])) ?>
    <a href="<?= base_url('products/edit?id=' . $product['pid']) ?>" title="Edit" style="margin-left: 8px; color: #2980b9;">
        <i class="fas fa-edit"></i>
    </a>
</td>
                    <td data-label="Status">
                        <?php if ($product['expiry_alert_seen']): ?>
                            <span class="badge-seen">Seen</span>
                        <?php else: ?>
                            <span class="badge-unseen">Unseen</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center; padding: 2rem; color:#7f8c8d;">No expiring products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination justify-content-center" style="margin-top: 2rem;">
        <?= $pagination ?>
    </div>
</div>
