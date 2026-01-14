<form method="post" id="data_form" class="content-body">
    <div class="sidebar-left sidebar-fixed bg-white">
        <div class="sidebar">
            <div class="sidebar-content ">
                <div class="card-body chat-fixed-search">

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="ft-search"></i></span>
                        </div>
                        <input type="text" class="form-control" id="pos-customer-box"
                               placeholder="<?php echo $this->lang->line('Enter Customer Name'); ?>"
                               aria-describedby="button-addon2">
                        <div class="input-group-append" id="button-addon2">
                            <button class="btn btn-primary" type="button" data-toggle="modal"
                                    data-target="#Pos_addCustomer"> <?php echo $this->lang->line('Add') ?></button>
                        </div>
                    </div>
                    <div id="customer-box-result" class="col-md-12"></div>
                    <div id="customer" class="col-md-12 ml-3">
                        <div class="clientinfo">

                            <input type="hidden" name="customer_id" id="customer_id" value="1">
                            <div id="customer_name"><?php echo $this->lang->line('Default'); ?>: <strong>Walk
                                    In </strong></div>
                        </div>


                    </div>

                </div>
                <div>
                    <div class="users-list-padding media-list">

                        <br>
                        <div class="row bg-gradient-directional-purple white m-0 pt-1 pb-1">
                            <div class="col-6 ">
                                <i class="fa fa-briefcase"></i>
                                <?php echo $this->lang->line('Products') ?></th>
                            </div>
                            <div class="col-3">
                                <i class="fa fa-money"></i><?php echo $this->lang->line('Price') ?>
                            </div>
                            <div class="col-3">
                                <i
                                        class="fa fa-shopping-bag"></i> <?php echo $this->lang->line('Total') ?>
                            </div>
                        </div>
                        <div id="saman-pos2">
                            <div id="pos_items"></div>
                        </div>
                        <input type="hidden" name="total" class="form-control"
                               id="invoiceyoghtml" readonly="">
                        <hr class="mt-1">
                        <div class="row m-2">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Shipping') ?></strong>
                            </div>
                            <div class="col-3">
                                <input type="text" class="form-control form-control-sm shipVal"
                                       onkeypress="return isNumber(event)"
                                       placeholder="Value"
                                       name="shipping" autocomplete="off"
                                       onkeyup="billUpyog()">
                            </div>
                            <div class="col-3">
                                ( <?php echo $this->lang->line('Tax') ?> <?= $this->config->item('currency'); ?>
                                <span id="ship_final">0</span> )
                            </div>
                        </div>


                        <div class="row m-2">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Total Tax') ?></strong>
                            </div>
                            <div class="col-3"><?php echo currency($this->aauth->get_user()->loc);
                                ?>
                                <span id="taxr" class="mr-1">0</span>
                            </div>
                        </div>
                        <div class="row m-2">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Total Discount') ?></strong>
                            </div>
                            <div class="col-9"><?php echo currency($this->aauth->get_user()->loc);
                                ?>
                                <span id="discs"
                                      class="lightMode mr-1">0</span>
                                <small>(<?php echo $this->lang->line('Products') ?>)</small>
                            </div>
                        </div>
                        <div class="row m-2">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Grand Total') ?></strong>
                            </div>
                            <div class="col-9"><?php echo currency($this->aauth->get_user()->loc);
                                ?>
                                <span class="font-medium-1 blue text-bold-600"
                                      id="bigtotal">0.00</span>
                            </div>
                        </div>
                        <div class="row m-2">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Extra') . ' ' . $this->lang->line('Discount') ?></strong>
                            </div>
                            <div class="col-2">
                                <select class="form-control form-control-sm" id="extra_discount_type" name="extra_discount_type">
                                    <option value="%">%</option>
                                    <option value="NRS"><?php echo $this->config->item('currency'); ?></option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="text" class="form-control form-control-sm discVal"
                                       onkeypress="return isNumber(event)"
                                       placeholder="Value" value="0"
                                       name="disc_val" autocomplete="off"
                                       >
                                <input type="hidden"
                                       name="after_disc" id="after_disc" value="0">
                            </div>
                            <div class="col-4">
                                ( <?= $this->config->item('currency'); ?>
                                <span id="disc_final">0</span> )
                            </div>
                        </div>


                        <hr>
                        <?php if ($emp['key1']) { ?>
                            <div class="col">
                                <div class="form-group form-group-sm text-g">
                                    <label for="employee"><?php echo $this->lang->line('Employee') ?></label>

                                    <select id="employee" name="employee" class="form-control form-control-sm">
                                        <?php
                                        foreach ($employee as $row) {
                                            $cid = $row['id'];
                                            $title = $row['name'];
                                            echo "<option value='$cid'>$title</option>";
                                        }
                                        ?>
                                    </select></div>
                            </div>
                        <?php } ?>
                        <div class="m-1">

                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="btn btn-outline-primary  mr-1 mb-1" id="base-tab1" data-toggle="tab"
                                       aria-controls="tab1" href="#tab1" role="tab" aria-selected="false"><i
                                                class="fa fa-trophy"></i>
                                        <?php echo $this->lang->line('Coupon') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-secondary mr-1 mb-1" id="base-tab2" data-toggle="tab"
                                       aria-controls="tab2" href="#tab2" role="tab" aria-selected="false"><i
                                                class="icon-handbag"></i>
                                        <?php echo $this->lang->line('POS') . ' ' . $this->lang->line('Settings') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-danger  mr-1 mb-1" id="base-tab3" data-toggle="tab"
                                       aria-controls="tab3" href="#tab3" role="tab" aria-selected="false"><i
                                                class="fa fa-save"></i> <?php echo $this->lang->line('Draft') ?>(s)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-success mb-1" id="base-tab4" data-toggle="tab"
                                       aria-controls="tab4" href="#tab4" role="tab" aria-selected="false"><i
                                                class="fa fa-cogs"></i>
                                        <?php echo $this->lang->line('Invoice Properties') ?></a>
                                </li>
                            </ul>
                            <div class="tab-content px-1 pt-1">
                                <div class="tab-pane" id="tab1" role="tabpanel" aria-labelledby="base-tab1">
                                    <div class="input-group">

                                        <input type="text" class="form-control"
                                               id="coupon" name="coupon"><input type="hidden"
                                                                                name="coupon_amount"
                                                                                id="coupon_amount"
                                                                                value="0"><span
                                                class="input-group-addon round"> <input type="button"
                                                                                        class="apply_coupon btn btn-small btn-primary sub-btn"
                                                                                        value="<?php echo $this->lang->line('Apply') ?>"></span>


                                    </div>
                                    <input type="hidden" class="text-info" name="i_coupon" id="i_coupon"
                                           value="">
                                    <span class="text-primary text-bold-600" id="r_coupon"></span>
                                </div>
                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="base-tab2">
                                    <div class="row">
                                        <div class="col-4 blue text-xs-center"><?php echo $this->lang->line('Warehouse') ?>
                                            <select
                                                    id="v2_warehouses"
                                                    name="warehouse"
                                                    class="selectpicker form-control round teal">
                                                <?php if (!isset($warehouse_filtered) || !$warehouse_filtered) { echo $this->common->default_warehouse();
                                                echo '<option value="0">' . $this->lang->line('All') . '</option>'; } ?><?php foreach ($warehouse as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                                } ?>

                                            </select></div>
                                        <div class="col-4 blue text-xs-center"><?php echo $this->lang->line('Tax') ?>
                                            <select class="form-control round"
                                                    onchange="changeTaxFormat(this.value)"
                                                    id="taxformat">
                                                <?php echo $taxlist; ?>
                                            </select></div>
                                        <div class="col-4 blue text-xs-center">  <?php echo $this->lang->line('Discount') ?>
                                            <select class="form-control round teal"
                                                    onchange="changeDiscountFormat(this.value)"
                                                    id="discountFormat">

                                                <?php echo $this->common->disclist() ?>
                                            </select></div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="base-tab3">
                                    <?php foreach ($draft_list as $rowd) {
                                        echo '<li class="indigo p-1"><a href="' . base_url() . 'pos_invoices/draft?id=' . $rowd['id'] . '"> #' . $rowd['tid'] . ' (' . $rowd['invoicedate'] . ')</a></li>';
                                    } ?>
                                </div>
                                <div class="tab-pane" id="tab4" role="tabpanel" aria-labelledby="base-tab4">
                                    <div class="form-group row">
                                        <div class="col-sm-3"><label for="invocieno"
                                                                     class="caption"><?php echo $this->lang->line('Invoice Number') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Invoice #"
                                                       name="invocieno" id="invocieno"
                                                       value="<?php echo $lastinvoice + 1 ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3"><label for="invocieno"
                                                                     class="caption"><?php echo $this->lang->line('Reference') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="Reference #"
                                                       name="refer">
                                            </div>
                                        </div>
                                           <div class="form-group row">

                                    <div class="col-sm-6"><label for="invociedate"
                                                                 class="caption"><?php echo $this->lang->line('Invoice Date'); ?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar4"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round required"
                                                   placeholder="Billing Date" name="invoicedate" id="invoicedate"
                                                   data-toggle="datepicker"
                                                   autocomplete="false">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieduedate"
                                                                 class="caption"><?php echo $this->lang->line('Invoice Due Date') ?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round required"
                                                   name="invocieduedate" id="invocieduedate"
                                                   placeholder="Due Date" autocomplete="false"    data-toggle="datepicker">
                                        </div>
                                    </div>
                                </div>


         </div>


                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <?php echo $this->lang->line('Payment Terms') ?> <select
                                                    name="pterms"
                                                    class="selectpicker form-control"><?php foreach ($terms as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                                } ?>

                                            </select>
                                            <?php if ($exchange['active'] == 1) {
                                                echo $this->lang->line('Payment Currency client') ?>
                                            <?php } ?>
                                            <?php if ($exchange['active'] == 1) {
                                                ?>
                                                <select name="mcurrency"
                                                        class="selectpicker form-control">
                                                <option value="0">Default</option>
                                                <?php foreach ($currency as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['symbol'] . ' (' . $row['code'] . ')</option>';
                                                } ?>

                                                </select><?php } ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="toAddInfo"
                                                   class="caption"><?php echo $this->lang->line('Invoice Note') ?></label>
                                            <textarea class="form-control" name="notes" rows="2"></textarea>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="content-right">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="chat-app-window">
                    <div class="row ">


                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa fa-barcode p-0"></i>&nbsp;<input type="checkbox"
                                                                                      aria-label="Enable BarCode"
                                                                                      value="1" id="bar_only">
                                    </div>
                                </div>
                                <input type="text" class="form-control text-center round mousetrap"
                                       name="product_barcode"
                                       placeholder="Enter Product name, code or scan barcode" id="v2_search_bar"
                                       autocomplete="off" autofocus="autofocus">
                            </div>


                        </div>
                        <div class="col-md-3  grey text-xs-center"><select
                                    id="v2_categories"
                                    class="selectpicker form-control round teal">
                                <option value="0"><?php echo $this->lang->line('All') ?></option><?php
                                foreach ($cat as $row) {
                                    $cid = $row['id'];
                                    $title = $row['title'];
                                    echo "<option value='$cid'>$title</option>";
                                }
                                ?>
                            </select></div>


                    </div>
                    <hr class="white">


                    <div class="row m-0">
                        <div class="col-md-12 pt-0 " id="pos_item">
                            <!-- pos items -->
                        </div>
                    </div>
                </section>
                <section class="chat-app-form">
    <div class="form-group text-center">
        <!-- Button Group with Icons in different sizes -->
        <div class="btn-group btn-group-lg" role="group">
            <button type="button" class="possubmit btn btn-warning">
                <i class="fa fa-save"></i> <?php echo $this->lang->line('Draft') ?>
            </button>

            <button type="button" class="btn btn-success possubmit3" data-type="6" data-toggle="modal"
                    data-target="#basicPay">
                <i class="fa fa-money"></i> <?php echo $this->lang->line('Payment') ?>
            </button>

            <?php
            /*
            if ($enable_card['url']) { ?>
                <button type="button" class="btn btn-primary possubmit2" data-type="4"
                        data-toggle="modal" data-target="#cardPay">
                    <i class="fa fa-credit-card"></i> <?php echo $this->lang->line('Card') ?>
                </button>
            <?php }
            */
            ?>
        </div>

        <a href="<?= base_url('stockreturn/create_client') ?>" class="red float-right">
            <i class="fa fa-reply-all"></i>
        </a>
    </div>
