<div class="content-body">
    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div class="card-body">
                <!-- Custom CSS for batch dropdown -->
                <style>
                    /* Ensure batch dropdowns have proper width */
                    .batch_select {
                        min-width: 200px !important;
                        width: 100% !important;
                        max-width: 100% !important;
                    }
                    
                   
                    .batch_select option {
                        padding: 8px;
                        font-size: 14px;
                    }
                    
                    /* Custom styling for selected option */
                    .batch_select option:checked {
                        font-weight: bold;
                        background-color: #f0f0f0;
                    }
                    
                    /* Add more padding for the dropdown itself */
                    .batch_select {
                        padding: 6px 8px;
                        height: auto !important;
                        white-space: normal;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    }
                    
                    /* Style for when dropdown is focused */
                    .batch_select:focus {
                        border-color: #80bdff;
                        outline: 0;
                        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                    }
                    
                    /* Style for selected batch class */
                    .selected-batch {
                    }
                    
                    /* When a value is selected, make it more prominent */
                    .batch_select.has-value {
                        font-weight: 600;
                        color: #495057;
                        border-color: #80bdff;
                    }
                    
                    /* Custom styling for the batch select container */
                    .batch-select-container {
                        position: relative;
                        width: 100%;
                    }
                </style>
                <form method="post" id="data_form">


                    <div class="row">

                        <div class="col-sm-4">

                        </div>

                        <div class="col-sm-3"></div>

                        <div class="col-sm-2"></div>

                        <div class="col-sm-3">

                        </div>

                    </div>

                    <div class="row">


                        <div class="col-sm-6 cmp-pnl">
                            <div id="customerpanel" class="inner-cmp-pnl">
                                <div class="form-group row">
                                    <div class="fcol-sm-12">
                                        <h3 class="title">
                                            <?php echo $this->lang->line('Bill From') ?> <a href='#'
                                                                                            class="btn btn-primary btn-sm rounded"
                                                                                            data-toggle="modal"
                                                                                            data-target="#addCustomer">
                                                <?php echo $this->lang->line('Add Supplier') ?>
                                            </a>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Supplier') ?> </label>
                                        <input type="text" class="form-control" name="cst" id="supplier-box"
                                               placeholder="Enter Supplier Name or Mobile Number to search"
                                               autocomplete="off"/>

                                        <div id="supplier-box-result"></div>
                                    </div>

                                </div>
                                <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Supplier Details') ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                                        <div id="customer_name"></div>
                                    </div>
                                    <div class="clientinfo">

                                        <div id="customer_address1"></div>
                                    </div>

                                    <div class="clientinfo">

                                        <div type="text" id="customer_phone"></div>
                                    </div>
                                    <hr><?php echo $this->lang->line('Warehouse') ?> <select id="s_warehouses"
                                                                                             class="selectpicker form-control">
                                        <?php echo $this->common->default_warehouse();
                                        echo '<option value="0">' . $this->lang->line('All') ?></option><?php foreach ($warehouse as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>

                                    </select>
                                </div>


                            </div>
                        </div>
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">


                                <div class="form-group row">

                                    <div class="col-sm-12"><h3
                                                class="title"><?php echo $this->lang->line('Purchase Order') ?> </h3>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Order Number') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Invoice #"
                                                   name="invocieno"
                                                   value="<?php echo $lastinvoice + 1 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Reference') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Reference #"
                                                   name="refer">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-sm-6"><label for="invociedate"
                                                                 class="caption"><?php echo $this->lang->line('Order Date') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar4"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required"
                                                   placeholder="Billing Date" name="invoicedate"
                                                   value="<?php echo date('d/m/Y'); ?>"
                                                   autocomplete="false">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieduedate"
                                                                 class="caption"><?php echo $this->lang->line('Order Due Date') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required" id="tsn_due"
                                                   name="invocieduedate"
                                                   placeholder="Due Date" 
                                                   value="<?php echo date('d/m/Y', strtotime('+30 days')); ?>"
                                                   autocomplete="false">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="taxformat"
                                               class="caption"><?php echo $this->lang->line('Tax') ?> </label>
                                        <select class="form-control round"
                                                onchange="changeTaxFormat(this.value)"
                                                id="taxformat">
                                            <?php echo $taxlist; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">

                                        <div class="form-group">
                                            <label for="discountFormat"
                                                   class="caption"><?php echo $this->lang->line('Discount') ?></label>
                                            <select class="form-control" onchange="changeDiscountFormat(this.value)"
                                                    id="discountFormat">
                                                <?php echo $this->common->disclist() ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                               <div class="form-group row">
    <label for="notes" class="col-sm-2 col-form-label"><?php echo $this->lang->line('Order Note') ?></label>
    <div class="col-sm-10">
        <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
    </div>
</div>

                            </div>
                        </div>

                    </div>


                    <div id="saman-row" style="margin-top: 30px; margin-bottom: 30px;">
                        <!-- Custom CSS for horizontal scrollable table -->
                        <style>
                        .horizontal-scroll-wrapper {
                            overflow-x: auto;
                            overflow-y: hidden;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            background: #fff;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            margin: 20px 0;
                        }

                        .horizontal-scroll-wrapper::-webkit-scrollbar {
                            height: 8px;
                        }

                        .horizontal-scroll-wrapper::-webkit-scrollbar-track {
                            background: #f1f1f1;
                            border-radius: 4px;
                        }

                        .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
                            background: #c1c1c1;
                            border-radius: 4px;
                        }

                        .horizontal-scroll-wrapper::-webkit-scrollbar-thumb:hover {
                            background: #a8a8a8;
                        }

                        .horizontal-scroll-wrapper .tfr.my_stripe {
                            min-width: 1600px; /* Increased width for more space */
                            margin: 0;
                            border: none;
                        }

                        .scroll-hint {
                            text-align: center;
                            padding: 8px;
                            background: #f8f9fa;
                            color: #666;
                            font-size: 12px;
                            border-top: 1px solid #ddd;
                        }

                        /* Improved spacing for all input fields */
                        .tfr input[type="text"], .tfr input[type="date"] {
                            min-width: 120px;
                            padding: 10px 12px; /* Increased padding */
                            margin: 3px 0;
                            border-radius: 4px;
                            border: 1px solid #ddd;
                            font-size: 14px;
                            height: 40px; /* Fixed height for consistency */
                        }

                        .tfr input:focus {
                            border-color: #007bff;
                            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
                            outline: none;
                        }

                        .tfr td {
                            padding: 15px 10px; /* Increased padding for more space */
                            vertical-align: middle;
                        }

                        .tfr th {
                            padding: 18px 10px; /* Increased header padding */
                            font-weight: 600;
                            font-size: 13px;
                        }

                        /* Batch fields specific styling */
                        .tfr input.batch_no_input {
                            min-width: 140px;
                            border-left: 3px solid #007bff;
                        }

                        .tfr input.expiry_date {
                            min-width: 140px;
                        }

                        /* Profit fields styling */
                        .tfr input.profit, .tfr input.profit_margin {
                            min-width: 130px;
                            font-weight: bold;
                            color: #2e7d32;
                        }

                        .tfr input.selling_price {
                            min-width: 130px;
                            font-weight: bold;
                            color: #f57c00;
                        }

                        /* Amount column styling */
                        .tfr td:last-child {
                            font-weight: bold;
                        }

                        .tfr .ttlText {
                            font-size: 16px;
                            color: #007bff;
                        }

                        /* Description textarea styling */
                        .tfr textarea {
                            width: 100%;
                            min-height: 100px; /* Increased height */
                            padding: 12px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            resize: vertical;
                            margin: 8px 0;
                            font-size: 14px;
                        }

                        /* Add row button styling */
                        .add-row .btn {
                            padding: 10px 20px;
                            font-size: 14px;
                        }

                        .tfr tbody tr {
                            line-height: 2.2; /* Increased line height for more space */
                            margin-bottom: 8px; /* Add space between rows */
                        }

                        .tfr tbody tr:hover {
                            background-color: #f8f9fa;
                        }
                        </style>
                        
                        <div class="horizontal-scroll-wrapper">
                            <table class="tfr my_stripe">
                            <thead>

                            <tr class="item_header bg-gradient-directional-amber">
                                <th width="35%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="12%" class="text-center">Batch No.</th>       
                                <th width="12%" class="text-center">Expiry Date</th>     
                                <th width="8%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
                                <th width="12%" class="text-center"><?php echo $this->lang->line('Rate') ?></th>
                                <th width="12%" class="text-center">Selling Price</th>
                                <th width="12%" class="text-center">Profit</th>
                                <th width="12%" class="text-center">Profit Margin (%)</th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Tax') ?>(%)</th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Tax') ?></th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Discount') ?></th>
                                <th width="12%" class="text-center">
                                    <?php echo $this->lang->line('Amount') ?>
                                    (<?php echo $this->config->item('currency'); ?>)
                                </th>
                                <!-- <th width="5%" class="text-center"><?php echo $this->lang->line('Action') ?></th> -->
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" class="form-control text-center" name="product_name[]"
                                           placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                                           id='productname-0'>
                                </td>
                                 <td>
        <input type="text" class="form-control batch_no_input" name="batch_no[]" id="batch_no-0" placeholder="Batch No">
    </td>

    <td>
        <input type="date" class="form-control expiry_date" name="expiry_date[]" id="expiry_date-0">
    </td>

                                <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off" value="1"></td>
                                <td><input type="text" class="form-control req prc" name="product_price[]" id="price-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off"></td>
                                <td><input type="text" class="form-control selling_price" name="selling_price[]" id="selling_price-0" placeholder="Selling Price" style="min-width: 120px;"></td>
                                <td><input type="text" class="form-control profit" name="profit[]" id="profit-0" placeholder="Profit" readonly style="background:#f8f9fa; color:#333; min-width: 120px; font-weight: bold;"></td>
                                <td><input type="text" class="form-control profit_margin" name="profit_margin[]" id="profit_margin-0" placeholder="Profit %" readonly style="background:#f8f9fa; color:#333; min-width: 120px; font-weight: bold;"></td>
                                <td><input type="text" class="form-control vat " name="product_tax[]" id="vat-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off" style="min-width: 100px;"></td>
                                <td class="text-center" id="texttaxa-0">0</td>
                                <td><input type="text" class="form-control discount" name="product_discount[]"
                                           onkeypress="return isNumber(event)" id="discount-0"
                                           onkeyup="rowTotal('0'), billUpyog()" autocomplete="off" style="min-width: 100px;"></td>
                                <td><span class="currenty"><?php echo $this->config->item('currency'); ?></span>
                                    <strong><span class='ttlText' id="result-0">0</span></strong></td>
                                <td class="text-center">

                                </td>
                                <input type="hidden" name="taxa[]" id="taxa-0" value="0">
                                <input type="hidden" name="disca[]" id="disca-0" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                <input type="hidden" name="product_description[]" id="product_description-0" value="">
                                <input type="hidden" name="unit[]" id="unit-0" value=""><input type="hidden"
                                                                                               name="hsn[]" id="hsn-0"
                                                                                               value="">
                            </tr>


                            <tr class="last-item-row">
                                <td class="add-row">
                                    <button type="button" class="btn btn-success" aria-label="Left Align"
                                            id="addproduct">
                                        <i class="fa fa-plus-square"></i> <?php echo $this->lang->line('Add Row') ?>
                                    </button>
                                </td>
                                <td colspan="7"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>

                        <!-- Summary Section (outside scroll wrapper) -->
                        <table class="table-responsive tfr my_stripe" style="margin-top: 0;">
                            <tbody>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="10"><input type="hidden" value="0" id="subttlform"
                                                                     name="subtotal"><strong><?php echo $this->lang->line('Total Tax') ?></strong>
                                </td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="taxr" class="lightMode">0</span></td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" align="right">
                                    <strong><?php echo $this->lang->line('Total Discount') ?></strong></td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="discs" class="lightMode">0</span></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" align="right">
                                    <strong><?php echo $this->lang->line('Shipping') ?></strong></td>
                                <td align="left" colspan="2"><input type="text" class="form-control shipVal"
                                                                    onkeypress="return isNumber(event)"
                                                                    placeholder="Value"
                                                                    name="shipping" autocomplete="off"
                                                                    onkeyup="billUpyog();">
                                    ( <?php echo $this->lang->line('Tax') ?> <?= $this->config->item('currency'); ?>
                                    <span id="ship_final">0</span> )
                                </td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"><?php if ($exchange['active'] == 1){
                                    echo $this->lang->line('Payment Currency client') . ' <small>' . $this->lang->line('based on live market') ?></small>
                                    <select name="mcurrency"
                                            class="selectpicker form-control">
                                        <option value="0">Default</option>
                                        <?php foreach ($currency as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['symbol'] . ' (' . $row['code'] . ')</option>';
                                        } ?>

                                    </select><?php } ?></td>
                                <td colspan="4" align="right"><strong><?php echo $this->lang->line('Grand Total') ?>
                                        (<span
                                                class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>)</strong>
                                </td>
                                <td align="left" colspan="2"><input type="text" name="total" class="form-control"
                                                                    id="invoiceyoghtml" readonly="">

                                </td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"><?php echo $this->lang->line('Payment Terms') ?> <select name="pterms"
                                                                                                         class="selectpicker form-control"><?php foreach ($terms as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>

                                    </select></td>
                                <td colspan="2">
                                    <div>
                                        <label><?php echo $this->lang->line('Update Stock') ?></label>
                                        <fieldset class="right-radio">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="update_stock"
                                                       id="customRadioRight1" value="yes" checked="">
                                                <label class="custom-control-label"
                                                       for="customRadioRight1"><?php echo $this->lang->line('Yes') ?></label>
                                            </div>
                                        </fieldset>
                                        <fieldset class="right-radio">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="update_stock"
                                                       id="customRadioRight2" value="no">
                                                <label class="custom-control-label"
                                                       for="customRadioRight2"><?php echo $this->lang->line('No') ?></label>
                                            </div>
                                        </fieldset>

                                    </div>
                                </td>
                                <td align="right" colspan="4"><input type="submit" class="btn btn-success sub-btn"
                                                                     value="<?php echo $this->lang->line('Generate Order') ?>"
                                                                     id="submit-data" data-loading-text="Creating...">

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>

                    <input type="hidden" value="purchase/action" id="action-url">
                    <input type="hidden" value="puchase_search" id="billtype">
                    <input type="hidden" value="purchase_order" id="page-type">
                    <input type="hidden" value="0" name="counter" id="ganak">
                    <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">
                    <input type="hidden" value="<?= $taxdetails['handle']; ?>" name="taxformat" id="tax_format">

                    <input type="hidden" value="<?= $taxdetails['format']; ?>" name="tax_handle" id="tax_status">
                    <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">


                    <input type="hidden" value="<?= $this->common->disc_status()['disc_format']; ?>"
                           name="discountFormat" id="discount_format">
                    <input type="hidden" value="<?= amountFormat_general($this->common->disc_status()['ship_rate']); ?>"
                           name="shipRate"
                           id="ship_rate">
                    <input type="hidden" value="<?= $this->common->disc_status()['ship_tax']; ?>" name="ship_taxtype"
                           id="ship_taxtype">
                    <input type="hidden" value="0" name="ship_tax" id="ship_tax">

                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addCustomer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="product_action" class="form-horizontal">
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-directional-success white">

                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('Add Supplier') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">


                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="name"><?php echo $this->lang->line('Name') ?></label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Name"
                                   class="form-control margin-bottom" id="mcustomer_name" name="name" required>
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

                        <label class="col-sm-2 col-form-label" for="email">Email</label>

                        <div class="col-sm-10">
                            <input type="email" placeholder="Email"
                                   class="form-control margin-bottom crequired" name="email" id="mcustomer_email">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="address"><?php echo $this->lang->line('Address') ?></label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Address"
                                   class="form-control margin-bottom " name="address" id="mcustomer_address1">
                        </div>
                    </div>
                    <div class="form-group row">


                        <div class="col-sm-4">
                            <input type="text" placeholder="City"
                                   class="form-control margin-bottom" name="city" id="mcustomer_city">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Region"
                                   class="form-control margin-bottom" name="region">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Country"
                                   class="form-control margin-bottom" name="country" id="mcustomer_country">
                        </div>

                    </div>

                    <div class="form-group row">


                        <div class="col-sm-6">
                            <input type="text" placeholder="PostBox"
                                   class="form-control margin-bottom" name="postbox">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="TAX ID"
                                   class="form-control margin-bottom" name="taxid" id="tax_id">
                        </div>
                    </div>


                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                    <input type="submit" id="msupplier_add" class="btn btn-primary submitBtn"
                           value="<?php echo $this->lang->line('ADD') ?>"/>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom CSS for batch dropdown -->
<style>
    /* Ensure batch dropdowns have proper width */
    .batch_select {
        min-width: 200px !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* Improve readability of selected batch text */
    .batch_select option {
        padding: 8px;
        font-size: 14px;
    }
    
    /* Custom styling for selected option */
    .batch_select option:checked {
        font-weight: bold;
        background-color: #f0f0f0;
    }
    
    /* Add more padding for the dropdown itself */
    .batch_select {
        padding: 6px 8px;
        height: auto !important;
        white-space: normal;
    }
    
    /* Highlight selected batch in the dropdown */
    .batch_select option.selected-batch {
        background-color: #007bff;
        color: white;
    }
</style>

<script>
// Function to get the next incremented batch number based on existing rows
function getNextIncrementedBatchNo() {
    var maxBatchNo = 0;
    var batchPrefix = 'B';
    
    // Check all existing batch numbers in the current form
    $('[id^="batch_no-"]').each(function() {
        var batchVal = $(this).val().trim();
        if (batchVal && batchVal.match(/^B\d+$/)) {
            var batchNum = parseInt(batchVal.substring(1));
            if (batchNum > maxBatchNo) {
                maxBatchNo = batchNum;
            }
        }
    });
    
    // If we found batch numbers in the form, increment from the highest
    if (maxBatchNo > 0) {
        var nextNum = maxBatchNo + 1;
        return batchPrefix + String(nextNum).padStart(5, '0');
    }
    
    // Otherwise, fetch from server
    return null;
}

$(document).on('focus', '.batch_no_input, [id^="batch_no-"]', function() {
    var $input = $(this);
    if ($input.val().trim() === '') {
        // First try to get incremented batch number from existing form data
        var incrementedBatch = getNextIncrementedBatchNo();
        
        if (incrementedBatch) {
            $input.val(incrementedBatch);
        } else {
            // Fallback to server if no batches exist in form yet
            if (typeof baseurl !== 'undefined') {
                $.getJSON(baseurl + 'purchase/get_next_batch_no', function(data) {
                    if (data.batch_no) {
                        $input.val(data.batch_no);
                    }
                });
            }
        }
    }
});

// Function to auto-calculate profit and profit margin for each row
function calculateProfitAndMargin(rowIdx) {
    var rate = parseFloat($("#price-" + rowIdx).val()) || 0;
    var sp = parseFloat($("#selling_price-" + rowIdx).val()) || 0;
    
    // Calculate and display even if one value is 0 (but both fields have values)
    if ((rate >= 0 && sp >= 0) && (rate > 0 || sp > 0)) {
        var profit = sp - rate;
        var margin = rate > 0 ? ((profit / rate) * 100) : 0;
        
        $("#profit-" + rowIdx).val(profit.toFixed(2));
        $("#profit_margin-" + rowIdx).val(margin.toFixed(2));
        
        // Add visual feedback
        if (profit > 0) {
            $("#profit-" + rowIdx).css('color', '#6c757d'); 
            $("#profit_margin-" + rowIdx).css('color', '#6c757d');
        } else if (profit < 0) {
            $("#profit-" + rowIdx).css('color', '#dc3545'); // Red for negative profit
            $("#profit_margin-" + rowIdx).css('color', '#dc3545');
        } else {
            $("#profit-" + rowIdx).css('color', '#6c757d'); // Gray for zero profit
            $("#profit_margin-" + rowIdx).css('color', '#6c757d');
        }
    } else if ($("#price-" + rowIdx).val() === '' && $("#selling_price-" + rowIdx).val() === '') {
        // Clear fields only if both rate and selling price are completely empty
        $("#profit-" + rowIdx).val('');
        $("#profit_margin-" + rowIdx).val('');
        $("#profit-" + rowIdx).css('color', '#333');
        $("#profit_margin-" + rowIdx).css('color', '#333');
    }
}

// Attach event listeners for profit calculation
$(document).on('input keyup change', '[id^="price-"], [id^="selling_price-"]', function() {
    var id = $(this).attr('id');
    var rowIdx = id.split('-')[1];
    // Add a small delay to ensure all values are properly set
    setTimeout(function() {
        calculateProfitAndMargin(rowIdx);
    }, 50);
});

// Also trigger calculation when fields lose focus
$(document).on('blur', '[id^="price-"], [id^="selling_price-"]', function() {
    var id = $(this).attr('id');
    var rowIdx = id.split('-')[1];
    calculateProfitAndMargin(rowIdx);
});

// === Batch Fetch and Autofill Logic ===
// Helper: Get warehouse ID
function getSelectedWarehouseId() {
    return $("#s_warehouses").val() || 0;
}
// Helper: Get product ID from the row (assumes hidden input pid-<rowIdx>)
function getProductIdFromRow(rowIdx) {
    return $("#pid-" + rowIdx).val();
}
// Helper: Initialize batch logic for a row
function initBatchLogicForRow(rowIdx) {
    var productId = getProductIdFromRow(rowIdx);
    var warehouseId = getSelectedWarehouseId();
    var batchCell = $("#batch_no-" + rowIdx).closest('td');
    if (!productId) {
        // Clear batch fields
        $("#batch_no-" + rowIdx).val('').show();
        batchCell.find('.batch_select').remove();
        $("#expiry_date-" + rowIdx).val('');
        $("#selling_price-" + rowIdx).val('');
        $("#profit-" + rowIdx).val('');
        $("#profit_margin-" + rowIdx).val('');
        return;
    }
    // Fetch batches for this product and warehouse
    if (typeof baseurl !== 'undefined') {
        $.getJSON(baseurl + 'purchase/get_product_batches', { product_id: productId, warehouse_id: warehouseId }, function(resp) {
            batchCell.find('.batch_select').remove();
            if (resp.status === 'Success' && resp.batches.length > 0) {
                // Build batch dropdown
                var batchSelect = $('<select class="form-control batch_select" style="width: 100%;"></select>');
                batchSelect.append('<option value="">Select Batch</option>');
                $.each(resp.batches, function(i, batch) {
                    var label = batch.batch_no + ' (Exp: ' + (batch.expiry_date || '-') + ')';
                    batchSelect.append('<option value="' + batch.batch_no + '" data-expiry="' + batch.expiry_date + '" data-sp="' + batch.selling_price + '" data-profit="' + batch.profit + '" data-margin="' + batch.profit_margin + '">' + label + '</option>');
                });
                // Add option to create new batch
                batchSelect.append('<option value="__new__">+ Add New Batch</option>');
                $("#batch_no-" + rowIdx).hide();
                batchCell.append('<div class="batch-select-container"></div>');
                batchCell.find('.batch-select-container').append(batchSelect);
                // If only one batch, auto-select it
                if (resp.batches.length === 1) {
                    batchSelect.val(resp.batches[0].batch_no).trigger('change');
                    // Add has-value class to indicate a value is selected
                    batchSelect.addClass('has-value');
                }
                // When batch is selected, fill expiry and other fields
                batchSelect.on('change', function() {
                    var selected = $(this).find('option:selected');
                    if ($(this).val() === "__new__") {
                        // Show batch_no input and auto-generate
                        $(this).remove();
                        $("#batch_no-" + rowIdx).show().val("");
                        $("#expiry_date-" + rowIdx).val("");
                        $("#selling_price-" + rowIdx).val("");
                        $("#profit-" + rowIdx).val("");
                        $("#profit_margin-" + rowIdx).val("");
                        // Auto-generate batch number
                        var incrementedBatch = getNextIncrementedBatchNo();
                        if (incrementedBatch) {
                            $("#batch_no-" + rowIdx).val(incrementedBatch);
                        } else if (typeof baseurl !== 'undefined') {
                            $.getJSON(baseurl + 'purchase/get_next_batch_no', function(data) {
                                if (data.batch_no) {
                                    $("#batch_no-" + rowIdx).val(data.batch_no);
                                }
                            });
                        }
                    } else {
                        var expiry = selected.data('expiry') || '';
                        var sp = selected.data('sp') || '';
                        var profit = selected.data('profit') || '';
                        var margin = selected.data('margin') || '';
                        
                        console.log('Batch selected for row ' + rowIdx + ':', {
                            batch_no: $(this).val(),
                            expiry: expiry,
                            selling_price: sp,
                            profit: profit,
                            margin: margin
                        });
                        
                        $("#batch_no-" + rowIdx).val($(this).val());
                        $("#expiry_date-" + rowIdx).val(expiry);
                        $("#selling_price-" + rowIdx).val(sp);
                        $("#profit-" + rowIdx).val(profit);
                        $("#profit_margin-" + rowIdx).val(margin);
                        
                        // Protect the expiry date value from being overwritten
                        if (expiry && expiry !== 'null' && expiry !== '') {
                            $("#expiry_date-" + rowIdx).attr('data-protected-value', expiry);
                            // Ensure it's a date input type
                            $("#expiry_date-" + rowIdx).attr('type', 'date');
                            console.log('Protected batch expiry date for row', rowIdx, ':', expiry);
                        }
                        
                        // Debug: Log what was actually set
                        console.log('Set expiry_date-' + rowIdx + ' to:', expiry);
                        console.log('Actual field value after setting:', $("#expiry_date-" + rowIdx).val());
                        
                        // Trigger profit calculation to ensure values are properly displayed
                        setTimeout(function() {
                            calculateProfitAndMargin(rowIdx);
                            // Check if the value is still correct after calculation
                            console.log('Expiry field value after profit calculation:', $("#expiry_date-" + rowIdx).val());
                        }, 100);
                    }
                });
            } else {
                // No batches: show batch_no input and auto-generate as before
                $("#batch_no-" + rowIdx).show().val('');
                $("#expiry_date-" + rowIdx).val('');
                $("#selling_price-" + rowIdx).val('');
                $("#profit-" + rowIdx).val('');
                $("#profit_margin-" + rowIdx).val('');
                // Optionally, trigger auto-batch generation
                var incrementedBatch = getNextIncrementedBatchNo();
                if (incrementedBatch) {
                    $("#batch_no-" + rowIdx).val(incrementedBatch);
                } else if (typeof baseurl !== 'undefined') {
                    $.getJSON(baseurl + 'purchase/get_next_batch_no', function(data) {
                        if (data.batch_no) {
                            $("#batch_no-" + rowIdx).val(data.batch_no);
                        }
                    });
                }
            }
        });
    }
}

// When product is selected or changed (including autocomplete)
$(document).on('change', '[id^="productname-"]', function() {
    var id = $(this).attr('id');
    var rowIdx = id.split('-')[1];
    
    setTimeout(function() {
        initBatchLogicForRow(rowIdx);
    }, 100);
});
// Also trigger batch logic when pid is set directly (for autocomplete)
$(document).on('change', '[id^="pid-"]', function() {
    var id = $(this).attr('id');
    var rowIdx = id.split('-')[1];
    initBatchLogicForRow(rowIdx);
});
// When warehouse changes, re-initialize batch logic for all rows
$(document).on('change', '#s_warehouses', function() {
    $('[id^="productname-"]').each(function() {
        var id = $(this).attr('id');
        var rowIdx = id.split('-')[1];
        initBatchLogicForRow(rowIdx);
    });
});

// When a new row is added, initialize batch logic for that row 

$(document).on('focus', '[id^="productname-"]', function() {
    var id = $(this).attr('id');
    var rowIdx = id.split('-')[1];
    
    // Check if this row has been initialized already
    if (!$(this).data('batch-initialized')) {
        $(this).data('batch-initialized', true);
        setTimeout(function() {
            initBatchLogicForRow(rowIdx);
        }, 100);
    }
});

// Initialize any batch selects present on the page to improve appearance
function enhanceBatchSelects() {
    $('.batch_select').each(function() {
        // Add tooltip to show full text on hover
        $(this).attr('title', $(this).find('option:selected').text());
        
        // Highlight the selected option
        $(this).find('option:selected').addClass('selected-batch');
    });
}

// Call on document ready and after any batch select changes
$(document).ready(function() {
    enhanceBatchSelects();
});

// Add change handler for all batch selects
$(document).on('change', '.batch_select', function() {
    // Update the title attribute with selected text for tooltip
    $(this).attr('title', $(this).find('option:selected').text());
    
    // Highlight the selected option
    $(this).find('option').removeClass('selected-batch');
    $(this).find('option:selected').addClass('selected-batch');
    
    // Add or remove has-value class based on whether a value is selected
    if ($(this).val() && $(this).val() !== '') {
        $(this).addClass('has-value');
    } else {
        $(this).removeClass('has-value');
    }
});

// Add comprehensive error handling and debugging for form submission
$(document).ready(function() {
    console.log('Purchase form ready - debugging enabled');
    
    // OVERRIDE: Prevent Nepali calendar from initializing on expiry fields
    window.originalNepaliDatepicker = window.$ && $.fn.nepaliDatePicker;
    if (window.originalNepaliDatepicker) {
        $.fn.nepaliDatePicker = function(options) {
            // Check if this is being called on an expiry date field
            var isExpiryField = this.is('[name="expiry_date[]"]') || this.attr('id') && this.attr('id').includes('expiry_date');
            
            if (isExpiryField) {
                console.warn('Blocked Nepali calendar initialization on expiry field:', this.attr('id'));
                return this; // Return without initializing
            }
            
            // For other fields, use the original function
            return originalNepaliDatepicker.call(this, options);
        };
    }
    
    // Catch all JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
        console.error('File:', e.filename);
        console.error('Line:', e.lineno);
        // Don't show alert for Nepali calendar errors as they're expected
        if (!e.filename.includes('nepali.datepicker')) {
            alert('JavaScript Error: ' + e.message + '\nFile: ' + e.filename + '\nLine: ' + e.lineno);
        }
    });
    
    // Protect expiry date fields from Nepali calendar interference
    function protectExpiryFields() {
        $('[name="expiry_date[]"]').each(function() {
            var $field = $(this);
            var currentValue = $field.val();
            
            // If field has a valid date, protect it from being overwritten
            if (currentValue && currentValue !== 'english' && currentValue !== 'nepali' && currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                $field.attr('data-protected-value', currentValue);
                console.log('Protected expiry field:', $field.attr('id'), 'value:', currentValue);
            }
        });
    }
    
    // Monitor for any changes to expiry fields and restore protected values
    $(document).on('change keyup blur', '[name="expiry_date[]"]', function() {
        var $field = $(this);
        var currentValue = $field.val();
        var protectedValue = $field.attr('data-protected-value');
        
        // If the field value was changed to 'english' or 'nepali', restore the protected value
        if ((currentValue === 'english' || currentValue === 'nepali') && protectedValue) {
            console.warn('Restoring protected expiry value for', $field.attr('id'), 'from:', currentValue, 'to:', protectedValue);
            $field.val(protectedValue);
        }
    });
    
    // Run protection periodically to catch any interference
    setInterval(function() {
        $('[name="expiry_date[]"]').each(function() {
            var $field = $(this);
            var currentValue = $field.val();
            var protectedValue = $field.attr('data-protected-value');
            
            if ((currentValue === 'english' || currentValue === 'nepali') && protectedValue) {
                console.warn('Periodic restore of expiry field:', $field.attr('id'), 'from:', currentValue, 'to:', protectedValue);
                $field.val(protectedValue);
            }
        });
    }, 500);
    
    // Disable Nepali calendar on expiry date fields specifically
    $(document).on('focus', '[name="expiry_date[]"]', function() {
        var $field = $(this);
        
        // Remove any Nepali calendar classes or data attributes
        $field.removeClass('nepali-calendar english-calendar');
        $field.removeData('nepali-calendar');
        
        // Set the field type to date to use browser's native date picker
        if ($field.attr('type') !== 'date') {
            $field.attr('type', 'date');
        }
        
        console.log('Protected expiry field from calendar interference:', $field.attr('id'));
    });
    
    // Aggressive prevention of Nepali calendar initialization on expiry fields
    setInterval(function() {
        $('[name="expiry_date[]"]').each(function() {
            var $field = $(this);
            
            // Force remove any Nepali calendar initialization
            if ($field.hasClass('nepali-datepicker') || $field.data('nepali-calendar')) {
                console.warn('Removing Nepali calendar from expiry field:', $field.attr('id'));
                $field.removeClass('nepali-datepicker nepali-calendar english-calendar');
                $field.removeData('nepali-calendar');
                $field.attr('type', 'date');
                
                // Remove any calendar widgets that might have been attached
                $field.siblings('.nepali-calendar-widget').remove();
                $field.next('.ui-datepicker').remove();
            }
        });
    }, 100); // Check every 100ms for aggressive protection
    
    // Monitor form submission
    $('#data_form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        console.log('Form submission started');
        
        // Debug: Check all expiry date values before submission
        $('[name="expiry_date[]"]').each(function(index) {
            console.log('Expiry date field ' + index + ':', {
                id: $(this).attr('id'),
                value: $(this).val(),
                name: $(this).attr('name'),
                protected_value: $(this).attr('data-protected-value'),
                type: $(this).attr('type')
            });
        });
        
        // Force restore any protected values right before submission
        $('[name="expiry_date[]"]').each(function() {
            var protectedValue = $(this).attr('data-protected-value');
            var currentValue = $(this).val();
            
            if (protectedValue && (currentValue === 'english' || currentValue === 'nepali' || currentValue === '')) {
                console.warn('Final restore before submission:', $(this).attr('id'), 'from:', currentValue, 'to:', protectedValue);
                $(this).val(protectedValue);
            }
        });
        
        // Re-check values after final restore
        console.log('Final expiry date values after protection:');
        $('[name="expiry_date[]"]').each(function(index) {
            console.log('Field ' + index + ':', $(this).attr('id'), '=', $(this).val());
        });
        
        console.log('Form data:', $(this).serialize());
        
        // Check for required fields
        var hasErrors = false;
        $('.req').each(function() {
            if ($(this).val() === '') {
                console.warn('Required field empty:', $(this).attr('name') || $(this).attr('id'));
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            console.error('Form has required field errors');
            alert('Please fill all required fields');
            return false;
        }
        
        // Get action URL
        var actionUrl = $('#action-url').val() || 'purchase/action';
        console.log('Form will submit to:', actionUrl);
        
        // Disable submit button to prevent double submission
        $('#submit-data').prop('disabled', true).text('Creating...');
        
        // Submit form via AJAX
        $.ajax({
            url: baseurl + actionUrl,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.status === 'Success') {
                    alert('Purchase order created successfully!');
                    // Show the message which includes view link
                    $('#notify .message').html(response.message);
                    $('#notify').show();
                    
                    // Optionally redirect or reset form
                    // window.location.reload();
                } else {
                    console.error('Server returned error:', response.message);
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:');
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                console.error('Error:', error);
                
                alert('Error submitting form: ' + error + '\nStatus: ' + xhr.status);
            },
            complete: function() {
                // Re-enable submit button
                $('#submit-data').prop('disabled', false).text('<?php echo $this->lang->line('Generate Order') ?>');
            }
        });
        
        return false;
    });
    
    // Monitor submit button clicks
    $('#submit-data').on('click', function(e) {
        console.log('Submit button clicked');
        console.log('Button state:', $(this).attr('disabled'));
        
        // Add timeout to catch any immediate errors
        setTimeout(function() {
            console.log('Checking for form submission errors...');
        }, 1000);
    });
    
    // Monitor AJAX requests if any
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:');
        console.error('URL:', settings.url);
        console.error('Status:', xhr.status);
        console.error('Response:', xhr.responseText);
        console.error('Error:', thrownError);
        alert('AJAX Error: ' + xhr.status + ' - ' + thrownError + '\nResponse: ' + xhr.responseText);
    });
    
    $(document).ajaxSuccess(function(event, xhr, settings) {
        console.log('AJAX Success:');
        console.log('URL:', settings.url);
        console.log('Response:', xhr.responseText);
    });
});
</script>
<script src="<?= base_url('assets/js/purchase-entry.js') ?>"></script>