<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>
    <div class="card-body">
        <h5 class="title"> Product Variations 
            <a href="<?php echo base_url('units/create_product_variation') ?>" class="btn btn-primary btn-sm rounded">
                Add New Variation
            </a>
        </h5>
        <p class="text-muted">Manage single and multi-attribute product variations</p>

        <hr>
        <table id="hierarchyTable" class="table table-striped table-bordered zero-configuration" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Attributes</th>
                <th>Variation Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach ($units as $row) {
                if ($row['variation_name']) { // Only show complete variations
                    $type_badge = $row['type'] === 'multi' 
                        ? '<span class="badge badge-success">Multi-Attribute</span>' 
                        : '<span class="badge badge-info">Single Attribute</span>';
                    
                    $attributes_display = $row['type'] === 'multi' 
                        ? $row['variation_option_name']
                        : $row['option_name'] . ' → ' . $row['variation_option_name'];
                    
                    echo "<tr>
                        <td>$i</td>
                        <td>$type_badge</td>
                        <td>$attributes_display</td>
                        <td><strong>{$row['variation_name']}</strong></td>
                        <td>
                            <a href='#' class='btn btn-danger btn-xs delete-variation' data-id='{$row['variation_id']}' data-type='{$row['type']}'>
                                <i class='fa fa-trash'></i> Delete
                            </a>
                        </td>
                    </tr>";
                    $i++;
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#hierarchyTable').DataTable({
            responsive: true,
            order: [[1, 'asc'], [3, 'asc']] // Sort by type then by name
        });
        
        // Handle variation deletion
        $('.delete-variation').click(function(e) {
            e.preventDefault();
            const variationId = $(this).data('id');
            const variationType = $(this).data('type');
            
            let confirmMessage = 'Are you sure you want to delete this variation?';
            if (variationType === 'multi') {
                confirmMessage = 'Are you sure you want to delete this multi-attribute variation? This will also remove all its attribute relationships.';
            }
            
            if (confirm(confirmMessage)) {
                // AJAX call to delete variation
                $.post('<?php echo base_url('units/delete_variation'); ?>', {
                    variation_id: variationId,
                    type: variationType
                }, function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'Success') {
                            location.reload();
                        } else {
                            alert('Error deleting variation: ' + result.message);
                        }
                    } catch(e) {
                        alert('Error processing response');
                    }
                }).fail(function() {
                    alert('Network error occurred');
                });
            }
        });
    });
</script>

<style>
.badge-success {
    background-color: #28a745;
}
.badge-info {
    background-color: #17a2b8;
}
.table td {
    vertical-align: middle;
}
</style>