</section>

            </div>
        </div>
    </div>
    <input type="hidden" value="pos_invoices/action" id="action-url">
    <input type="hidden" value="0" id="subttlform"
           name="subtotal">
    <input type="hidden" value="search" id="billtype">
    <input type="hidden" value="0" name="counter" id="ganak">
    <input type="hidden" value="0" id="custom_discount">
    <input type="hidden" value="<?php echo currency($this->aauth->get_user()->loc); ?>" name="currency">
    <input type="hidden" value="<?= $taxdetails['handle']; ?>" name="taxformat" id="tax_format">
    <input type="hidden" value="<?= $taxdetails['format']; ?>" name="tax_handle" id="tax_status">
    <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">
    <input type="hidden" value="<?= $this->common->disc_status()['disc_format']; ?>" name="discountFormat"
           id="discount_format">
    <input type="hidden" value="<?= amountFormat_general($this->common->disc_status()['ship_rate']); ?>" name="shipRate"
           id="ship_rate">
    <input type="hidden" value="<?= $this->common->disc_status()['ship_tax']; ?>" name="ship_taxtype"
           id="ship_taxtype">
    <input type="hidden" value="0" name="ship_tax" id="ship_tax">
</form>
<audio id="beep" src="<?= assets_url() ?>assets/js/beep.wav" autoplay="false"></audio>

