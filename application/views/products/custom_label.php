<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $this->lang->line('Products') . ' ' . $this->lang->line('Label'); ?></h5>
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
                <?php
                $attributes = array('class' => 'form-horizontal form-simple', 'id' => 'login_form', 'target' => '_blank');
                echo form_open('products/custom_label', $attributes);
                ?>
                <input type="hidden" name="act" value="add_product">
                <div class="form-group row">


                    <div class="col-sm-4"><label class="col-form-label"
                                                 for="product_cat"><?php echo $this->lang->line('Warehouse') ?></label>
                        <select id="wfrom" name="from_warehouse" class="form-control">
                            <option value='0'>Select</option>
                            <?php

                            foreach ($warehouse as $row) {
                                $cid = $row['id'];
                                $title = $row['title'];
                                echo "<option value='$cid'>$title</option>";
                            }
                            ?>
                        </select>


                    </div>


                    <div class="col-sm-8"><label class="col-form-label"
                                                 for="pay_cat"><?php echo $this->lang->line('Products') ?></label>
                        <select id="products_l" name="products_l[]" class="form-control required select-box"
                                multiple="multiple">

                        </select>


                    </div>
                </div>
                <hr>
                <?php echo $this->lang->line('Print') ?> <?php echo $this->lang->line('Settings') ?>
                <hr> <div class="form-group row">
                <div class="col-sm-2"><label class="col-form-label"
                                                 for="b_type">Barcode Type</label>
                        <select class="form-control" name="b_type">
                            <option value="1">EAN-13</option>
                             <option value="2">CODE-128</option>
                            <option value="3">CODE-39</option>
                            <option value="4">EAN-5</option>
                            <option value="5">EAN-8</option>
                            <option value="6">UPC-A</option>
                            <option value="7">UPC-E</option>
                        </select>
                    </div> </div>
                <div class="form-group row">


                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Sheet Width</label>
                        <input name="width" class="form-control required" type="number" value="106" step="0.01">
                        <small>in MM</small>

                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Sheet Height</label>
                        <input name="height" class="form-control required" type="number" value="15" step="0.01">
                        <small>in MM</small>

                    </div>

                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Label Width</label>
                        <input name="label_width" class="form-control required" type="number" value="55" step="0.01">
                        <small>in MM</small>

                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Label Height</label>
                        <input name="label_height" class="form-control required" type="number" value="20" step="0.01">
                        <small>in MM</small>

                    </div>

            <!--        <div class="col-sm-2"><label class="col-form-label"
                                                 for="padding">Padding</label>
                        <input name="padding" class="form-control required" type="number" value="10">
                        <small>in PX</small>

                    </div>-->


                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Total Rows</label>
                        <select class="form-control" name="total_rows">
                            <option value="0">1</option>
                            <option value="1">2</option>
                            <option value="2">3</option>
                            <option value="3">4</option>
                            <option value="4">5</option>
                            <option value="5">6</option>
                            <option value="6">7</option>
                            <option value="7">8</option>
                            <option value="8">9</option>
                            <option value="9">10</option>
                            <option value="19">20</option>
                        </select>
                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">Total Cols</label>
                        <select class="form-control" name="items_per_row">
                           <option value="0">1</option>
                            <option value="1" selected>2</option>
                            <option value="2">3</option>
                            <option value="3">4</option>
                            <option value="4">5</option>
                            <option value="5">6</option>
                            <option value="6">7</option>
                            <option value="7">8</option>
                            <option value="8">9</option>
                            <option value="9">10</option>
                            <option value="19">20</option>

                        </select>
                    </div>  </div>  <div class="row mb-2">


    <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">BarCode Width</label>
               <input name="bar_width" class="form-control required" type="number" value="30" step="0.01">
                    </div>                     <div class="col-sm-2"><label class="col-form-label"
                                                 for="width">BarCode height</label>

                                              <input name="bar_height" class="form-control required" type="number" value="6" step="0.01">
                    </div>  <div class="col-sm-2"><label class="col-form-label"
                                                 for="font_size">Font Size</label>
                        <select class="form-control" name="font_size">
                            <option value="8">8pt</option>
                            <option value="9">9pt</option>
                            <option value="10">10pt</option>
                           <option value="11" selected>11pt</option>
                            <option value="12" >12pt</option>
                            <option value="13">13pt</option>
                            <option value="14">14pt</option>
                            <option value="15">15pt</option>
                            <option value="16">16pt</option>
                            <option value="17">17pt</option>
                            <option value="18">18pt</option>
                            <option value="19">19pt</option>
                            <option value="20">20pt</option>


                        </select>
                    </div>
                    </div>  <div class="row mb-2">
   <div class="col-sm-2"><label class="col-form-label"
                                                 for="store_name">Product Name</label>
                        <select class="form-control" name="product_name">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="store_name"><?php echo $this->lang->line('Business') . ' ' . $this->lang->line('Location'); ?></label>
                        <select class="form-control" name="store_name">
    <option value="0" selected>No</option>
    <option value="1">Yes</option>
