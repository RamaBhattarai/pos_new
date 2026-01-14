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
                                        <?php echo '  <input type="hidden" name="customer_id" id="customer_id" value="' . $invoice['csd'] . '">
                                <div id="customer_name"><strong>' . $invoice['name'] . '</strong></div>
                            </div>
                            <div class="clientinfo">

                                <div id="customer_address1"><strong>' . $invoice['address'] . '<br>' . $invoice['city'] . ',' . $invoice['country'] . '</strong></div>
                            </div>

                            <div class="clientinfo">

                                <div type="text" id="customer_phone">Phone: <strong>' . $invoice['phone'] . '</strong><br>Email: <strong>' . $invoice['email'] . '</strong></div>
                            </div>'; ?>
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
<?php $i = 0; foreach ($products as $row) { ?>
<tr>
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
               value="<?= date('Y-m-d', strtotime($row['expiry'])) ?>">
    </td>

    <td>
        <input type="number" class="form-control qty"
               name="qty[]"
               value="<?= $row['qty'] ?>">
    </td>

    <td>
        <input type="number" class="form-control rate"
               name="rate[]"
               value="<?= $row['price'] ?>">
    </td>

    <td>
        <input type="number" class="form-control selling_price"
               name="selling_price[]"
               value="<?= $row['selling_price'] ?>">
    </td>

    <td>
        <input type="text" class="form-control profit"
               name="profit[]"
               value="<?= $row['profit'] ?>"
               readonly>
    </td>

    <td>
        <input type="text" class="form-control profit_margin"
               name="profit_margin[]"
               value="<?= $row['profit_margin'] ?>"
               readonly>
    </td>

    <td>
        <input type="number" class="form-control tax"
               name="tax[]"
               value="<?= $row['tax'] ?>">
    </td>

    <td class="text-center tax_amount">0</td>

    <td>
        <input type="number" class="form-control discount"
               name="discount[]"
               value="<?= $row['discount'] ?>">
    </td>

    <td>
        <strong class="ttlText">0</strong>
    </td>

    <!-- Hidden values -->
    <input type="hidden" name="pid[]" value="<?= $row['pid'] ?>">
</tr>
<?php $i++; } ?>

<tr class="last-item-row">
    <td class="add-row">
        <button type="button" class="btn btn-success" id="addproduct">
            <i class="fa fa-plus-square"></i> Add Row
        </button>
    </td>
    <td colspan="11"></td>
</tr>
</tbody>

                        </table>
                        </div>
                    </div>

                        <input type="hidden" value="purchase/editaction" id="action-url">
                        <input type="hidden" value="puchase_search" id="billtype">
                        <input type="hidden" value="<?php echo $i; ?>" name="counter" id="ganak">
                        <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">

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

<script src="<?= base_url('assets/js/purchase-entry.js') ?>"></script>