<div class="modal fade" id="Pos_addCustomer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">
            <form method="post" id="product_action" class="form-horizontal">
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-directional-blue white">
                    <i class="icon-user-plus"></i> <?php echo $this->lang->line('Add Customer') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5><?php echo $this->lang->line('Billing Address') ?></h5>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="name"><?php echo $this->lang->line('Name') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Name"
                                           class="form-control margin-bottom" id="mcustomer_name" name="name"
                                           required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="phone"><?php echo $this->lang->line('Phone') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Phone"
                                           class="form-control margin-bottom" name="phone" id="mcustomer_phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="email"><?php echo $this->lang->line('Email') ?></label>
                                <div class="col-sm-10">
                                    <input type="email" placeholder="Email"
                                           class="form-control margin-bottom" name="email"
                                           id="mcustomer_email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="address"><?php echo $this->lang->line('Address') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Address"
                                           class="form-control margin-bottom " name="address"
                                           id="mcustomer_address1">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="customergroup"><?php echo $this->lang->line('Group') ?></label>
                                <div class="col-sm-10">
                                    <select name="customergroup" class="form-control">
                                        <?php
                                        foreach ($customergrouplist as $row) {
                                            $cid = $row['id'];
                                            $title = $row['title'];
                                            echo "<option value='$cid'>$title</option>";
                                        }
                                        ?>
                                    </select>


                                </div>
                            </div>

                            <?php
                            if (is_array($custom_fields_c)) {
                                foreach ($custom_fields_c as $row) {
                                    if ($row['f_type'] == 'text') { ?>
                                        <div class="form-group row">

                                            <label class="col-sm-2 col-form-label"
                                                   for="docid"><?= $row['name'] ?></label>

                                            <div class="col-sm-8">
                                                <input type="text" placeholder="<?= $row['placeholder'] ?>"
                                                       class="form-control margin-bottom b_input"
                                                       name="custom[<?= $row['id'] ?>]">
                                            </div>
                                        </div>


                                    <?php }
                                }
                            }
                            ?>
                        </div>


                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                    <input type="submit" id="mclient_add" class="btn btn-primary submitBtn" value="ADD"/ >
                </div>
            </form>
        </div>
    </div>
