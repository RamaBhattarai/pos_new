<div class="content-body">
   
    <div class="card">
    <input type="hidden" id="csrf_token_name" value="<?= $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_token_value" value="<?= $this->security->get_csrf_hash(); ?>">

        <div class="card-header">
            <h4 class="card-title"><?php echo $this->lang->line('Manage POS Invoices') ?> <a
                        href="<?php echo base_url('pos_invoices/create') ?>"
                        class="btn btn-primary btn-sm rounded">
                    <?php echo $this->lang->line('Add new') ?></a></h4>
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
                <div class="row">

                    <div class="col-md-2"><?php echo $this->lang->line('Invoice Date') ?></div>
                    <div class="col-md-2">
                        <input type="text" name="start_date" id="start_date"
                               class="date30 form-control form-control-sm" autocomplete="off"/>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="end_date" id="end_date" class="form-control form-control-sm"
                               data-toggle="datepicker" autocomplete="off"/>
                    </div>

                    <div class="col-md-2">
                        <input type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm"/>
                    </div>

                </div>
                <hr>
                <table id="invoices" class="table table-striped table-bordered  dataex-res-constructor">
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('No') ?></th>
                        <th> #</th>
                        <th><?php echo $this->lang->line('Customer') ?></th>
                        <th><?php echo $this->lang->line('Date') ?></th>
                        <th><?php echo $this->lang->line('Amount') ?></th>
                        <th><?php echo $this->lang->line('Status') ?></th>
                        <th class="no-sort"><?php echo $this->lang->line('Settings') ?></th>


                    </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>

                    <tfoot>
                    <tr>
                        <th><?php echo $this->lang->line('No') ?></th>
                        <th> #</th>
                        <th><?php echo $this->lang->line('Customer') ?></th>
                        <th><?php echo $this->lang->line('Date') ?></th>

                        <th><?php echo $this->lang->line('Amount') ?></th>
                        <th><?php echo $this->lang->line('Status') ?></th>
                        <th class="no-sort"><?php echo $this->lang->line('Settings') ?></th>

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>





<!-- End of Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-message">
                <!-- Dynamic message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- cancel -->
<div id="cancel_bill" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Cancel Invoice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form class="cancelbill">


                    <?php echo $this->lang->line('You can not revert'); ?>


            </div>


            <div class="modal-footer">
                <input type="hidden" class="form-control"
                       name="tid" value="<?php echo $invoice['iid'] ?>">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
                <button type="button" class="btn btn-danger"
                        id="send"><?php echo $this->lang->line('Cancel Invoice'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Delete Invoice') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php echo $this->lang->line('delete this invoice') ?> ?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="pos_invoices/delete_i">
                <button type="button" data-dismiss="modal" class="btn btn-primary"
                        id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                <button type="button" data-dismiss="modal"
                        class="btn"><?php echo $this->lang->line('Cancel') ?></button>
                 
              

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var csrf_token_name = $('#csrf_token_name').val();
var csrf_token_value = $('#csrf_token_value').val();


    $(document).ready(function () {
        draw_data();

        function draw_data(start_date = '', end_date = '') {
            $('#invoices').DataTable({
                'processing': true,
                'serverSide': true,
                'stateSave': true,
                responsive: true,
                <?php datatable_lang();?>
                'order': [],
                'ajax': {
                    'url': "<?php echo site_url('pos_invoices/ajax_list')?>",
                    'type': 'POST',
                    'data': {
                        '<?=$this->security->get_csrf_token_name()?>': crsf_hash,
                        start_date: start_date,
                        end_date: end_date
                    }
                },
                'columnDefs': [
                    {
                        'targets': [0],
                        'orderable': false,
                    },


                ],
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    }
                ],
            });
        }

        $('#search').click(function () {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if (start_date != '' && end_date != '') {
                $('#invoices').DataTable().destroy();
                draw_data(start_date, end_date);
            } else {
                alert("Date range is Required");
            }
        });



//////////////cancel
// Open Cancel Modal
$(document).on('click', ".cancel-bill", function (e) {
    e.preventDefault();
    const tid = $(this).data('id'); // Get invoice TID from button
    $('input[name="tid"]').val(tid); // Set it in modal form hidden input
    $('#cancel_bill').modal({ backdrop: 'static', keyboard: false });
});

// Handle Confirm Cancel Click
$(document).on('click', "#send", function (e) {
    e.preventDefault();
    var acturl = 'transactions/cancelinvoice';
    cancelBill(acturl);
});

// Ajax call to cancel
function cancelBill(acturl) {
    var errorNum = farmCheck();
    $("#cancel_bill").modal('hide');
    if (errorNum > 0) {
        $("#notify").removeClass("alert-success").addClass("alert-warning").fadeIn();
        $("#notify .message").html("<strong>Error</strong>");
        $("html, body").animate({ scrollTop: $('#notify').offset().bottom }, 1000);
    } else {
        jQuery.ajax({
            url: baseurl + acturl,
            type: 'POST',
            data: $('form.cancelbill').serialize() + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function (data) {
                if (data.status == "Success") {
                    $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                    $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                    $("html, body").scrollTop($("body").offset().top);
                } else {
                    $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                    $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
                    $("html, body").scrollTop($("body").offset().top);
                }
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (data) {
                $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
                $("html, body").scrollTop($("body").offset().top);
            }
        });
    }
}



       
    });
</script>