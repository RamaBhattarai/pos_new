
<div class="inventory-adjustments-page">
    <!-- Your existing HTML content -->
<div class="top-bar">
    <h2>Inventory Adjustments</h2>
    <a href="<?= base_url('StockAdjustment/create') ?>" class="btn">+ Add Adjustment</a>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Adjustment No</th>
            <th>Reason</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($adjustments)): ?>
            <?php foreach ($adjustments as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($row['adjustment_no']) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= date('jS M, Y', strtotime($row['adjustment_date'])) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                   <td class="action-links">
    <a href="<?= base_url('StockAdjustment/view/' . $row['adjustment_no']) ?>" title="View" class="action-btn view">
        <i class="icon-eye"></i>
    </a>
    <a href="<?= base_url('StockAdjustment/edit/' . $row['adjustment_no']) ?>" title="Edit" class="action-btn edit">
        <i class="icon-pencil"></i>
    </a>

    <a href="#" 
   class="action-btn delete btn-delete" 
   data-url="<?= base_url('StockAdjustment/delete/' . $row['adjustment_no']) ?>" 
   title="Delete">
    <i class="icon-trash"></i>
</a>
</td>



                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="no-data">No stock adjustments found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>


 <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the stock adjustment and revert stock changes.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
                
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL
                    window.location.href = url;
                }
            });
        });
    });
});

</script>
</body>
</html>

