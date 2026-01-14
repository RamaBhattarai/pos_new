<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="<?= LTR ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <?php if (@$title) {
        echo "<title>$title</title >";
    } else {
        echo "<title>POS</title >";
    }
    ?>
     <!-- Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="apple-touch-icon" href="<?= assets_url() ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= assets_url() ?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i"
          rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= LTR ?>/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/extensions/unslider.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/weather-icons/climacons.min.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/fonts/meteocons/style.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/charts/morris.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/tables/datatable/datatables.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= LTR ?>/app.css">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/<?= LTR ?>/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/<?= LTR ?>/core/colors/palette-gradient.css">
    <link rel="stylesheet" href="<?php echo assets_url('assets/custom/datepicker.min.css') . APPVER ?>">
    <!-- Nepali Datepicker CSS -->
    <link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.4.min.css" rel="stylesheet" type="text/css"/>
    
    <!-- Modern Date Toggle Styles -->
    <style>
        /* Essential layout for EN/NP date toggle */
        /* Hide default calendar icon for date inputs with EN/NP toggle */
        .date-toggle-input::-webkit-calendar-picker-indicator {
            opacity: 0;
            pointer-events: none;
        }
        .date-toggle-input::-ms-input-placeholder {
            color: transparent;
        }
        .date-toggle-input::-moz-placeholder {
            color: transparent;
        }
        .date-toggle-input::-o-placeholder {
            color: transparent;
        }
        .date-toggle-input::-webkit-input-placeholder {
            color: transparent;
        }
        .date-toggle-input::-webkit-clear-button,
        .date-toggle-input::-webkit-inner-spin-button,
        .date-toggle-input::-webkit-outer-spin-button {
            display: none;
            -webkit-appearance: none;
        }
        .date-toggle-input[type="date"]::-ms-expand {
            display: none;
        }
        .date-toggle-input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
        }
        .date-toggle-input[type="date"]::-moz-calendar-picker-indicator {
            display: none;
        }
        .date-toggle-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .date-toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #f1f1f1;
            color: #888;
            font-size: 12px;
            border-radius: 12px;
            cursor: pointer;
            z-index: 10;
            min-width: 32px;
            height: 24px;
            padding: 0 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .date-toggle-input {
            padding-right: 40px !important;
        }
        @media (max-width: 768px) {
            .date-toggle-btn {
                right: 5px;
                min-width: 28px;
                height: 22px;
                font-size: 11px;
                padding: 0 6px;
            }
            .date-toggle-input {
                padding-right: 36px !important;
            }
        }
        .form-control-sm + .date-toggle-btn {
            height: 20px;
            min-width: 24px;
            font-size: 10px;
            right: 6px;
            padding: 0 5px;
        }
        .form-control-sm.date-toggle-input {
            padding-right: 32px !important;
        }
    </style>
    
    <link rel="stylesheet" href="<?php echo assets_url('assets/custom/summernote-bs4.css') . APPVER; ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/forms/selects/select2.min.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/css/style.css<?= APPVER ?>">
    <?php if(LTR=='rtl') echo '<link rel="stylesheet" type="text/css" href="'.assets_url().'assets/css/style-rtl.css'.APPVER.'">'; ?>
    <!-- END Custom CSS-->
    <script src="<?= assets_url() ?>app-assets/vendors/js/vendors.min.js"></script>
    <script type="text/javascript" src="<?= assets_url() ?>app-assets/vendors/js/ui/jquery.sticky.js"></script>
    <script type="text/javascript"
            src="<?= assets_url() ?>app-assets/vendors/js/charts/jquery.sparkline.min.js"></script>
    <script src="<?php echo assets_url(); ?>assets/portjs/raphael.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url(); ?>assets/portjs/morris.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url('assets/myjs/datepicker.min.js') . APPVER; ?>"></script>
    <script src="<?php echo assets_url('assets/myjs/summernote-bs4.min.js') . APPVER; ?>"></script>
    <script src="<?php echo assets_url('assets/myjs/select2.min.js') . APPVER; ?>"></script>
    <script type="text/javascript">var baseurl = '<?php echo base_url() ?>';
        var crsf_token = '<?=$this->security->get_csrf_token_name()?>';
        var crsf_hash = '<?=$this->security->get_csrf_hash(); ?>';
    </script>
    <script src="<?php echo assets_url(); ?>assets/portjs/accounting.min.js" type="text/javascript"></script>
    <?php accounting() ?>
</head>
<?php
$id = $this->aauth->get_user()->lang;
$this->lang->load($id, $id);
$this->lang->load('part',$id);
if (MENU) {
    include_once('header-va.php');
} else {
    include_once('header-ha.php');
}