</div>
<!--card-->
<div class="modal fade" id="cardPay" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('Make Payment') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>

            </div>

            <!-- Modal Body -->
            <div class="modal-body ">
                <p id="statusMsg"></p>
                <form role="form" id="card_data">

                    <div class="row">
                        <div class="col-6">
                            <label for="cardNumber"><?php echo $this->lang->line('Payment Gateways') ?></label>
                            <select class="form-control" name="gateway"><?php
                                $surcharge_t = false;
                                foreach ($gateway as $row) {
                                    $cid = $row['id'];
                                    $title = $row['name'];
                                    if ($row['surcharge'] > 0) {
                                        $surcharge_t = true;
                                        $fee = '(<span class="gate_total"></span>+' . amountFormat_s($row['surcharge']) . ' %)';
                                    } else {
                                        $fee = '';
                                    }
                                    echo "<option value='$cid'>$title $fee</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-4"><br><img class="img-responsive pull-right"
                                                    src="<?php echo assets_url('assets/images/accepted_c22e0.png') ?>">
                        </div>
                    </div>


                    <div class="row mt-1">
                        <div class="col">
                            <button class="btn btn-success btn-lg"
                                    type="submit"
                                    id="pos_card_pay"
                                    data-type="2"><i
                                        class="fa fa-credit-card"></i> <?php echo $this->lang->line('Paynow') ?>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">

                        <?php if ($surcharge_t) echo '<br>' . $this->lang->line('Note: Payment Processing'); ?>

                    </div>
                    <div class="row" style="display:none;">
                        <div class="col-xs-12">
                            <p class="payment-errors"></p>
                        </div>
                    </div>

                    <input type="hidden" value="pos_invoices/action" id="pos_action-url">
                </form>

                <!-- shipping -->


            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<?php $this->load->view('pos/payment_modal',['acc_list'=>$acc_list, 'payment_methods'=>$payment_methods]); ?>
<div class="modal fade" id="register" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Your Register') ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>

            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center m-1"><?php echo $this->lang->line('Active') ?> - <span id="r_date"></span></div>


                <div class="row">
                    <div class="col-6">
                        <div class="form-group  text-bold-600 green">
                            <label for="amount"><?php echo $this->lang->line('Cash') ?>
                                (<?= $this->config->item('currency'); ?>)
                            </label>
                            <input type="number" class="form-control green" id="r_cash"
                                   value="0.00"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-5 col-md-5 pull-right">
                        <div class="form-group text-bold-600 blue">
                            <label for="b_change blue"><?php echo $this->lang->line('Card') ?></label>
                            <input
                                    type="number"
                                    class="form-control blue"
                                    id="r_card" value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group  text-bold-600 indigo">
                            <label for="amount"><?php echo $this->lang->line('Bank') ?>
                            </label>
                            <input type="number" class="form-control indigo" id="r_bank"
                                   value="0.00"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-5 col-md-5 pull-right">
                        <div class="form-group text-bold-600 red">
                            <label for="b_change"><?php echo $this->lang->line('Change') ?>(-)</label>
                            <input
                                    type="number"
                                    class="form-control red"
                                    id="r_change" value="0" readonly>
                        </div>
                    </div>
                </div>


                <div class="row" style="display:none;">
                    <div class="col-xs-12">
                        <p class="payment-errors"></p>
                    </div>
                </div>


                <!-- shipping -->


            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div class="modal fade" id="close_register" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Close') ?><?php echo $this->lang->line('Your Register') ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">
                        <a href="<?= base_url() ?>/register/close" class="btn btn-danger btn-lg btn-block"
                           type="submit"
                        ><i class="icon icon-arrow-circle-o-right"></i> <?php echo $this->lang->line('Yes') ?></a>
                    </div>
                    <div class="col-4"></div>
                </div>

            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div class="modal fade" id="stock_alert" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Stock Alert') ?> !</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="row p-1">
                    <div class="alert alert-danger mb-2" role="alert">
                        <strong>Insufficient Stock!</strong> The requested quantity exceeds available stock. Please adjust the quantity or select a different product.
                    </div>
                </div>

            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div id="shortkeyboard" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">ShortCuts</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>Alt+X</td>
                        <td>Focus to products search</td>
                    </tr>
                    <tr>
                        <td>Alt+C</td>
                        <td>Focus to customer search</td>
                    </tr>

                    <tr>
                        <td>Alt+S (twice)</td>
                        <td>PayNow + Thermal Print</td>
                    </tr>
                    <tr>
                        <td>Alt+Z</td>
                        <td>Make Card Payment</td>
                    </tr>
                    <tr>
                        <td>Alt+Q</td>
                        <td>Select First product</td>
                    </tr>
                    <tr>
                        <td>Alt+N</td>
                        <td>Create New Invoice</td>
                    </tr>


                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<div id="pos_print" class="modal fade" role="dialog">
    <div class="modal-dialog ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Legacy Print Mode</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body border_no_print" id="print_section">
                <embed src="<?= base_url('assets/images/ssl-seal.png') ?>"
                       type="application/pdf" height="600px" width="470" id="loader_pdf"
                ">
                <input id="loader_file" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    var wait = true;
    $.ajax({
        url: baseurl + 'search_products/v2_pos_search',
        dataType: 'html',
        method: 'POST',
        data: 'cid=' + $('#v2_categories').val() + '&wid=' + $('#v2_warehouses option:selected').val() + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
        success: function (data) {
            $('#pos_item').html(data);

        }
    });
    
     // Initialize tax format on page load to ensure proper VAT calculation
    $(document).ready(function() {
        // Trigger tax format initialization with current dropdown value
        var currentTaxValue = $('#taxformat').val();
        if (currentTaxValue && typeof changeTaxFormat === 'function') {
            changeTaxFormat(currentTaxValue);
        }
    });

    function update_register() {
        $.ajax({
            url: baseurl + 'register/status',
            dataType: 'json',
            data: crsf_token + '=' + crsf_hash,
            success: function (data) {
                $('#r_cash').val(data.cash);
                $('#r_card').val(data.card);
                $('#r_bank').val(data.bank);
                $('#r_change').val(data.change);
                $('#r_date').text(data.date);
            }
        });
    }

    update_register();
    $(".possubmit").on("click", function (e) {
        e.preventDefault();
        var o_data = $("#data_form").serialize() + '&type=' + $(this).attr('data-type');
        var action_url = $('#action-url').val();
        addObject(o_data, action_url);
    });

    $(".possubmit2").on("click", function (e) {
        e.preventDefault();
        $('#card_total').val(accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal));
    });

    $(".possubmit3").on("click", function (e) {
        e.preventDefault();
        var roundoff = parseFloat(accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal)).toFixed(two_fixed);

        <?php
        $round_off = $this->custom->api_config(4);
        if ($round_off['other'] == 'PHP_ROUND_HALF_UP') {
            echo ' roundoff=Math.ceil(roundoff);';
        } elseif ($round_off['other'] == 'PHP_ROUND_HALF_DOWN') {
            echo ' roundoff=Math.floor(roundoff);';
        }
        ?>
        $('#b_total').html(' <?= $this->config->item('currency'); ?> ' + accounting.formatNumber(roundoff));
        $('#p_amount').val(accounting.formatNumber(roundoff));

    });

    function update_pay_pos() {
        var am_pos = accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal);
        var ttl_pos = accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal);
        <?php
        $round_off = $this->custom->api_config(4);
        if ($round_off['other'] == 'PHP_ROUND_HALF_UP') {
            echo ' ttl_pos=Math.ceil(ttl_pos);';
        } elseif ($round_off['other'] == 'PHP_ROUND_HALF_DOWN') {
            echo ' ttl_pos=Math.floor(ttl_pos);';
        }
        ?>

        var due = parseFloat(ttl_pos - am_pos).toFixed(two_fixed);

        if (due >= 0) {
            $('#balance1').val(accounting.formatNumber(due));
            $('#change_p').val(0);
        } else {
            due = due * (-1)
            $('#balance1').val(0);
            $('#change_p').val(accounting.formatNumber(due));
        }
    }

    $('#pos_card_pay').on("click", function (e) {
        e.preventDefault();
        $('#cardPay').modal('toggle');
        $("#notify .message").html("<strong>Processing</strong>: .....");
        $("#notify").removeClass("alert-danger").addClass("alert-primary").fadeIn();
        $("html, body").animate({scrollTop: $('body').offset().top - 100}, 1000);
        var o_data = $("#data_form").serialize() + '&' + $("#card_data").serialize() + '&type=' + $(this).attr('data-type');
        var action_url = $('#action-url').val();
        addObject(o_data, action_url);
        update_register();
    });
    $('#pos_basic_pay').on("click", function (e) {
        e.preventDefault();
        $('#basicPay').modal('toggle');
        $("#notify .message").html("<strong>Processing</strong>: .....");
        $("#notify").removeClass("alert-danger").addClass("alert-primary").fadeIn();
        $("html, body").animate({scrollTop: $('body').offset().top - 100}, 1000);
        var o_data = $("#data_form").serialize() + '&p_amount=' + accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal) + '&p_method=' + $("#p_method option:selected").val() + '&type=' + $(this).attr('data-type') + '&account=' + $("#p_account option:selected").val() + '&employee=' + $("#employee option:selected").val();
        var action_url = $('#action-url').val();
        addObject(o_data, action_url);
        setTimeout(
            function () {
                update_register();
            }, 3000);
    });

    $('#pos_basic_print').on("click", function (e) {
        e.preventDefault();
        $('#basicPay').modal('toggle');
        $("#notify .message").html("<strong>Processing</strong>: .....");
        $("#notify").removeClass("alert-danger").addClass("alert-primary").fadeIn();
        $("html, body").animate({scrollTop: $('body').offset().top - 100}, 1000);
        var o_data = $("#data_form").serialize() + '&p_amount=' + accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal) + '&p_method=' + $("#p_method option:selected").val() + '&type=' + $(this).attr('data-type') + '&printnow=1' + '&account=' + $("#p_account option:selected").val() + '&employee=' + $("#employee option:selected").val();
        var action_url = $('#action-url').val();
        addObject(o_data, action_url);
        setTimeout(
            function () {
                update_register();
            }, 3000);
    });
    var file_id;
    $(document.body).on('click', '.print_image', function (e) {

        e.preventDefault();

        var inv_id = $(this).attr('data-inid');

        jQuery.ajax({
            url: '<?php echo base_url('pos_invoices/invoice_legacy') ?>',
            type: 'POST',
            data: 'inv=' + inv_id + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function (data) {
                file_id= data.file_name;
                $("#loader_pdf").attr('src','<?= base_url() ?>userfiles/pos_temp/'+data.file_name+'.pdf');
                $("#loader_file").val(data.file_name);
            },
        });

        $('#pos_print').modal('toggle');
        $("#print_section").printThis({
            //  beforePrint: function (e) {$('#pos_print').modal('hide');},

            printDelay: 500,
            afterPrint: clean_data()
        });
    });


    function clean_data() {
        setTimeout(function(){
        var file_id= $("#loader_file").val();
        jQuery.ajax({
            url: '<?php echo base_url('pos_invoices/invoice_clean') ?>',
            type: 'POST',
            data: 'file_id=' + file_id + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function (data) {

            },
        });
}, 2500);

    }

