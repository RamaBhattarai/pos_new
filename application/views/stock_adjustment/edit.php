<form method="post" action="<?= base_url('StockAdjustment/update') ?>" class="stock-adjustment-form">

  <div class="form-group">
    <label for="adjustment_no">Adjustment No</label>
    <input type="text" id="adjustment_no" name="adjustment_no" value="<?= htmlspecialchars($header['adjustment_no']) ?>" readonly>
  </div>

  <div class="form-group">
    <label for="adjustment_reason">Adjustment Reason</label>
    <input type="text" id="adjustment_reason" name="adjustment_reason" value="<?= htmlspecialchars($header['reason']) ?>" required>
  </div>

  <div class="form-group">
    <label for="product_search">Select Products *</label>
    <input type="text" id="product_search" placeholder="Search products...">
  </div>

  <table class="products-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Code</th>
        <th>Name</th>
        <th>Stock</th>
        <th>Warehouse</th> <!-- Add this -->
        <th>Adjustment Type</th>
        <th>Quantity</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="products-list">
      <?php foreach ($product_adjustments as $index => $row): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($row['code'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['stock'] ?? '0') ?></td>
          <td><?= htmlspecialchars($row['warehouse_name'] ?? '') ?></td> <!-- Show warehouse name -->
          <td>
            <select name="adjustment_type[]">
              <option value="increment" <?= $row['adjustment_type'] === 'increment' ? 'selected' : '' ?>>Increment</option>
              <option value="decrement" <?= $row['adjustment_type'] === 'decrement' ? 'selected' : '' ?>>Decrement</option>
            </select>
          </td>
          <td>
            <input type="number" name="quantity[]" value="<?= $row['quantity'] ?>" min="1" required>
          </td>
          <td>
    <button type="button" class="remove-product-btn">Remove</button>
    <input type="hidden" name="product_id[]" value="<?= $row['product_id'] ?>">
    <input type="hidden" name="warehouse[]" value="<?= $row['warehouse'] ?>">
    <input type="hidden" name="original_quantity[]" value="<?= $row['quantity'] ?>">
    <input type="hidden" name="original_adjustment_type[]" value="<?= $row['adjustment_type'] ?>">
</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="form-footer">
    <div class="form-group note-group">
      <label for="note">Note</label>
      <textarea id="note" name="note" placeholder="Write your note here!"><?= htmlspecialchars($header['note']) ?></textarea>
    </div>
    <div class="form-group">
      <label for="adjustment_date">Adjustment Date</label>
      <input type="date" id="adjustment_date" name="adjustment_date" value="<?= date('Y-m-d', strtotime($header['adjustment_date'])) ?>" required>
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="active" <?= $header['status'] === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $header['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>
  </div>

  <!-- <button type="button" id="add-product-btn" class="btn-primary">Add Product</button> -->
  <button type="submit" class="btn-primary" id="save-adjustment-btn">Save Changes</button>

</form>


 



