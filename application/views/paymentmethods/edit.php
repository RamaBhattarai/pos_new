<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Payment Method</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('paymentmethods'); ?>">Payment Methods</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Payment Method</h3>
                        </div>
                        
                        <?php echo form_open('paymentmethods/edit/' . $payment_method['id'], array('class' => 'form-horizontal')); ?>
                        <div class="card-body">
                            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                            
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Payment Method Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo set_value('name', $payment_method['name']); ?>" 
                                           placeholder="e.g. Credit Card, Mobile Banking, etc." required>
                                    <small class="form-text text-muted">Enter a unique name for this payment method</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="account_id" class="col-sm-3 col-form-label">Link to Account <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="account_id" name="account_id" required>
                                        <option value="">-- Select Account --</option>
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $account): ?>
                                                <option value="<?php echo $account['id']; ?>" 
                                                        <?php echo set_select('account_id', $account['id'], ($account['id'] == $payment_method['account_id'])); ?>>
                                                    <?php echo htmlspecialchars($account['holder']); ?> 
                                                    (Balance: <?php echo number_format($account['lastbal'], 2); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Select which account this payment method should be linked to</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Update Payment Method
                                    </button>
                                    <a href="<?php echo base_url('paymentmethods'); ?>" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
