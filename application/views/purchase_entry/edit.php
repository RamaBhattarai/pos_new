<div class="content-body">
    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>

                <div class="message"></div>
            </div>
            <div class="card-body">
                <form method="post" id="data_form">


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
                                                <?php echo $this->lang->line('Add Suppliers') ?>
                                            </a>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Supplier') ?></label>
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
                                        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $invoice['csd']; ?>">
                                        <div id="customer_name"><strong><?php echo $invoice['name']; ?></strong></div>
                                    </div>
                                    <div class="clientinfo">
                                        <div id="customer_address1"><strong><?php echo $invoice['address'] . '<br>' . $invoice['city'] . ',' . $invoice['country']; ?></strong></div>
                                    </div>
                                    <div class="clientinfo">
                                        <div type="text" id="customer_phone">Phone: <strong><?php echo $invoice['phone']; ?></strong><br>Email: <strong><?php echo $invoice['email']; ?></strong></div>
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
                                                    class="title"> <?php echo $this->lang->line('Purchase Order Properties') ?></h3>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6"><label for="invocieno"
                                                                     class="caption"> <?php echo $this->lang->line('Purchase Order') ?>
                                                #</label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                     aria-hidden="true"></span></div>
                                                <input type="text" class="form-control" placeholder="Purchase Order #"
                                                       name="invocieno"
                                                       value="<?php echo $invoice['tid']; ?>" readonly><input
                                                        type="hidden"
                                                        name="iid"
                                                        value="<?php echo $invoice['iid']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6"><label for="invocieno"
                                                                     class="caption"> <?php echo $this->lang->line('Reference') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                     aria-hidden="true"></span></div>
                                                <input type="text" class="form-control" placeholder="Reference #"
                                                       name="refer"
                                                       value="<?php echo $invoice['refer'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <div class="col-sm-6"><label for="invociedate"
                                                                     class="caption"> <?php echo $this->lang->line('Order Date') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar4"
                                                                                     aria-hidden="true"></span></div>
                                                <input type="text" class="form-control required editdate"
                                                       placeholder="Billing Date" name="invoicedate"
                                                       autocomplete="false"
                                                       value="<?php echo dateformat($invoice['invoicedate']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6"><label for="invocieduedate"
                                                                     class="caption"><?php echo $this->lang->line('Order Due Date') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                     aria-hidden="true"></span></div>
                                                <input type="text" class="form-control required editdate"
                                                       name="invocieduedate"
                                                       placeholder="Due Date" autocomplete="false"
                                                       value="<?php echo dateformat($invoice['invoiceduedate']) ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="taxformat"
                                                   class="caption"><?php echo $this->lang->line('Tax') ?></label>
                                            <select class="form-control round" onchange="changeTaxFormat(this.value)"
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
                                                    <?php echo '<option value="' . $invoice['format_discount'] . '">' . $this->lang->line('Do not change') . '</option>'; ?>
                                                    <?php echo $this->common->disclist() ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="toAddInfo"
                                                   class="caption"><?php echo $this->lang->line('Order Note') ?></label>
                                            <textarea class="form-control" name="notes"
                                                      rows="2"><?php echo $invoice['notes'] ?></textarea></div>
                                    </div>

                                </div>
                            </div>

                        </div>


                        <div id="saman-row">
                            <table class="table-responsive tfr my_stripe">

                         <thead>
<tr class="item_header bg-gradient-directional-amber">
    <th width="25%" class="text-center">Item Name</th>
    <th width="8%" class="text-center">Batch No</th>
    <th width="8%" class="text-center">Expiry</th>
    <th width="6%" class="text-center">Qty</th>
    <th width="8%" class="text-center">Cost</th>
    <th width="8%" class="text-center">Selling</th>
    <th width="8%" class="text-center">Profit</th>
    <th width="8%" class="text-center">Profit %</th>
    <th width="6%" class="text-center">Tax %</th>
    <th width="8%" class="text-center">Tax</th>
    <th width="6%" class="text-center">Discount</th>
    <th width="10%" class="text-center">Amount</th>
    <th width="5%" class="text-center">Action</th>
</tr>
</thead>

                               <tbody>
