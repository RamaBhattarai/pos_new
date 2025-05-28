<article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <form method="post" id="data_form" class="form-horizontal">
            <div class="card card-block">

                <h5>Edit Product warehouse</h5>
                <hr>


                <input type="hidden" name="catid" value="<?php echo $warehouse['id'] ?>">


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="product_cat_name">Warehouse Name</label>

                    <div class="col-sm-8">
                        <input type="text"
                               class="form-control margin-bottom  required" name="product_cat_name"
                               value="<?php echo $warehouse['title'] ?>">
                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label">Description</label>

                    <div class="col-sm-8">


                        <input type="text" name="product_cat_desc" class="form-control required"
                               aria-describedby="sizing-addon1" value="<?php echo $warehouse['extra'] ?>">

                    </div>

                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="lid"><?php echo $this->lang->line('Business Locations') ?></label>

                    <div class="col-sm-6">
                        <select name="lid" class="form-control">
    <option value=""><?php echo $this->lang->line('Choose...'); ?></option>
    <option value="0" <?php if ($warehouse['loc'] == '0') echo 'selected'; ?>>
        <?php echo $this->lang->line('All'); ?>
    </option>
    <?php foreach ($locations as $row): ?>
        <option value="<?= $row['id']; ?>"
                <?= ($row['id'] == $warehouse['loc']) ? 'selected' : ''; ?>>
            <?= $row['cname']; ?> – <?= $row['address']; ?>
        </option>
    <?php endforeach; ?>
</select>

 


                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                        <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                               value="Update" data-loading-text="Updating...">
                        <input type="hidden" value="productcategory/editwarehouse" id="action-url">
                    </div>
                </div>

            </div>
        </form>
    </div>

</article>

