<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>
    <div class="card card-block">
        <form method="post" id="data_form" class="card-body">
            <h5>Add an Option Value</h5>
            <p class="text-muted">Add values like Blue, Red, XL, Large under an Option</p>
            <hr>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="option_id">Option Name</label>
                <div class="col-sm-4">
                    <select name="option_id" id="option_id" class="form-control required">
                        <option value="">Select Option (Color, Size, etc.)</option>
                        <?php foreach ($options as $row) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="name">Option Value Name</label>
                <div class="col-sm-4">
                    <input type="text" placeholder="e.g., Blue, Red, XL, Large" 
                           class="form-control margin-bottom round required" name="name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-4">
                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                           value="Add Option Value" data-loading-text="Adding...">
                    <input type="hidden" value="units/create_variation_option" id="action-url">
                    <input type="hidden" name="level_type" value="2">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Simple form - no complex toggles needed for Option Values
$(document).ready(function() {
    // Any additional JavaScript if needed
});
</script>