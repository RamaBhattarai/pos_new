<div class="content-body">
    <div class="row">
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="success"><span id="dash_0"></span></h3>
                                <span><?php echo $this->lang->line('In Stock') ?></span>
                            </div>
                            <div class="align-self-center">
                                <i class="icon-rocket success font-large-2 float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="danger"><span id="dash_1"></span></h3>
                                <span><?php echo $this->lang->line('Stock out') ?></span>
                            </div>
                            <div class="align-self-center">
                                <i class="icon-eyeglasses danger font-large-2 float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="purple"><span id="dash_2"></span></h3>
                                <span><?php echo $this->lang->line('Total') ?></span>
                            </div>
                            <div class="align-self-center">
                                <i class="icon-pie-chart purple font-large-2 float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="card-header">
            <h5><?php echo $this->lang->line('Products') ?> <a
                        href="<?php echo base_url('products/add') ?>"
                        class="btn btn-primary btn-sm rounded">
                    <?php echo $this->lang->line('Add new') ?>
                </a> 
                <a href="<?php echo base_url('products?stock=out'); ?>" class="btn btn-danger btn-sm rounded" style="margin-left:2px;">
    Show Stock Out
</a>

                 <a
                        href="<?php echo base_url('products') ?>?group=yes"
                        class="btn btn-purple btn-sm rounded"><i class="ft-grid"></i></a> <a
                        href="<?php echo base_url('products') ?>"
                        class="btn btn-purple btn-sm rounded"><i class="ft-list"></i></a></h5>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

<!-- Import Excel Section -->
<div class="card shadow-sm border-0 mt-3" style="border-left: 5px solid #28a745; transition: all 0.3s ease;">
    <div class="card-body" style="padding: 0.5rem;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa fa-file-excel-o"></i>
            </div>
            <div class="ms-3">
                <h5 class="mb-0">Import Products via Excel</h5>
                <small class="text-muted">Easily upload products in bulk using a spreadsheet file.</small>
            </div>
        </div>

        <form id="excelForm" action="<?php echo base_url('products/import_excel'); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-2">
                <label for="excel_file" class="form-label fw-semibold">Select Excel File</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control border border-success" required>
                <small class="form-text text-muted">
                    Don't have a template?
                    <a href="<?php echo base_url('assets/templates/POS.xlsx'); ?>" download>
                        Download sample Excel template
                    </a>
                </small>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fa fa-upload"></i> Import Now
            </button>

            <!-- Spinner (initially hidden) -->
            <div id="loadingSpinner" class="text-center mt-3" style="display: none;">
                <div class="spinner-border text-success" role="status"></div>
                <div class="mt-2 fw-semibold text-success">Importing... Please wait</div>
            </div>
        </form>
    </div>
</div>








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


                <table id="productstable" class="table table-striped table-bordered zero-configuration" cellspacing="0"
                       width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('Name') ?></th>
                        <th><?php echo $this->lang->line('Qty') ?></th>
                        <th><?php echo $this->lang->line('Code') ?></th>
                        <th><?php echo $this->lang->line('Category') ?></th>
                        <th><?php echo $this->lang->line('Warehouse') ?></th>
                        <th><?php echo $this->lang->line('Price') ?></th>
                        <th><?php echo $this->lang->line('Settings') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('Name') ?></th>
                        <th><?php echo $this->lang->line('Qty') ?></th>
                        <th><?php echo $this->lang->line('Code') ?></th>
                        <th><?php echo $this->lang->line('Category') ?></th>
                        <th><?php echo $this->lang->line('Warehouse') ?></th>
                        <th><?php echo $this->lang->line('Price') ?></th>
                        <th><?php echo $this->lang->line('Settings') ?></th>
                    </tr>
                    </tfoot>
                </table>

            </div>
            <input type="hidden" id="dashurl" value="products/prd_stats">
        </div>
        <script type="text/javascript">
            document.getElementById('excelForm').addEventListener('submit', function () {
        document.getElementById('loadingSpinner').style.display = 'block';
    });

            var table;

            $(document).ready(function () {

                //datatables
                table = $('#productstable').DataTable({

                    "processing": true, //Feature control the processing indicator.
                    "serverSide": true, //Feature control DataTables' server-side processing mode.
                    "order": [], //Initial no order.
                    responsive: true,
                    <?php datatable_lang();?>

                   "ajax": {
    "url": "<?php echo site_url('products/product_list')?>",
    "type": "POST",
    "data": function(d) {
        d.<?=$this->security->get_csrf_token_name()?> = crsf_hash;
        d.group = '<?=$this->input->get('group')?>';
        d.stock = '<?php echo $this->input->get('stock'); ?>'; // Pass stock filter from URL
    }
},

                    //Set column definition initialisation properties.
                    "columnDefs": [
                        {
                            "targets": [0], //first column / numbering column
                            "orderable": false, //set not orderable
                        },
                    ],
                    dom: 'Blfrtip',lengthMenu: [10, 20, 50, 100, 200, 500, 1000, 1500],
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            footer: true,
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6]
                            }
                        }
                    ],

                });
                miniDash();


                $(document).on('click', ".view-object", function (e) {
                    e.preventDefault();
                    $('#view-object-id').val($(this).attr('data-object-id'));

                    $('#view_model').modal({backdrop: 'static', keyboard: false});

                    var actionurl = $('#view-action-url').val();
                    $.ajax({
                        url: baseurl + actionurl,
                        data: 'id=' + $('#view-object-id').val() + '&' + crsf_token + '=' + crsf_hash,
                        type: 'POST',
                        dataType: 'html',
                        success: function (data) {
                            $('#view_object').html(data);

                        }

                    });

                });

   
            });
        </script>
        <div id="delete_model" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">

                        <h4 class="modal-title"><?php echo $this->lang->line('Delete') ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $this->lang->line('delete this product') ?></p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="object-id" value="">
                        <input type="hidden" id="action-url" value="products/delete_i">
                        <button type="button" data-dismiss="modal" class="btn btn-primary"
                                id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                        <button type="button" data-dismiss="modal"
                                class="btn"><?php echo $this->lang->line('Cancel') ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div id="view_model" class="modal  fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content ">
                    <div class="modal-header">

                        <h4 class="modal-title"><?php echo $this->lang->line('View') ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body" id="view_object">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="view-object-id" value="">
                        <input type="hidden" id="view-action-url" value="products/view_over">

                        <button type="button" data-dismiss="modal"
                                class="btn"><?php echo $this->lang->line('Close') ?></button>
                    </div>
                </div>
            </div>
        </div>