<?php $i = 0; foreach ($products as $row) { ?>
<tr class="purchase-row">
    <td>
        <input type="text" class="form-control text-center"
               name="product_name[]"
               id="productname-<?= $i ?>"
               value="<?= $row['product'] ?>">
    </td>

    <td>
        <input type="text" class="form-control batch_no_input"
               name="batch_no[]"
               id="batch_no-<?= $i ?>"
               value="<?= $row['batch'] ?>">
    </td>

    <td>
        <input type="date" class="form-control expiry_date"
               name="expiry_date[]"
               id="expiry_date-<?= $i ?>"
               data-toggle="datepicker"
               value="<?= $row['expiry'] ? date('Y-m-d', strtotime(str_replace('/', '-', $row['expiry']))) : '' ?>">
    </td>

    <!-- Quantity -->
    <td>
        <input type="text" class="form-control amnt qty"
               name="product_qty[]"
               id="amount-<?= $i ?>"
               onkeyup="calculateRowTotal(<?= $i ?>); billUpyog();"
               value="<?= amountFormat_general($row['qty']) ?>">
    </td>

    <!-- Rate (Purchase Price) -->
    <td>
        <input type="text" class="form-control prc rate"
               name="product_price[]"
               id="price-<?= $i ?>"
               onkeyup="calculateRowTotal(<?= $i ?>); billUpyog();"
               value="<?= edit_amountExchange_s($row['price'], $invoice['multi'], $this->aauth->get_user()->loc) ?>">
    </td>

    <!-- Selling Price -->
    <td>
        <input type="text" class="form-control selling_price"
               name="selling_price[]"
               id="selling_price-<?= $i ?>"
               onkeyup="calculateRowTotal(<?= $i ?>); calculateProfit(<?= $i ?>);"
               value="<?= edit_amountExchange_s($row['selling_price'], $invoice['multi'], $this->aauth->get_user()->loc) ?>">
    </td>

    <!-- Profit -->
    <td>
        <input type="text" class="form-control profit"
               name="profit[]"
               id="profit-<?= $i ?>"
               value="<?= $row['profit'] ?>" readonly>
    </td>

    <!-- Profit Margin -->
    <td>
        <input type="text" class="form-control profit_margin"
               name="profit_margin[]"
               id="profit_margin-<?= $i ?>"
               value="<?= $row['profit_margin'] ?>" readonly>
    </td>

    <!-- Tax (%) -->
    <td>
        <input type="text" class="form-control vat tax"
               name="product_tax[]"
               id="vat-<?= $i ?>"
               onkeyup="calculateRowTotal(<?= $i ?>); billUpyog();"
               value="<?= amountFormat_general($row['tax']) ?>">
    </td>

    <td class="text-center" id="texttaxa-<?= $i ?>">
        <?= edit_amountExchange_s($row['totaltax'], $invoice['multi'], $this->aauth->get_user()->loc) ?>
    </td>

    <!-- Discount (%) -->
    <td>
        <input type="text" class="form-control discount"
               name="product_discount[]"
               id="discount-<?= $i ?>"
               onkeyup="calculateRowTotal(<?= $i ?>); billUpyog();"
               value="<?= amountFormat_general($row['discount']) ?>">
    </td>

    <td>
        <strong><span id="result-<?= $i ?>">
            <?= edit_amountExchange_s($row['subtotal'], $invoice['multi'], $this->aauth->get_user()->loc) ?>
        </span></strong>
    </td>

    <td class="text-center">
        <button type="button" data-rowid="<?= $i ?>"
                class="btn btn-danger removeProd">
            <i class="fa fa-minus-square"></i>
        </button>
    </td>

    <!-- Hidden fields for saving -->
    <input type="hidden" name="pid[]" value="<?= $row['pid'] ?>">
    <input type="hidden" name="taxa[]" id="taxa-<?= $i ?>" value="<?= $row['totaltax'] ?>">
    <input type="hidden" name="disca[]" id="disca-<?= $i ?>" value="<?= $row['totaldiscount'] ?>">
    <input type="hidden" name="product_subtotal[]" id="total-<?= $i ?>" value="<?= $row['subtotal'] ?>">
</tr>
<?php $i++; } ?>

<tr class="last-item-row">
    <td class="add-row">
        <button type="button" class="btn btn-success" id="addproduct">
            <i class="fa fa-plus-square"></i> Add Row
        </button>
    </td>
    <td colspan="12"></td>
</tr>
</tbody>

                            </table>
                        </div>

                        <input type="hidden" value="purchase/editaction_entry" id="action-url">
                        <input type="hidden" name="iid" value="<?php echo $id; ?>">
                        <input type="hidden" value="puchase_search" id="billtype">
                        <input type="hidden" value="<?php echo $i; ?>" name="counter" id="ganak">
                        <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">
                        <input type="hidden" id="page-type" value="purchase_entry_edit">

                        <input type="hidden" value="<?= $this->common->taxhandle_edit($invoice['taxstatus']) ?>"
                               name="taxformat" id="tax_format">
                        <input type="hidden" value="<?= $invoice['format_discount']; ?>" name="discountFormat"
                               id="discount_format">
                        <input type="hidden" value="<?= $invoice['taxstatus']; ?>" name="tax_handle" id="tax_status">
                        <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">

                        <input type="hidden" value="<?php
                        $tt = 0;

                        if($invoice['shipping']==0)  $invoice['shipping']=1;
                        if ($invoice['ship_tax_type'] == 'incl') $tt = @number_format(($invoice['shipping'] - $invoice['ship_tax']) / $invoice['shipping'], 2, '.', '');
                        echo amountFormat_general(number_format((($invoice['ship_tax'] / $invoice['shipping']) * 100) + $tt, 3, '.', '')); ?>"
                               name="shipRate" id="ship_rate">
                        <input type="hidden" value="<?= $invoice['ship_tax_type']; ?>" name="ship_taxtype"
                               id="ship_taxtype">
                        <input type="hidden" value="<?= amountFormat_general($invoice['ship_tax']); ?>" name="ship_tax"
                               id="ship_tax">


                               <div class="row mt-3">
    <div class="col-sm-12 text-right">
        <button type="submit"
                class="btn btn-primary btn-lg"
                id="submit-data">
            <i class="fa fa-save"></i> Update Purchase
        </button>
    </div>