</select>
                    </div>

                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="store_name"><?php echo $this->lang->line('Warehouse') ?></label>
                        <select class="form-control" name="warehouse_name">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>

                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="product_price"><?php echo $this->lang->line('Price') ?></label>
                        <select class="form-control" name="product_price">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-sm-2"><label class="col-form-label"
                                                 for="product_code"><?php echo $this->lang->line('Product Code') ?></label>
                        <select class="form-control" name="product_code">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>    <div class="col-sm-2"><label class="col-form-label"
                                                 for="max_char">Max Characters (each line)</label>
               <input name="max_char" class="form-control required" type="number" value="50">
                    </div>
                </div>


                <div class="form-group row">


                    <div class="col-sm-4">
                        <input type="submit" class="btn btn-success margin-bottom"
                               value="<?php echo $this->lang->line('Print') ?>"
                               data-loading-text="Adding...">
                        <button type="button" class="btn btn-primary margin-bottom ml-2" id="save-settings">
                            Save Settings
                        </button>

                    </div>
                </div>
            </div>

            </form>
        </div>
    </div>

    <!-- Showing products according to warehouse -->
    <script type="text/javascript">
$(document).ready(function () {
    const $productsSelect = $("#products_l");
    const $warehouse = $("#wfrom");

    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function initProductSelect2(warehouseId) {
        if (!warehouseId || warehouseId === '0') {
            $productsSelect.select2({
                placeholder: "Please select a warehouse first",
                allowClear: true
            });
            return;
        }

        $productsSelect.select2('destroy').select2({
            placeholder: "Type to search products...",
            allowClear: true,
            multiple: true,
            ajax: {
                url: baseurl + 'products/stock_transfer_products?wid=' + warehouseId,
                dataType: 'json',
                type: 'POST',
                delay: 300,
                cache: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                data: function (params) {
                    return {
                        product: { term: params.term },
                        '<?= $this->security->get_csrf_token_name() ?>': getCsrfToken()
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.pid,
                                text: item.product_name + (item.product_code ? ' (' + item.product_code + ')' : '')
                            };
                        })
                    };
                },
                error: function (xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                }
            }
        });
    }

    // On warehouse change, reload products
    $warehouse.on('change', function () {
        const warehouseId = $(this).val();
        $productsSelect.val(null).trigger('change');
        initProductSelect2(warehouseId);
    });

    // On back/forward cache restore (important after print or navigating back)
    $(window).on("pageshow", function (event) {
        if (event.originalEvent.persisted || performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
            const selectedWarehouse = $warehouse.val();
            initProductSelect2(selectedWarehouse);
        }
    });

    // Initial setup
    const selectedWarehouse = $warehouse.val();
    initProductSelect2(selectedWarehouse);

    // Settings management using localStorage
    const FORM_STORAGE_KEY = 'custom_label_form_data';

    // Save form data to localStorage
    function saveFormData() {
        console.log('saveFormData function called');
        const formData = {
            from_warehouse: $('#wfrom').val(),
            products_l: $('#products_l').val() || [],
            b_type: $('select[name="b_type"]').val(),
            width: $('input[name="width"]').val(),
            height: $('input[name="height"]').val(),
            label_width: $('input[name="label_width"]').val(),
            label_height: $('input[name="label_height"]').val(),
            total_rows: $('select[name="total_rows"]').val(),
            items_per_row: $('select[name="items_per_row"]').val(),
            bar_width: $('input[name="bar_width"]').val(),
            bar_height: $('input[name="bar_height"]').val(),
            font_size: $('select[name="font_size"]').val(),
            product_name: $('select[name="product_name"]').val(),
            store_name: $('select[name="store_name"]').val(),
            warehouse_name: $('select[name="warehouse_name"]').val(),
            product_price: $('select[name="product_price"]').val(),
            product_code: $('select[name="product_code"]').val(),
            max_char: $('input[name="max_char"]').val()
        };
        console.log('Form data to save:', formData);
        localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(formData));

        // Show success message
        showMessage('Settings saved successfully!', 'success');
    }

    // Load form data from localStorage
    function loadFormData() {
        console.log('loadFormData function called');
        const savedData = localStorage.getItem(FORM_STORAGE_KEY);
        console.log('Saved data from localStorage:', savedData);
        if (savedData) {
            const formData = JSON.parse(savedData);
            console.log('Parsed form data:', formData);

            // Restore simple inputs and selects
            $('#wfrom').val(formData.from_warehouse);
            $('select[name="b_type"]').val(formData.b_type);
            $('input[name="width"]').val(formData.width);
            $('input[name="height"]').val(formData.height);
            $('input[name="label_width"]').val(formData.label_width);
            $('input[name="label_height"]').val(formData.label_height);
            $('select[name="total_rows"]').val(formData.total_rows);
            $('select[name="items_per_row"]').val(formData.items_per_row);
            $('input[name="bar_width"]').val(formData.bar_width);
            $('input[name="bar_height"]').val(formData.bar_height);
            $('select[name="font_size"]').val(formData.font_size);
            $('select[name="product_name"]').val(formData.product_name);
            $('select[name="store_name"]').val(formData.store_name);
            $('select[name="warehouse_name"]').val(formData.warehouse_name);
            $('select[name="product_price"]').val(formData.product_price);
            $('select[name="product_code"]').val(formData.product_code);
            $('input[name="max_char"]').val(formData.max_char);

            // Restore products after select2 is initialized
            if (formData.products_l && formData.products_l.length > 0) {
                setTimeout(function() {
                    $('#products_l').val(formData.products_l).trigger('change');
                    console.log('Products restored:', formData.products_l);
                }, 500);
            }

            // Re-initialize product select2 with the restored warehouse
            if (formData.from_warehouse && formData.from_warehouse !== '0') {
                setTimeout(function() {
                    initProductSelect2(formData.from_warehouse);
                    console.log('Product select2 re-initialized for warehouse:', formData.from_warehouse);
                }, 100);
            }
            console.log('Form data loaded successfully');
        } else {
            console.log('No saved data found in localStorage');
        }
    }

    // Show message function
    function showMessage(message, type) {
        // Remove existing alerts
        $('.alert').remove();

        // Create and show new alert
        const alertClass = type === 'success' ? 'alert-success' :
                          type === 'warning' ? 'alert-warning' :
                          type === 'info' ? 'alert-info' : 'alert-danger';

        const alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                         '<strong>' + message + '</strong>' +
                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                         '<span aria-hidden="true">&times;</span></button></div>';

        // Insert before the card content
        $('.card-content').prepend(alertHtml);

        // Auto-hide after 5 seconds (increased from 3)
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Load saved settings on page load
    loadFormData();

    // Save settings button handler
    $('#save-settings').on('click', function() {
        console.log('Save Settings button clicked');
        saveFormData();
    });
});
</script>
