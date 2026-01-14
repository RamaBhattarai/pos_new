<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $this->lang->line('Add New supplier Details') ?></h5>

            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <form method="post" id="data_form" class="form-horizontal">
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="name"><?php echo $this->lang->line('Name') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="Name"
                               class="form-control margin-bottom required" name="name">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="name"><?php echo $this->lang->line('Company') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="Company"
                               class="form-control margin-bottom" name="company">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="phone"><?php echo $this->lang->line('Phone') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="phone"
                               class="form-control margin-bottom  required" name="phone">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="email"><?php echo $this->lang->line('Email') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="email"
                               class="form-control margin-bottom" name="email">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="address"><?php echo $this->lang->line('Address') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="address"
                               class="form-control margin-bottom" name="address">
                    </div>
                </div>
                <!-- <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="city"><?php echo $this->lang->line('City') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="city"
                               class="form-control margin-bottom" name="city">
                    </div>
                </div> -->
                <!-- <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="region"><?php echo $this->lang->line('Region') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="Region"
                               class="form-control margin-bottom" name="region">
                    </div>
                </div> -->
                <!-- <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="country"><?php echo $this->lang->line('Country') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="Country"
                               class="form-control margin-bottom" name="country">
                    </div>
                </div> -->
                <!-- <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="postbox"><?php echo $this->lang->line('PostBox') ?></label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="PostBox"
                               class="form-control margin-bottom" name="postbox">
                    </div>
                </div> -->
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="postbox"><?php echo $this->lang->line('TAX') ?> ID</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="TAX"
                               class="form-control margin-bottom" name="taxid">
                    </div>
                </div>

                <!-- Party Confirmation Settings Section -->
                <div class="form-group row">
                    <div class="col-sm-12">
                        <hr>
                        <h5 class="text-primary"><i class="fa fa-handshake-o"></i> Party Confirmation Settings</h5>
                        <p class="text-muted">Set threshold amount for automatic confirmation alerts</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="confirmation_threshold">Confirmation Threshold</label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo $this->config->item('currency'); ?></div>
                            <input type="number" placeholder="100000" 
                                   class="form-control margin-bottom" 
                                   name="confirmation_threshold" 
                                   id="confirmation_threshold"
                                   value="50000"
                                   step="1000">
                        </div>
                        <small class="text-muted">System will alert when total purchases exceed this amount</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                        <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                               value="<?php echo $this->lang->line('Add') ?>" data-loading-text="Adding...">
                        <input type="hidden" value="supplier/addsupplier" id="action-url">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

