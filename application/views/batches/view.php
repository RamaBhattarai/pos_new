<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5>Batch Details - <?php echo $batch['batch_no']; ?>
                <a href="<?php echo base_url('batches'); ?>" class="btn btn-secondary btn-sm float-right">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h5>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Batch Number:</th>
                                <td><?php echo $batch['batch_no']; ?></td>
                            </tr>
                            <tr>
                                <th>Product Name:</th>
                                <td><?php echo $batch['product_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Product Code:</th>
                                <td><?php echo $batch['product_code']; ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?php echo $batch['category_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Warehouse:</th>
                                <td><?php echo $batch['warehouse_name']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Quantity:</th>
                                <td><?php echo number_format($batch['qty'], 2); ?> units</td>
                            </tr>
                            <tr>
                                <th>Expiry Date:</th>
                                <td>
                                    <?php 
                                    if ($batch['expiry_date'] && $batch['expiry_date'] !== '0000-00-00') {
                                        echo dateformat($batch['expiry_date']);
                                        
                                        // Calculate days to expiry
                                        $today = new DateTime();
                                        $expiry = new DateTime($batch['expiry_date']);
                                        $diff = $today->diff($expiry);
                                        
                                        if ($expiry < $today) {
                                            echo ' <span class="badge badge-danger">Expired ' . $diff->days . ' days ago</span>';
                                        } elseif ($diff->days <= 7) {
                                            echo ' <span class="badge badge-warning">Expires in ' . $diff->days . ' days</span>';
                                        } else {
                                            echo ' <span class="badge badge-success">Expires in ' . $diff->days . ' days</span>';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Purchase Price:</th>
                                <td>NRS <?php echo number_format($batch['purchase_price'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Selling Price:</th>
                                <td>NRS <?php echo number_format($batch['selling_price'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Profit:</th>
                                <td>NRS <?php echo number_format($batch['profit'], 2); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="text-center">
                            <button type="button" class="btn btn-warning" onclick="editBatch(<?php echo $batch['id']; ?>)">
                                <i class="fa fa-edit"></i> Edit Batch
                            </button>
                            <a href="<?php echo base_url('products/edit?id=' . $batch['product_id']); ?>" class="btn btn-info">
                                <i class="fa fa-cube"></i> Edit Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editBatch(batchId) {
    window.opener.editBatch(batchId);
    window.close();
}
</script>