function updateExtraDiscount() {
    var disc_val = accounting.unformat($('.discVal').val(), accounting.settings.number.decimal);
    var disc_type = $('#extra_discount_type').val();
    var subtotal = accounting.unformat($('#subttlform').val(), accounting.settings.number.decimal);

    var disc_amount = 0;
    if (disc_val > 0 && subtotal > 0) {
        if (disc_type == 'NRS') {
            disc_amount = disc_val;
        } else {
            disc_amount = (subtotal * disc_val) / 100;
        }
    }
    $('#disc_final').html(accounting.formatNumber(disc_amount));
    $('#after_disc').val(accounting.formatNumber(disc_amount));

    // Now update the total using the correct discount
    var shipping = parseFloat(shipTot());
    var grand_total = subtotal + shipping - disc_amount;
    $("#invoiceyoghtml").val(accounting.formatNumber(grand_total));
    $("#bigtotal").html(accounting.formatNumber(grand_total));
}

$('#extra_discount_type').on('change', function() {
    updateExtraDiscount();
});
$('.discVal').on('input', function() {
    updateExtraDiscount();
});

</script>

<!-- Vendor libraries -->
<script type="text/javascript">
    var $form = $('#payment-form');
    $form.on('submit', payWithCard);

    /* If you're using Stripe for payments */
    function payWithCard(e) {
        e.preventDefault();

        /* Visual feedback */
        $form.find('[type=submit]').html('Processing <i class="fa fa-spinner fa-pulse"></i>')
            .prop('disabled', true);

        jQuery.ajax({
            url: '<?php echo base_url('billing/process_card') ?>',
            type: 'POST',
            data: $('#payment-form').serialize() + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function (data) {
                $form.find('[type=submit]').html('Payment successful <i class="fa fa-check"></i>').prop('disabled', true);
                $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                $("html, body").animate({scrollTop: $('#notify').offset().top}, 1000);
            },
            error: function () {
                $form.find('[type=submit]').html('There was a problem').removeClass('success').addClass('error');
                /* Show Stripe errors on the form */
                $form.find('.payment-errors').text('Try refreshing the page and trying again.');
                $form.find('.payment-errors').closest('.row').show();
                $form.find('[type=submit]').html('Error! <i class="fa fa-exclamation-circle"></i>')
                    .prop('disabled', true);
                $("#notify .message").html("<strong>Error</strong>: Please try again!");
            }
        });
    }


    $('#v2_categories').change(function () {
        var whr = $('#v2_warehouses option:selected').val();
        var cat = $('#v2_categories option:selected').val();
        $.ajax({
            type: "POST",
            url: baseurl + 'search_products/v2_pos_search',
            data: 'wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash,
            beforeSend: function () {
                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
            },
            success: function (data) {

                $("#pos_item").html(data);

            }
        });
    });
    $('#v2_warehouses').change(function () {
        var whr = $('#v2_warehouses option:selected').val();
        var cat = $('#v2_categories option:selected').val();
        $.ajax({
            type: "POST",
            url: baseurl + 'search_products/v2_pos_search',
            data: 'wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash,
            beforeSend: function () {
                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
            },
            success: function (data) {

                $("#pos_item").html(data);

            }
        });
    })
    $(document).ready(function () {

        // if (localStorage.bar_only && localStorage.bar_only != '') {
        //     $('#bar_only').attr('checked', 'checked');

        // } else {
        //     $('#bar_only').removeAttr('checked');
        // }
        // 1. Restore the warehouse and category selections from localStorage on page load
    var savedWarehouse = localStorage.getItem('selectedWarehouse');
    var savedCategory = localStorage.getItem('selectedCategory');

    if (savedWarehouse) {
        $('#v2_warehouses').val(savedWarehouse).change();
    }

    if (savedCategory) {
        $('#v2_categories').val(savedCategory).change();
    }

    // 2. Save the warehouse selection to localStorage
    $('#v2_warehouses').on('change', function () {
        localStorage.setItem('selectedWarehouse', $(this).val());
    });

    // 3. Save the category selection to localStorage
    $('#v2_categories').on('change', function () {
        localStorage.setItem('selectedCategory', $(this).val());
    });

    // 4. Restore the state of the barcode checkbox
    if (localStorage.bar_only && localStorage.bar_only != '') {
        $('#bar_only').attr('checked', 'checked');
    } else {
        $('#bar_only').removeAttr('checked');
    }

        $('#bar_only').click(function () {

            if ($('#bar_only').is(':checked')) {

                localStorage.bar_only = $('#bar_only').val();
            } else {
                localStorage.bar_only = '';
            }
            $('#v2_search_bar').attr('readonly', false);
        });

        Mousetrap.bind('alt+x', function () {
            $('#v2_search_bar').focus();
        });
        Mousetrap.bind('alt+c', function () {
            $('#pos-customer-box').focus();
        });

        Mousetrap.bind('alt+z', function () {
            $('.possubmit2').click();
        });
        Mousetrap.bind('alt+n', function () {
            window.location.href = "<?=base_url('pos_invoices/create') ?>";
        });
        Mousetrap.bind('alt+q', function () {
            $('#posp0').click();
            $('#v2_search_bar').val('');
        });
        Mousetrap.bind('alt+s', function () {
            if ($('#basicPay').hasClass('show')) {
                $('#pos_basic_print').click();
            } else {
                $('.possubmit3').click();
            }

        });

        function getIdFromBarcode (barcode){
            // alert('here');

        }


        $('#v2_search_bar').keyup(function (event) {
            
           
            // alert('here');
           
            // var globalPid = null;

            if (!$(this).attr('readonly')) {
                //$('#v2_search_bar').keyup(function () {
                // alert('hello');
                var whr = $('#v2_warehouses option:selected').val();
                var cat = $('#v2_categories option:selected').val();
                if (this.value.length === 0 || this.value.length > 2) {
                    $.ajax({
                        type: "POST",
                        url: baseurl + 'search_products/v2_pos_search',
                        data: 'name=' + $(this).val() + '&wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
                        beforeSend: function () {
                            $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
                        },
                        success: function (data) {
                            $("#pos_item").html(data);

                        }
                    });

                }
            }

            if (event.keyCode == 13 && !$('#v2_search_bar').attr('readonly')) {
                
                // alert('here')
                $('#v2_search_bar').attr('readonly', true);
                var barcode = $('#v2_search_bar').val().trim();

    if (barcode.length === 13) {
        barcode = barcode.substring(0, 12); 
        $(this).val(barcode); 
    }


        // if (barcode.length === 13) {
        //     $(this).val(barcode);  // Update input value with the 13-digit barcode
        // } else if (barcode.length === 12) {
        //     $(this).val(barcode);  // Update input value with the 12-digit barcode
        // }

                $.ajax({
                    type: "POST",
                    url: baseurl + 'search_products/get_id_from_barcode',
                    data: {name: barcode, 
                        warehouse: whr  // Send warehouse with barcode
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Product data from barcode:', data);
                        
                        // Check if we received an error response
                        if (data.error) {
                            console.error("Error: " + data.error);
                            $('#v2_search_bar').attr('readonly', false);
                            $('#v2_search_bar').val('');
                            return;
                        }
                        
                        // Also update product grid to show the product (useful for multiple scans)
                        $.ajax({
                            type: "POST",
                            url: baseurl + 'search_products/v2_pos_search',
                            data: 'name=' + barcode + '&wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
                            beforeSend: function () {
                                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
                            },
                            success: function (gridData) {
                                $("#pos_item").html(gridData);
                            }
                        });
                        
                        // DIRECTLY ADD TO CART WITHOUT SIMULATING CLICK
                        var flag = true;
                        var custom_discount = accounting.unformat($('#custom_discount').val(), accounting.settings.number.decimal);
                        var discount = data.disrate;
                        if (custom_discount > 0) discount = accounting.formatNumber(custom_discount);
                        
                        // Check if product already exists in cart (by pid and batch if applicable)
                        $('.pdIn').each(function () {
                            var existingPid = $(this).val();
                            var pi = $(this).attr('id').split('-')[1];
                            var existingBatchId = $('#batchid-' + pi).val() || '';
                            
                            var dataBatchId = data.has_batches ? data.batch_id : '';
                            
                            if (data.pid == existingPid && (dataBatchId == existingBatchId || (!data.has_batches && !existingBatchId))) {
                                // Same product & batch found — increment quantity
                                var stotal = accounting.unformat($('#amount-' + pi).val(), accounting.settings.number.decimal) + 1;
                                
                                if (stotal <= accounting.unformat(data.stock, accounting.settings.number.decimal)) {
                                    $('#amount-' + pi).val(accounting.formatNumber(stotal));
                                } else {
                                    $('#stock_alert').modal('toggle');
                                }
                                
                                rowTotal(pi);
                                billUpyog();
                                
                                flag = false;
                                return false; // Break out of each loop
                            }
                        });
                        
                        // Add new item to cart if not found
                        if (flag) {
                            var sound = document.getElementById("beep");
                            if(sound) sound.play();
                            
                            var ganak = $('#ganak').val();
                            var cvalue = parseInt(ganak);
                            var functionNum = "'" + cvalue + "'";
                            
                            var batchidInput = '';
                            var batchDisplay = '';
                            if (data.has_batches && data.batch_no) {
                                batchidInput = '<input type="hidden" name="batchid[]" id="batchid-' + cvalue + '" value="' + data.batch_id + '">';
                                batchDisplay = ' (' + data.batch_no + ')';
                            }
                            
                            var productDisplayName = data.product_name.replace(/ \([^)]*\).*/, '') + batchDisplay;
                            
                            var taxRate = data.taxrate;
                            if ($("#taxformat option:selected").attr('data-trate')) {
                                taxRate = $("#taxformat option:selected").attr('data-trate');
                            }
                            
                            var data_html = `
                            <div class="row m-0 pt-1 pb-1 border-bottom" id="ppid-${cvalue}">
                                <div class="col-6">
                                    <span class="quantity">
                                        <input type="text" class="form-control req amnt display-inline mousetrap" name="product_qty[]" inputmode="numeric" id="amount-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="1" >
                                        <div class="quantity-nav">
                                            <div class="quantity-button quantity-up">+</div>
                                            <div class="quantity-button quantity-down">-</div>
                                        </div>
                                    </span>
                                    ${productDisplayName}
                                </div>
                                <div class="col-3"> ${data.product_price} </div>
                                <div class="col-3">
                                    <strong><span class="ttlText" id="result-${cvalue}">0</span></strong>
                                    <a data-rowid="${cvalue}" class="red removeItem" title="Remove"><i class="fa fa-trash"></i></a>
                                </div>
                                <input type="hidden" class="form-control text-center" name="product_name[]" id="productname-${cvalue}" value="${productDisplayName}">
                                <input type="hidden" id="alert-${cvalue}" value="${data.stock}" name="alert[]">
                                <input type="hidden" class="form-control req prc" name="product_price[]" id="price-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${data.product_price}" inputmode="numeric">
                                <input type="hidden" class="form-control vat" name="product_tax[]" id="vat-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${taxRate}">
                                <input type="hidden" class="form-control discount pos_w" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-${cvalue}" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${discount}">
                                <input type="hidden" name="taxa[]" id="taxa-${cvalue}" value="0">
                                <input type="hidden" name="disca[]" id="disca-${cvalue}" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${cvalue}" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-${cvalue}" value="${data.pid}">
                                <input type="hidden" name="unit[]" id="unit-${cvalue}" value="${data.unit}">
                                <input type="hidden" name="hsn[]" id="hsn-${cvalue}" value="${data.product_code}">
                                <input type="hidden" name="serial[]" id="serial-${cvalue}" value="">
                                ${batchidInput}
                            </div>`;
                            
                            $('#pos_items').append(data_html);
                            rowTotal(cvalue);
                            billUpyog();
                            $('#ganak').val(cvalue + 1);
                        }
                        
                        // Reset search input for next scan
                        $('#v2_search_bar').attr('readonly', false);
                        $('#v2_search_bar').val('');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                        $('#v2_search_bar').attr('readonly', false);
                        $('#v2_search_bar').val('');
                    }
                });


              


            }
        });
    });

    // Handle product clicks - check for variations
    $(document).on('click', '.v2_select_pos_item, .v2_select_pos_item_bar', function(e) {
        var hasVariations = $(this).attr('data-has-variations');
        
        if (hasVariations == '1' || hasVariations == 1) {
            // Prevent default and show variations modal
            e.preventDefault();
            e.stopImmediatePropagation();
            
            var pid = $(this).attr('data-pid');
            var wid = $('#v2_warehouses option:selected').val();

            console.log('Showing variations popup for product:', pid);
            $.ajax({
                type: "POST",
                url: baseurl + 'search_products/get_product_variations_popup',
                data: 'pid=' + pid + '&wid=' + wid + '&' + crsf_token + '=' + crsf_hash,
                success: function(data) {
                    $('#variationsContent').html(data);
                    $('#productVariationsModal').modal('show');
                }
            });
            return false; // Prevent any other click handlers
        }
        // For products without variations, allow normal click behavior to proceed
    });

    // Handle variation selection
    $(document).on('click', '.select-variation', function() {
        var data = {
            product_name: $(this).data('name'),
            product_price: $(this).data('price'),
            taxrate: $(this).data('tax'),
            disrate: $(this).data('discount'),
            product_code: $(this).data('pcode'),
            pid: $(this).data('pid'),
            stock: $(this).data('stock'),
            unit: $(this).data('unit'),
            batch_id: $(this).data('batchid'),
            batch_no: $(this).data('batchno'),
            expiry_date: $(this).data('expiry'),
            has_batches: true
        };

        // Add the selected variation to cart (similar to barcode scanning logic)
        var flag = true;
        var custom_discount = accounting.unformat($('#custom_discount').val(), accounting.settings.number.decimal);
        var discount = data.disrate;
        if (custom_discount > 0) discount = accounting.formatNumber(custom_discount);
        
        // Check if product already exists in cart (by pid and batch)
        $('.pdIn').each(function () {
            var existingPid = $(this).val();
            var pi = $(this).attr('id').split('-')[1];
            var existingBatchId = $('#batchid-' + pi).val() || '';
            
            if (data.pid == existingPid && data.batch_id == existingBatchId) {
                // Same product & batch found — increment quantity
                var stotal = accounting.unformat($('#amount-' + pi).val(), accounting.settings.number.decimal) + 1;
                
                if (stotal <= accounting.unformat(data.stock, accounting.settings.number.decimal)) {
                    $('#amount-' + pi).val(accounting.formatNumber(stotal));
                } else {
                    $('#stock_alert').modal('toggle');
                }
                
                rowTotal(pi);
                billUpyog();
                
                flag = false;
                return false; // Break out of each loop
            }
        });
        
        // Add new item to cart if not found
        if (flag) {
            var sound = document.getElementById("beep");
            if(sound) sound.play();
            
            var ganak = $('#ganak').val();
            var cvalue = parseInt(ganak);
            var functionNum = "'" + cvalue + "'";
            
            var batchidInput = data.batch_id ? '<input type="hidden" name="batchid[]" id="batchid-' + cvalue + '" value="' + data.batch_id + '">' : '';
            var batchDisplay = data.batch_no ? ' (' + data.batch_no + ')' : '';
            
            var productDisplayName = data.product_name.replace(/ \([^)]*\).*/, '') + batchDisplay;
            
            var taxRate = data.taxrate;
            if ($("#taxformat option:selected").attr('data-trate')) {
                taxRate = $("#taxformat option:selected").attr('data-trate');
            }
            
            var data_html = `
            <div class="row m-0 pt-1 pb-1 border-bottom" id="ppid-${cvalue}">
                <div class="col-6">
                    <span class="quantity">
                        <input type="text" class="form-control req amnt display-inline mousetrap" name="product_qty[]" inputmode="numeric" id="amount-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="1" >
                        <div class="quantity-nav">
                            <div class="quantity-button quantity-up">+</div>
                            <div class="quantity-button quantity-down">-</div>
                        </div>
                    </span>
                    ${productDisplayName}
                </div>
                <div class="col-3"> ${data.product_price} </div>
                <div class="col-3">
                    <strong><span class="ttlText" id="result-${cvalue}">0</span></strong>
                    <a data-rowid="${cvalue}" class="red removeItem" title="Remove"><i class="fa fa-trash"></i></a>
                </div>
                <input type="hidden" class="form-control text-center" name="product_name[]" id="productname-${cvalue}" value="${productDisplayName}">
                <input type="hidden" id="alert-${cvalue}" value="${data.stock}" name="alert[]">
                <input type="hidden" class="form-control req prc" name="product_price[]" id="price-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${data.product_price}" inputmode="numeric">
                <input type="hidden" class="form-control vat" name="product_tax[]" id="vat-${cvalue}" onkeypress="return isNumber(event)" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${taxRate}">
                <input type="hidden" class="form-control discount pos_w" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-${cvalue}" onkeyup="rowTotal(${functionNum}), billUpyog()" autocomplete="off" value="${discount}">
                <input type="hidden" name="taxa[]" id="taxa-${cvalue}" value="0">
                <input type="hidden" name="disca[]" id="disca-${cvalue}" value="0">
                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${cvalue}" value="0">
                <input type="hidden" class="pdIn" name="pid[]" id="pid-${cvalue}" value="${data.pid}">
                <input type="hidden" name="unit[]" id="unit-${cvalue}" value="${data.unit}">
                <input type="hidden" name="hsn[]" id="hsn-${cvalue}" value="${data.product_code}">
                <input type="hidden" name="serial[]" id="serial-${cvalue}" value="">
                ${batchidInput}
            </div>`;
            
            $('#pos_items').append(data_html);
            rowTotal(cvalue);
            billUpyog();
            $('#ganak').val(cvalue + 1);
        }
        
        // Close the modal
        $('#productVariationsModal').modal('hide');
    });

    // Extra discount events
    $('#extra_discount_type').on('change', function() {
        updateExtraDiscount();
    });
    $('.discVal').on('input', function() {
        updateExtraDiscount();
    });

</script>

<!-- Product Variations Modal -->
<div class="modal fade" id="productVariationsModal" tabindex="-1" role="dialog" aria-labelledby="productVariationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h6 class="modal-title font-weight-bold" id="productVariationsModalLabel">
                    <i class="fa fa-list-ul mr-2"></i>Select Product Variation
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="variationsContent" style="max-height: 400px; overflow-y: auto;">
                <!-- Variations will be loaded here -->
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    // Initialize Nepali Datepicker on all datepickers
    $('[data-toggle="datepicker"]').each(function () {
        var $input = $(this);
        var inputId = $input.attr('id');
        if (!inputId) return; // Skip if no ID

        // Destroy if already initialized
        if ($input.data('NepaliDatePicker')) {
            $input.nepaliDatePicker('destroy');
        }

        // Initialize with toggle
        $input.nepaliDatePicker({
            language: 'en',
            dateFormat: 'YYYY-MM-DD',
            ndpEnglishInput: '#' + inputId,
            closeOnDateSelect: true
        });
    });
});
</script>


