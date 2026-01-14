<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Methods</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Payment Methods</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Manage Payment Methods</h3>
                            <div class="card-tools">
                                <a href="<?php echo base_url('paymentmethods/add'); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Payment Method
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Payment Method Name</th>
                                            <th>Linked Account</th>
                                            <th>Account Balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($payment_methods)): ?>
                                            <?php foreach ($payment_methods as $method): ?>
                                                <tr>
                                                    <td><?php echo $method['id']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($method['name']); ?></strong></td>
                                                    <td>
                                                        <?php if ($method['account_name'] == 'Not Linked'): ?>
                                                            <span class="badge badge-warning">Not Linked</span>
                                                        <?php else: ?>
                                                            <?php echo htmlspecialchars($method['account_name']); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($method['account_name'] != 'Not Linked'): ?>
                                                            <span class="badge badge-info">
                                                                <?php echo number_format($method['balance'], 2); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?php echo base_url('paymentmethods/edit/' . $method['id']); ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <a href="<?php echo base_url('paymentmethods/delete/' . $method['id']); ?>" 
                                                               class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Are you sure you want to delete this payment method?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No payment methods found. <a href="<?php echo base_url('paymentmethods/add'); ?>">Add one now</a></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>