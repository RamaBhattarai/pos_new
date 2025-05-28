<body>
<div class="stock-adjustment-create-page">
<div class="container">
     <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <div class="card p-4">
        <h3 class="mb-4">Create Stock Adjustment</h3>
        
        <form id="stockAdjustmentForm" method="POST" action="<?= base_url('StockAdjustment/save') ?>">
            <!-- Adjustment Reason -->
            <div class="form-group">
                <label for="adjustment_reason">Adjustment Reason <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="adjustment_reason" name="adjustment_reason" placeholder="Enter reason for adjustment" required>
            </div>
         
            <!-- Warehosue Select -->
<div class="form-group">
    <label for="warehouse_select">Select Warehouse <span class="text-danger">*</span></label>
    <select class="form-control" id="warehouse_select" name="warehouse" required>
        <option value="">-- Select a Warehouse --</option>
        <?php foreach ($warehouses as $wh): ?>
            <option value="<?= $wh['id'] ?>"><?= htmlspecialchars($wh['title']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Category Select -->
<div class="form-group">
    <label for="category_select">Select Category <span class="text-danger">*</span></label>
    <select class="form-control" id="category_select" name="category_id" required disabled>
        <option value="">-- Select a Category --</option>
    </select>
</div>

<!-- Subcategory Select -->
<div class="form-group">
    <label for="subcategory_select">Select Subcategory</label>
    <select class="form-control" id="subcategory_select" name="subcategory_id" disabled>
        <option value="">-- Select a subcategory --</option>
    </select>
</div>
           <!-- Product Select -->
<div class="form-group">
    <label for="product_select">Select Product <span class="text-danger">*</span></label>
    <select class="form-control" id="product_select" name="product_id" disabled>
        <option value="">-- Select a product --</option>
    </select>
</div>

<!-- Removed the Add Product button -->

<!-- Product Details Table (hidden initially) -->
<div id="productDetails" class="mt-4 d-none">

    <h5 class="form-section-title">Selected Product Details</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Stock</th>
                <th>Adjustment Type</th>
                <th>Quantity</th>
                <th>Note</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="productDetailsBody">
            <!-- Rows will be added dynamically here by JS -->
        </tbody>
    </table>
</div>

            <!-- Adjustment Date -->
            <div class="form-group mt-3">
                <label for="adjustment_date">Adjustment Date</label>
                <input type="date" class="form-control" id="adjustment_date" name="adjustment_date" value="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-save btn-block mt-4">
                <i class="fas fa-save"></i> Save Adjustment
            </button>
        </form>
    </div>
</div>
</div>

<!-- JQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Keep your existing HTML as is -->


<!-- Pass PHP data to JS -->
<script>
    let currentProducts = [];
    const warehouses = <?= json_encode($warehouses) ?>;
    const categories = <?= json_encode($categories) ?>;
    const products = <?= json_encode($products) ?>;

        $(document).ready(function () {
        const $warehouseSelect = $('#warehouse_select');
        const $categorySelect = $('#category_select');
        const $subcategorySelect = $('#subcategory_select');
        const $productSelect = $('#product_select');
        const $productDetails = $('#productDetails');
        const $productDetailsBody = $('#productDetailsBody');

        const addedProducts = {};

       function resetSelect(selector, placeholder) {
    let $select = $(selector);
    $select.empty().append($('<option>', {value: '', text: placeholder}));
    $select.prop('disabled', false);
}


       $('#warehouse_select').on('change', function () {
    let warehouseId = $(this).val();
    resetSelect('#category_select', 'Select Category');
    resetSelect('#subcategory_select', 'Select Subcategory');
    resetSelect('#product_select', 'Select Product');

    if (warehouseId) {
        $.post('<?= base_url('StockAdjustment/get_categories') ?>', {warehouse_id: warehouseId}, function (data) {
            data = JSON.parse(data);
            let $cat = $('#category_select');
            data.forEach(function (cat) {
                $cat.append($('<option>', {value: cat.id, text: cat.title}));
            });
            $cat.prop('disabled', false);
        });
    }
});



       $('#category_select').on('change', function () {
    let categoryId = $(this).val();
    let warehouseId = $('#warehouse_select').val();
    resetSelect('#subcategory_select', 'Select Subcategory');
    resetSelect('#product_select', 'Select Product');

    if (categoryId && warehouseId) {
        $.post('<?= base_url('StockAdjustment/get_subcategories') ?>', {category_id: categoryId, warehouse_id: warehouseId}, function (data) {
            data = JSON.parse(data);
            let $sub = $('#subcategory_select');
            if (data.length > 0) {
                data.forEach(function (sub) {
                    $sub.append($('<option>', {value: sub.id, text: sub.title}));
                });
                $sub.prop('disabled', false);
            } else {
                // No subcategories, fetch products by category directly!
                $.post('<?= base_url('StockAdjustment/get_products') ?>', {category_id: categoryId, warehouse_id: warehouseId}, function (pdata) {
                    pdata = JSON.parse(pdata);
                    currentProducts = pdata;
                    let $prod = $('#product_select');
                    pdata.forEach(function (prod) {
                        $prod.append($('<option>', {value: prod.pid, text: prod.product_name + ' [' + prod.product_code + ']'}));
                    });
                    $prod.prop('disabled', false);
                });
            }
        });
    }
});




     $('#subcategory_select').on('change', function () {
    let subcatId = $(this).val();
    let warehouseId = $('#warehouse_select').val();
    resetSelect('#product_select', 'Select Product');

    if (subcatId && warehouseId) {
        $.post('<?= base_url('StockAdjustment/get_products') ?>', {sub_category_id: subcatId, warehouse_id: warehouseId}, function (data) {
            data = JSON.parse(data);
            currentProducts = data; // <-- Store the products for this warehouse/subcategory
            let $prod = $('#product_select');
            data.forEach(function (prod) {
                $prod.append($('<option>', {value: prod.pid, text: prod.product_name + ' [' + prod.product_code + ']'}));
            });
            $prod.prop('disabled', false);
        });
    }
});




        $productSelect.on('change', function () {
    const selectedId = $(this).val();
    if (!selectedId) return;

    if (addedProducts[selectedId]) {
        alert('This product is already added.');
        $productSelect.val('');
        return;
    }

    // Use the products from the current AJAX response
    const product = currentProducts.find(p => p.pid == selectedId);
    if (product) {
        addProductRow(product);
        addedProducts[selectedId] = true;
        $productDetails.removeClass('d-none');
        $productSelect.val('');
    }
});

        function addProductRow(product) {
            const rowCount = $productDetailsBody.children('tr').length + 1;

            const rowHtml = `
                <tr data-product-id="${product.pid}">
                    <td>${rowCount}</td>
                    <td>${product.product_code}</td>
                    <td>${product.product_name}</td>
                    <td>${product.qty !== undefined ? product.qty : 'N/A'}</td>
                    <td>
                        <select name="adjustment_type[]" class="form-control" required>
                            <option value="increment">Increment</option>
                            <option value="decrement">Decrement</option>
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                    <td><input type="text" name="note[]" class="form-control"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product-btn">Remove</button>
                        <input type="hidden" name="product_id[]" value="${product.pid}">
                    </td>
                </tr>
            `;

            $productDetailsBody.append(rowHtml);
        }

        $productDetailsBody.on('click', '.remove-product-btn', function () {
            const $row = $(this).closest('tr');
            const productId = $row.data('product-id');

            delete addedProducts[productId];
            $row.remove();

            $productDetailsBody.children('tr').each(function (index) {
                $(this).find('td:first').text(index + 1);
            });

            if ($productDetailsBody.children('tr').length === 0) {
                $productDetails.addClass('d-none');
            }
        });
    });
</script>




</body>