


<div class="content-body">

    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div class="card-body">
                <form method="post" id="data_form" >


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
                                        <!-- <input type="hidden" name="customer_id" id="customer_id" value="0"> -->
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                        <input type="hidden" name="supplier_name" id="supplier_name" value="">

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
                                                class="title"><?php echo $this->lang->line('Purchase Entry') ?> </h3>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Entry Number') ?> </label>

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
                                                                 class="caption"><?php echo $this->lang->line('Entry Date') ?> </label>

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
                                                                 class="caption"><?php echo $this->lang->line('Entry Due Date') ?> </label>

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
                                    <div class="col-sm-12">
                                        <label for="toAddInfo"
                                               class="caption"><?php echo $this->lang->line('Entry Note') ?> </label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea></div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div id="saman-row" style="overflow-x: auto;">
<table class="tfr my_stripe">
<thead>
<tr class="item_header bg-gradient-directional-amber">
    <th width="30%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
    <th width="10%" class="text-center">Batch No.</th>
    <th width="10%" class="text-center">Expiry Date</th>
    <th width="7%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
    <th width="10%" class="text-center"><?php echo $this->lang->line('Rate') ?></th>
    <th width="10%" class="text-center">Selling Price</th>
    <th width="10%" class="text-center">Profit</th>
    <th width="10%" class="text-center">Profit %</th>
    <th width="8%" class="text-center"><?php echo $this->lang->line('Tax') ?>(%)</th>
    <th width="8%" class="text-center"><?php echo $this->lang->line('Tax') ?></th>
    <th width="8%" class="text-center"><?php echo $this->lang->line('Discount') ?></th>
    <th width="10%" class="text-center"><?php echo $this->lang->line('Amount') ?></th>
</tr>
</thead>

<tbody>
<tr class="purchase-row">
    <td>
        <input type="text" class="form-control" name="product_name[]" id="productname-0" style="min-width: 150px;">
    </td>

    <td>
        <input type="text" class="form-control" name="batch_no[]" id="batch_no-0" style="min-width: 100px;">
    </td>

    <td>
        <input type="date" class="form-control expiry_date" name="expiry_date[]" id="expiry_date-0" data-toggle="datepicker" style="min-width: 120px;">
    </td>

    <td>
        <input type="text" class="form-control amnt"
               name="product_qty[]" id="amount-0"
               onkeyup="calculateRowTotal(0); billUpyog();" value="1" style="min-width: 70px;">
    </td>

    <td>
        <input type="text" class="form-control prc"
               name="product_price[]" id="price-0"
               onkeyup="calculateRowTotal(0); billUpyog(); calculateProfit(0);" style="min-width: 90px;">
    </td>

    <td>
        <input type="text" class="form-control selling_price"
               name="selling_price[]" id="selling_price-0"
               onkeyup="calculateProfit(0);" style="min-width: 90px;">
    </td>

    <td>
        <input type="text" class="form-control profit"
               name="profit[]" id="profit-0" readonly style="min-width: 90px;">
    </td>

    <td>
        <input type="text" class="form-control profit_margin"
               name="profit_margin[]" id="profit_margin-0" readonly style="min-width: 90px;">
    </td>

    <td>
        <input type="text" class="form-control vat"
               name="product_tax[]" id="vat-0"
               onkeyup="calculateRowTotal(0); billUpyog();"
               style="min-width: 100px;">
    </td>

    <td class="text-center" id="texttaxa-0">0</td>

    <td>
        <input type="text" class="form-control discount"
               name="product_discount[]" id="discount-0"
               onkeyup="calculateRowTotal(0); billUpyog();"
               style="min-width: 100px;">
    </td>

    <td>
        <strong><span id="result-0">0</span></strong>
    </td>

    <!-- hidden -->
    <input type="hidden" name="taxa[]" id="taxa-0">
    <input type="hidden" name="disca[]" id="disca-0">
    <input type="hidden" name="product_subtotal[]" id="total-0">
    <input type="hidden" name="pid[]" id="pid-0">
</tr>

