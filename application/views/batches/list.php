<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.batch-status-expired {
    color: #dc3545 !important;
    font-weight: bold;
}
.batch-status-expiring {
    color: #ffc107 !important;
    font-weight: bold;
}
.batch-status-active {
    color: #28a745 !important;
    font-weight: bold;
}
.batch-status-out-of-stock {
    color: #6c757d !important;
    font-weight: bold;
}
</style>

<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5>Batch Management</h5>
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
                <table id="batchestable" class="table table-striped table-bordered zero-configuration" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Batch No</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Warehouse</th>
                        <th>Expiry Date</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Settings</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Batch No</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Warehouse</th>
                        <th>Expiry Date</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Settings</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Batch</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editBatchForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-batch-id">
                    <div class="form-group">
                        <label>Product:</label>
                        <input type="text" id="edit-product-name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Batch Number:</label>
                        <input type="text" id="edit-batch-no" name="batch_no" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Expiry Date:</label>
                        <input type="date" id="edit-expiry-date" name="expiry_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" id="edit-qty" name="qty" class="form-control" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Selling Price:</label>
                        <input type="number" id="edit-selling-price" name="selling_price" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var batchTable;

$(document).ready(function () {
    // Initialize DataTable
    batchTable = $('#batchestable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('batches/ajax_list')?>",
            "type": "POST",
            "data": {'<?=$this->security->get_csrf_token_name()?>': crsf_hash}
        },
        "columns": [
            { 
                "data": "DT_RowIndex",
                "name": "DT_RowIndex",
                "orderable": false,
                "searchable": false
            },
            { "data": "batch_no" },
            { "data": "product_name" },
            { 
                "data": "qty",
                "render": function(data, type, row) {
                    return data;
                }
            },
            { "data": "product_code" },
            { "data": "category_name" },
            { "data": "warehouse_name" },
            { 
                "data": "expiry_date",
                "render": function(data, type, row) {
                    if (data && data !== 'N/A' && data !== '0000-00-00') {
                        var date = new Date(data);
                        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                                     'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        return months[date.getMonth()] + ' ' + 
                               String(date.getDate()).padStart(2, '0') + ', ' + 
                               date.getFullYear();
                    }
                    return 'N/A';
                }
            },
            { 
                "data": "selling_price",
                "render": function(data, type, row) {
                    return 'NRS ' + (data ? parseFloat(data).toFixed(2) : '0.00');
                }
            },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    var className = '';
                    switch(data) {
                        case 'Expired':
                            className = 'batch-status-expired';
                            break;
                        case 'Expiring Soon':
                            className = 'batch-status-expiring';
                            break;
                        case 'Out of Stock':
                            className = 'batch-status-out-of-stock';
                            break;
                        default:
                            className = 'batch-status-active';
                    }
                    return '<span class="' + className + '">' + data + '</span>';
                }
            },
            { "data": "actions", "orderable": false }
        ],
        "order": [[ 7, "asc" ]], // Order by expiry date
        "pageLength": 25,
        "responsive": true,
        "columnDefs": [
            {
                "targets": [0, 10], // First and last column
                "orderable": false,
            },
        ],
        dom: 'Blfrtip',
        lengthMenu: [10, 20, 50, 100, 200, 500],
        buttons: [
            {
                extend: 'excelHtml5',
                footer: true,
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // Exclude # and Settings columns
                }
            }
        ]
    });

    // Edit batch form submission
    $('#editBatchForm').on('submit', function(e) {
        e.preventDefault();
        var batchId = $('#edit-batch-id').val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?php echo site_url('batches/edit') ?>/' + batchId,
            method: 'POST',
            data: formData + '&<?=$this->security->get_csrf_token_name()?>='+crsf_hash,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'Success') {
                    $('#editBatchModal').modal('hide');
                    batchTable.ajax.reload();
                    $('#notify .message').text(response.message);
                    $('#notify').show().delay(3000).fadeOut();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating batch');
            }
        });
    });
});

function viewBatch(batchId) {
    window.open('<?php echo site_url('batches/view') ?>/' + batchId, '_blank');
}

function editBatch(batchId) {
    $.ajax({
        url: '<?php echo site_url('batches/edit') ?>/' + batchId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            var batch = response.batch;
            $('#edit-batch-id').val(batch.id);
            $('#edit-product-name').val(batch.product_name);
            $('#edit-batch-no').val(batch.batch_no);
            $('#edit-expiry-date').val(batch.expiry_date);
            $('#edit-qty').val(batch.qty);
            $('#edit-selling-price').val(batch.selling_price);
            $('#editBatchModal').modal('show');
        }
    });
}
</script>