</div>


                </form>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    // Run ONLY for purchase entry edit page
    if (!$('#page-type').length || $('#page-type').val() !== 'purchase_entry_edit') return;

    var baseurl = '<?php echo base_url(); ?>';

    var rowIndex = <?php echo $i; ?>; // Start from the last index

    // Initialize autocomplete for existing product name inputs
    // for (var idx = 0; idx < <?php echo $i; ?>; idx++) {
    //     $('#productname-' + idx).autocomplete({
    //         ... autocomplete code ...
    //     });
    // }

    // Remove existing Add Row click (from shared JS)
    $('#addproduct').off('click');
    $('#addproduct_edit').off('click');

    // Simple calculation function for purchase entries
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
        
        $('#subttlform').val(grandTotal.toFixed(2));
        $('#taxr').text(totalTax.toFixed(2));
        $('#discs').text(totalDiscount.toFixed(2));
        $('#invoiceyoghtml').val(finalTotal.toFixed(2));
    };

    // Recalculate totals for existing rows on page load
    for (var row = 0; row < <?php echo $i; ?>; row++) {
        if (window.calculateRowTotal) {
            window.calculateRowTotal(row);
        }
    }

    // New Add Row logic (clone first row as clean template)
    $('#addproduct').on('click', function (e) {
        e.stopImmediatePropagation();
        // Clone the first row as a clean template (avoid cloning existing toggle markup)
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

        // Update IDs (replace -0 with -rowIndex)
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
        }, 200);

        rowIndex++; 
        // Update counter
        $('#ganak').val(rowIndex);

        // Initialize autocomplete for the new product name input
        var newProductNameId = 'productname-' + (rowIndex - 1);
        $('#' + newProductNameId).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: baseurl + 'search_products/puchase_search',
                    method: "POST",
                    data: { name_startsWith: request.term },
                    dataType: "json",
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item[0] + '-' + item[2],
                                value: item[0] + '-' + item[2],
                                data: item
                            };
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                id_arr = $(this).attr('id');
                id = id_arr.split("-");
                var cvalue = id[1];
                var t_r = ui.item.data[3];
                var discount = ui.item.data[4];
                var custom_discount = $('#custom_discount').val();
                // if (custom_discount > 0) discount = deciFormat(custom_discount);
                if (t_r == 0 && discount == 5) t_r = 13;

                $('#amount-' + cvalue).val(1);
                $('#price-' + cvalue).val(ui.item.data[1]);
                $('#pid-' + cvalue).val(ui.item.data[2]);
                $('#vat-' + cvalue).val(t_r);
                $('#discount-' + cvalue).val(discount);
                $('#dpid-' + cvalue).val(ui.item.data[5]);
                $('#unit-' + cvalue).val(ui.item.data[6]);
                $('#hsn-' + cvalue).val(ui.item.data[7]);
                $('#alert-' + cvalue).val(ui.item.data[8]);
                $('#serial-' + cvalue).val(ui.item.data[10]);
                // --- Batch logic for autocomplete ---
                // If batchid is present in ui.item.data, add hidden input
                if (typeof ui.item.data[11] !== 'undefined' && ui.item.data[11]) {
                    // Remove any existing batchid input for this row
                    $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
                    // Add hidden input for batchid
                    var batchidInput = '<input type="hidden" name="batchid[]" value="' + ui.item.data[11] + '">';
                    $(this).closest('tr').next('tr').append(batchidInput);
                } else {
                    // Remove any existing batchid input if not a batch product
                    $(this).closest('tr').next('tr').find('input[name="batchid[]"]').remove();
                }
                // --- End batch logic ---
                if (window.calculateRowTotal) {
                    window.calculateRowTotal(cvalue);
                } else {
                    rowTotal(cvalue);
                }
                billUpyog();
                calculateProfit(cvalue);
            }
        });
    });

});
</script>

<div class="modal fade" id="addCustomer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="product_action" class="form-horizontal">
                <!-- Modal Header -->
                <div class="modal-header">

                    <h4 class="modal-title"
                        id="myModalLabel"><?php echo $this->lang->line('Add Supplier') ?></h4>
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
                                   class="form-control margin-bottom crequired" name="email"
                                   id="mcustomer_email">
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


                        <div class="col-sm-6">
                            <input type="text" placeholder="City"
                                   class="form-control margin-bottom" name="city" id="mcustomer_city">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="Region"
                                   class="form-control margin-bottom" name="region">
                        </div>

                    </div>

                    <div class="form-group row">


                        <div class="col-sm-6">
                            <input type="text" placeholder="Country"
                                   class="form-control margin-bottom" name="country" id="mcustomer_country">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="PostBox"
                                   class="form-control margin-bottom" name="postbox">
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
<script type="text/javascript"> $('.editdate').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>'
    });</script>