<tr class="last-item-row">
    <td class="add-row">
        <button type="button" class="btn btn-success" id="addproduct">
            <i class="fa fa-plus-square"></i> <?php echo $this->lang->line('Add Row') ?>
        </button>
    </td>
    <td colspan="11"></td>
</tr>
</tbody>
</table>

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

                    <input type="hidden" value="purchase/action_entry" id="action-url">

                    <input type="hidden" value="puchase_search" id="billtype">
                    <input type="hidden" value="purchase_entry" id="page-type">
                    <input type="hidden" value="0" name="counter" id="ganak">
                    <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">
                    <input type="hidden" value="<?= $taxdetails['handle']; ?>" name="taxformat" id="tax_format">

                    <input type="hidden" value="<?= $taxdetails['format']; ?>" name="tax_handle" id="tax_status">
                    <input type="hidden" value="yes" name="discount_handle" id="discount_handle">

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
<script type="text/javascript">
    $(document).ready(function () {
        console.log('Supplier ID:', $('#customer_id').val());
        console.log('Supplier Name:', $('#supplier_name').val());
        console.log('Order ID (tid):', $('input[name="tid"]').val());
        
        // Simple calculation function
        window.calculateRowTotal = function(row) {
            var qty = parseFloat($('#amount-' + row).val()) || 0;
            var price = parseFloat($('#price-' + row).val()) || 0;
            var taxPercent = parseFloat($('#vat-' + row).val()) || 0;
            var discountPercent = parseFloat($('#discount-' + row).val()) || 0;
            
            var subtotal = qty * price;
            var taxAmount = (subtotal * taxPercent) / 100;
            var discountAmount = (subtotal * discountPercent) / 100;
            var total = subtotal + taxAmount - discountAmount;
            
            $('#texttaxa-' + row).text(taxAmount.toFixed(2));
            $('#taxa-' + row).val(taxAmount.toFixed(2));
            $('#disca-' + row).val(discountAmount.toFixed(2));
            $('#total-' + row).val(total.toFixed(2));
            $('#result-' + row).text(total.toFixed(2));
            
            calculateGrandTotal();
        };
        
        window.calculateProfit = function(row) {
            var price = parseFloat($('#price-' + row).val()) || 0;
            var selling = parseFloat($('#selling_price-' + row).val()) || 0;
            var profit = selling - price;
            var profit_margin = price > 0 ? (profit / price) * 100 : 0;
            $('#profit-' + row).val(profit.toFixed(2));
            $('#profit_margin-' + row).val(profit_margin.toFixed(2));
        };
        
        window.calculateGrandTotal = function() {
            var grandTotal = 0;
            var totalTax = 0;
            var totalDiscount = 0;
            
            $('input[name="product_subtotal[]"]').each(function() {
                grandTotal += parseFloat($(this).val()) || 0;
            });
            
            $('input[name="taxa[]"]').each(function() {
                totalTax += parseFloat($(this).val()) || 0;
            });
            
            $('input[name="disca[]"]').each(function() {
                totalDiscount += parseFloat($(this).val()) || 0;
            });
            
            var shipping = parseFloat($('input[name="shipping"]').val()) || 0;
            var finalTotal = grandTotal + shipping;
            
            $('#taxr').text(totalTax.toFixed(2));
            $('#discs').text(totalDiscount.toFixed(2));
            $('#subttlform').val(grandTotal.toFixed(2));
            $('#invoiceyoghtml').val(finalTotal.toFixed(2));
            $('#grandtotal').val(finalTotal.toFixed(2));
        };
        
        // Handle supplier search and selection
        $('#supplier-box').on('input', function () {
            const query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: "<?php echo base_url('search_products/purchase_entry_supplier_search'); ?>",
                    method: "POST",
                    data: { keyword: query },
                    success: function (data) {
                        $('#supplier-box-result').html(data);
                    }
                });
            }
        });

        // Set supplier details when a supplier is selected
        $(document).on('click', '.supplier-select', function () {
            const supplierId = $(this).data('id');
            const supplierName = $(this).data('name');
            const supplierAddress = $(this).data('address');
            const supplierPhone = $(this).data('phone');

            $('#customer_id').val(supplierId);
            $('#supplier_name').val(supplierName);
            $('#customer_name').text(supplierName);
            $('#customer_address1').text(supplierAddress);
            $('#customer_phone').text(supplierPhone);
            $('#supplier-box-result').html('');
        });

        // Form uses standard submission via control.js - no custom handler needed

        // Handle supplier addition modal
        $('#msupplier_add').on('click', function (e) {
            e.preventDefault();
            
            $.ajax({
                url: baseurl + 'supplier/addcustomer',
                type: 'POST',
                data: $('#product_action').serialize() + '&' + crsf_token + '=' + crsf_hash,
                dataType: 'json',
                success: function (data) {
                    if (data.status == "Success") {
                        $('#addCustomer').modal('hide');
                        // Set the new supplier data
                        $('#customer_id').val(data.supplier_id);
                        $('#supplier_name').val($('#mcustomer_name').val());
                        $('#customer_name').text($('#mcustomer_name').val());
                        $('#customer_address1').text($('#mcustomer_address1').val());
                        $('#customer_phone').text($('#mcustomer_phone').val());
                        
                        // Clear modal form
                        $('#product_action')[0].reset();
                        
                        $("#notify .message").html("<strong>" + data.status + "</strong>: Supplier added successfully");
                        $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                    } else {
                        $("#statusMsg").html("<strong>" + data.status + "</strong>: " + data.message);
                    }
                }
            });
        });
    });
