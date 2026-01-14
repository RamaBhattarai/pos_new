<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>

        <div class="message"></div>
    </div>
    <div class="card card-block ">


        <form method="post" id="data_form" class="card-body">

            <h5>Add Product Option</h5>
            <p class="text-muted">Create main categories like Color, Size, Material, etc.</p>
            <hr>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="name">Option Name</label>
                <div class="col-sm-4">
                    <input type="text" placeholder="e.g., Color, Size, Material"
                           class="form-control margin-bottom round required" name="name">
                </div>
            </div>


            <div class="form-group row">

                <label class="col-sm-2 col-form-label"></label>

                <div class="col-sm-4">
                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                           value="Add Option" data-loading-text="Adding...">
                    <input type="hidden" value="units/create_va" id="action-url">
                </div>
            </div>

            <input type="hidden" name="image" id="image" value="logo.png">
        </form>
    </div>
</div>