</script>

<script>
$(document).ready(function () {

    // Run ONLY for purchase entry page
    if ($('#page-type').val() !== 'purchase_entry') return;

    // Initialize tax format
    $('#taxformat').val('yes').trigger('change');

    // Initialize autocomplete for the first product name field
    $('#productname-0').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: baseurl + 'search_products/puchase_search',
                dataType: "json",
                method: 'post',
                data: 'name_startsWith=' + request.term + '&type=product_list&row_num=0&wid=' + $("#s_warehouses option:selected").val() + '&' + crsf_token + '=' + crsf_hash,
                success: function (data) {
                    response($.map(data, function (item) {
                        var product_d = item[0];
                        return {
                            label: product_d,
                            value: product_d,
                            data: item
                        };
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function (event, ui) {
            var t_r = ui.item.data[3];
            var discount = ui.item.data[4];
            var custom_discount = $('#custom_discount').val();
            if (custom_discount > 0) discount = deciFormat(custom_discount);
            if (t_r == 0 && discount == 5) t_r = 13;

            $('#amount-0').val(1);
            $('#price-0').val(ui.item.data[1]);
            $('#pid-0').val(ui.item.data[2]);
            $('#vat-0').val(t_r);
            $('#discount-0').val(discount);
            $('#dpid-0').val(ui.item.data[5]);
            $('#unit-0').val(ui.item.data[6]);
            $('#hsn-0').val(ui.item.data[7]);
            $('#alert-0').val(ui.item.data[8]);
            $('#serial-0').val(ui.item.data[10]);
            
            // Handle batch logic if present
            if (typeof ui.item.data[11] !== 'undefined' && ui.item.data[11]) {
                $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
                var batchidInput = '<input type="hidden" name="batchid[]" value="' + ui.item.data[11] + '">';
                $(this).closest('tr').next('tr').append(batchidInput);
            } else {
                $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
            }
            
            rowTotal(0);
            billUpyog();
            calculateProfit(0);
        }
    });

    let rowIndex = 1;

    // Remove existing Add Row click (from shared JS)
    $('#addproduct').off('click');

    // New Add Row logic (clone first row)
    $('#addproduct').on('click', function () {

        let $template = $('.purchase-row:first');
        let $newRow = $template.clone();

        // Clear input values
        $newRow.find('input').each(function () {
            if (this.type !== 'hidden') {
                $(this).val('');
            }
        });

        // Keep expiry date field exactly like existing rows for EN/NP toggle
        $newRow.find('input[name="expiry_date[]"]').each(function() {
            // Clean up any existing toggle state first
            $(this).removeClass('date-toggle-input toggle-managed no-datepicker-auto')
                  .removeAttr('data-toggle')
                  .unwrap('.date-toggle-container')
                  .siblings('.date-toggle-btn, input[name*="_format"]').remove();
            
            // Then add fresh attributes
            $(this).addClass('expiry_date').attr('data-toggle', 'datepicker').attr('type', 'date');
        });

        // Update IDs (0 → rowIndex)
        $newRow.find('[id]').each(function () {
            this.id = this.id.replace('-0', '-' + rowIndex);
        });

        // Update onkeyup handlers (calculateRowTotal(0) → calculateRowTotal(rowIndex))
        $newRow.find('[onkeyup]').each(function () {
            let fn = $(this).attr('onkeyup');
            if (fn) {
                $(this).attr('onkeyup', fn.replace(/\(0\)/g, '(' + rowIndex + ')'));
            }
        });

        // Reset calculated text
        $newRow.find('#result-' + rowIndex).text('0');
        $newRow.find('#texttaxa-' + rowIndex).text('0');
        $newRow.find('#texttaxa-' + rowIndex).text('0');

        // Insert before Add Row button
        $('.last-item-row').before($newRow);

        // Manually trigger date toggle initialization for the new expiry date field
        // Use a longer timeout to ensure DOM is fully updated
        setTimeout(function() {
            $newRow.find('.expiry_date').each(function() {
                if (typeof initializeDateToggle === 'function') {
                    console.log('Manually initializing date toggle for:', this.id);
                    initializeDateToggle(this);
                } else {
                    console.log('initializeDateToggle function not found');
                }
            });
            
            // Initialize autocomplete for the new product name field
            var newProductNameId = '#productname-' + (rowIndex - 1);
            $(newProductNameId).autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: baseurl + 'search_products/puchase_search',
                        dataType: "json",
                        method: 'post',
                        data: 'name_startsWith=' + request.term + '&type=product_list&row_num=' + (rowIndex - 1) + '&wid=' + $("#s_warehouses option:selected").val() + '&' + crsf_token + '=' + crsf_hash,
                        success: function (data) {
                            response($.map(data, function (item) {
                                var product_d = item[0];
                                return {
                                    label: product_d,
                                    value: product_d,
                                    data: item
                                };
                            }));
                        }
                    });
                },
                autoFocus: true,
                minLength: 0,
                select: function (event, ui) {
                    var id_arr = $(this).attr('id');
                    var id = id_arr.split("-");
                    var t_r = ui.item.data[3];
                    var discount = ui.item.data[4];
                    var custom_discount = $('#custom_discount').val();
                    if (custom_discount > 0) discount = deciFormat(custom_discount);
                    if (t_r == 0 && discount == 5) t_r = 13;

                    $('#amount-' + id[1]).val(1);
                    $('#price-' + id[1]).val(ui.item.data[1]);
                    $('#pid-' + id[1]).val(ui.item.data[2]);
                    $('#vat-' + id[1]).val(t_r);
                    $('#discount-' + id[1]).val(discount);
                    $('#dpid-' + id[1]).val(ui.item.data[5]);
                    $('#unit-' + id[1]).val(ui.item.data[6]);
                    $('#hsn-' + id[1]).val(ui.item.data[7]);
                    $('#alert-' + id[1]).val(ui.item.data[8]);
                    $('#serial-' + id[1]).val(ui.item.data[10]);
                    
                    // Handle batch logic if present
                    if (typeof ui.item.data[11] !== 'undefined' && ui.item.data[11]) {
                        $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
                        var batchidInput = '<input type="hidden" name="batchid[]" value="' + ui.item.data[11] + '">';
                        $(this).closest('tr').next('tr').append(batchidInput);
                    } else {
                        $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
                    }
                    
                    if (window.calculateRowTotal) {
                        window.calculateRowTotal(id[1]);
                    } else {
                        rowTotal(id[1]);
                    }
                    billUpyog();
                    calculateProfit(id[1]);
                }
            });
            
        }, 200);

        rowIndex++;
    });

});
</script>
