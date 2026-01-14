</div>
</div>
</div>
<!-- BEGIN VENDOR JS-->
<style>
/* Prevent dual calendar display - hide regular datepicker when input is in Nepali mode */
input.nepali-input + .ui-datepicker,
input.nepali-input ~ .ui-datepicker,
.nepali-input + .datepicker-dropdown {
    display: none !important;
}

/* Ensure Nepali datepicker appears above modals */
.nepali-datepicker {
    z-index: 1060 !important; /* Higher than Bootstrap modals (1055) */
}

.nepali-datepicker .ndp-element {
    z-index: 1060 !important;
}

.nepali-datepicker .ndp-header,
.nepali-datepicker .ndp-body,
.nepali-datepicker .ndp-footer {
    z-index: 1061 !important;
}

/* Additional z-index for modal context */
.modal .nepali-datepicker {
    z-index: 1060 !important;
}

.modal .nepali-datepicker .ndp-element {
    z-index: 1060 !important;
}

/* Specific styling for modal datepicker fields */
.modal-datepicker-field + .nepali-datepicker,
.modal-datepicker-field ~ .nepali-datepicker {
    z-index: 1060 !important;
}

/* Force high z-index for all Nepali datepickers in modals */
.modal .nepali-datepicker,
.modal .nepali-datepicker * {
    z-index: 1060 !important;
    position: relative;
}

/* Additional override for datepicker containers */
.ndp-container {
    z-index: 1060 !important;
}
</style>
<script type="text/javascript">
    // Prevent datepicker initialization on payment-related fields and toggle-managed fields
    $(document).ready(function() {
        // Disable all datepicker auto-initialization on payment fields
        $('#tsn_date').addClass('no-datepicker-auto');
        $('input[name*="pay"]').addClass('no-datepicker-auto');
        
        // IMMEDIATELY mark all fields that will get EN/NP toggles to prevent double calendars
        $('#sdate, #edate').addClass('no-datepicker-auto toggle-managed');
        $('input[name="invoicedate"], input[name="invocieduedate"]').addClass('no-datepicker-auto toggle-managed');
        $('input[name="start_date"], input[name="end_date"]').addClass('no-datepicker-auto toggle-managed');
        $('.date30, .date30_plus').addClass('no-datepicker-auto toggle-managed');
        $('input[data-toggle="datepicker"]').each(function() {
            var $field = $(this);
            var fieldId = $field.attr('id');
            var fieldName = $field.attr('name');
            
            // Mark fields that will get EN/NP toggle system
            if (fieldId === 'sdate' || fieldId === 'edate' || 
                fieldName === 'invoicedate' || fieldName === 'invocieduedate' ||
                fieldName === 'start_date' || fieldName === 'end_date' ||
                $field.hasClass('date30') || $field.hasClass('date30_plus')) {
                $field.addClass('no-datepicker-auto toggle-managed');
                console.log('Marked field to prevent auto-datepicker:', fieldId || fieldName);
            }
        });
    });
    
    // Initialize datepickers only for fields that are NOT marked as no-datepicker-auto
    $(document).ready(function() {
    // Initialize datepickers only for fields that are NOT marked as no-datepicker-auto
    $(document).ready(function() {
        // Wait a moment for all no-datepicker-auto classes to be applied
        setTimeout(function() {
            $('[data-toggle="datepicker"]:not(.no-datepicker-auto)').each(function() {
                var $field = $(this);
                var fieldId = $field.attr('id');
                var fieldName = $field.attr('name');
                
                console.log('Initializing Bootstrap datepicker for:', fieldId || fieldName);
                
                // Initialize datepicker for non-toggle fields only
                $field.datepicker({
                    autoHide: true,
                    format: '<?php echo $this->config->item('dformat2'); ?>'
                });
            });
            
            // Set default dates only for fields that got datepicker initialized
            $('[data-toggle="datepicker"]:not(.no-datepicker-auto)').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');
        }, 100);
    });

    // Initialize datepickers for specific fields - but only if not toggle-managed
    $(document).ready(function() {
        setTimeout(function() {
            $('#sdate:not(.toggle-managed)').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
            $('#sdate:not(.toggle-managed)').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');
            $('.date30:not(.toggle-managed)').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
            $('.date30:not(.toggle-managed)').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');

            $('.date30_plus:not(.toggle-managed)').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
            $('.date30_plus:not(.toggle-managed)').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('+30 days', strtotime(date('Y-m-d'))))); ?>');
        }, 200);
    });



</script>
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/unslider-min.js"></script>
<script src="<?= assets_url() ?>app-assets/vendors/js/timeline/horizontal-timeline.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url() ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<script src="<?php echo assets_url(); ?>assets/myjs/jquery-ui.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/datatables.min.js"></script>

<script type="text/javascript">var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');
</script>
<script src="<?php echo assets_url('assets/myjs/custom.js') . APPVER; ?>"></script>
<script src="<?php echo assets_url('assets/myjs/basic.js') . APPVER; ?>"></script>
<script src="<?php echo assets_url('assets/myjs/control.js') . APPVER; ?>"></script>

<script type="text/javascript">
    $.ajax({

        url: baseurl + 'manager/pendingtasks',
        dataType: 'json',
        success: function (data) {
            $('#tasklist').html(data.tasks);
            $('#taskcount').html(data.tcount);

        },
        error: function (data) {
            $('#response').html('Error')
        }

    });


</script>
<!-- Nepali Datepicker JS -->
<script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.4.min.js" type="text/javascript"></script>

<script type="text/javascript">
    




// Date Toggle Functionality - Reusable for all modules
function createDateToggle(inputElement) {
    var $input = $(inputElement);
    
    // Skip if already has toggle
    if ($input.closest('.date-toggle-container').length) {
        return;
    }
    
    // Mark this field as toggle-managed to prevent hardcoded datepicker conflicts
    $input.addClass('toggle-managed');
    $input.addClass('no-datepicker-auto'); // Prevent automatic Bootstrap datepicker initialization
    
    // AGGRESSIVE cleanup of any existing Bootstrap datepicker
    try {
        // Destroy any existing datepicker instances
        if ($input.hasClass('hasDatepicker')) {
            $input.datepicker('destroy');
            $input.removeClass('hasDatepicker');
        }
        if ($input.data('datepicker')) {
            $input.datepicker('destroy');
            $input.removeData('datepicker');
        }
        
        // Remove any datepicker-related DOM elements
        $('.datepicker-dropdown').remove();
        $('.ui-datepicker').remove();
        
        // Remove any event handlers that might trigger datepickers
        $input.off('focus.datepicker click.datepicker');
        
        console.log('✅ Aggressive cleanup completed for:', $input.attr('id') || 'field');
    } catch (e) {
        console.log('❌ Datepicker cleanup error:', e);
    }
    
    // Store original input properties
    var originalValue = $input.val();
    
    // Function to convert various date formats to YYYY-MM-DD
    var convertToValidFormat = function(dateStr) {
        if (!dateStr) return '';
        
        // Log the original value for debugging
        if (dateStr && dateStr !== '' && !/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            console.log('Converting date format from:', dateStr);
        }
        
        // Already in YYYY-MM-DD format
        if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            return dateStr;
        }
        
        // DD-MM-YYYY format (most likely from dateformat() function)
        if (/^\d{2}-\d{2}-\d{4}$/.test(dateStr)) {
            var parts = dateStr.split('-');
            var converted = parts[2] + '-' + parts[1] + '-' + parts[0];
            console.log('Converted DD-MM-YYYY to YYYY-MM-DD:', dateStr, '=>', converted);
            return converted;
        }
        
        // DD/MM/YYYY format
        if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
            var parts = dateStr.split('/');
            var converted = parts[2] + '-' + parts[1] + '-' + parts[0];
            console.log('Converted DD/MM/YYYY to YYYY-MM-DD:', dateStr, '=>', converted);
            return converted;
        }
        
        // D-M-YYYY or DD-M-YYYY or D-MM-YYYY formats (flexible)
        if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(dateStr)) {
            var parts = dateStr.split('-');
            var day = parts[0].padStart(2, '0');
            var month = parts[1].padStart(2, '0');
            var year = parts[2];
            var converted = year + '-' + month + '-' + day;
            console.log('Converted flexible DD-MM-YYYY to YYYY-MM-DD:', dateStr, '=>', converted);
            return converted;
        }
        
        // Try to parse as a valid date
        var date = new Date(dateStr);
        if (!isNaN(date.getTime())) {
            var yyyy = date.getFullYear();
            var mm = String(date.getMonth() + 1).padStart(2, '0');
            var dd = String(date.getDate()).padStart(2, '0');
            var converted = yyyy + '-' + mm + '-' + dd;
            console.log('Converted via Date object:', dateStr, '=>', converted);
            return converted;
        }
        
        console.log('Could not convert date format:', dateStr);
        return '';
    };
    
    // Convert the original value to proper format, or use today's date as fallback
    var convertedValue = convertToValidFormat(originalValue);
    if (!convertedValue) {
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var dd = String(today.getDate()).padStart(2, '0');
        convertedValue = yyyy + '-' + mm + '-' + dd;
    }
    
    // Set the converted value and use it as our English date
    originalValue = convertedValue;
    $input.val(originalValue);
    var inputId = $input.attr('id') || 'date_' + Math.random().toString(36).substr(2, 9);
    $input.attr('id', inputId);
    
    // Wrap input with container
    $input.wrap('<div class="date-toggle-container"></div>');
    $input.addClass('date-toggle-input');
    
    // Add minimal toggle button (EN/NP) - starts in English mode with subtle styling
    var toggleBtn = '<button type="button" class="date-toggle-btn" data-target="' + inputId + '" style="border:1px solid #ccc;background:#f8f9fa;color:#666;font-size:11px;padding:1px 8px;margin-left:4px;border-radius:4px;line-height:1.2;box-shadow:none;">EN</button>';
    $input.after(toggleBtn);
    
    var $container = $input.closest('.date-toggle-container');
    var $toggleBtn = $container.find('.date-toggle-btn');
    
    // Store date values and state
    var englishDate = originalValue;
    var nepaliDate = '';
    var currentMode = 'english';
    var nepaliPickerInstance = null;
    var originalFormat = 'english'; // Track what format user originally entered
    
    // Convert initial English date to Nepali if exists
    if (englishDate && typeof NepaliFunctions !== 'undefined') {
        try {
            var dateParts = englishDate.split('-');
            if (dateParts.length === 3) {
                var nepaliDateObj = NepaliFunctions.AD2BS({
                    year: parseInt(dateParts[0]),
                    month: parseInt(dateParts[1]),
                    day: parseInt(dateParts[2])
                });
                nepaliDate = nepaliDateObj.year + '-' + 
                    String(nepaliDateObj.month).padStart(2, '0') + '-' + 
                    String(nepaliDateObj.day).padStart(2, '0');
            }
        } catch (e) {
            // Silent fail for invalid dates
        }
    }
    
    // Store original format in a hidden field for form submission
    var hiddenFormatField = '<input type="hidden" name="' + ($input.attr('name') || inputId) + '_format" value="english">';
    $input.after(hiddenFormatField);
    var $formatField = $input.siblings('input[name*="_format"]');
    
    // Function to setup English mode
    function setupEnglishMode() {
        currentMode = 'english';
        $toggleBtn.text('EN').css({
            'background': '#f8f9fa',
            'color': '#666',
            'border': '1px solid #ccc'
        });

        console.log('✅ Setting up English mode for:', $input.attr('id'));

        // Destroy Nepali datepicker if exists
        if (nepaliPickerInstance || $input.data('nepali-initialized')) {
            try {
                if (typeof inputElement.NepaliDatePicker === 'function') {
                    inputElement.NepaliDatePicker('destroy');
                }
            } catch (e) {
                $input.off('.nepaliDatePicker');
            }
            nepaliPickerInstance = null;
            $input.removeData('nepali-initialized');
            $input.removeData('nepali-instance');
        }

        // Clean up any existing datepickers
        if ($input.hasClass('hasDatepicker')) {
            $input.datepicker('destroy');
            $input.removeClass('hasDatepicker');
        }
        
        // Clean up Bootstrap datepicker if exists
        if ($input.data('datepicker')) {
            try {
                $input.datepicker('destroy');
                $input.removeData('datepicker');
            } catch (e) {
                console.log('Bootstrap datepicker cleanup error:', e);
            }
        }
        
        // Hide any visible datepickers
        $('.ui-datepicker:visible').hide();
        $('.datepicker-dropdown:visible').hide();

        // Remove Nepali input class and attributes
        $input.removeClass('nepali-input')
              .removeAttr('readonly')
              .removeAttr('placeholder');

        // ALWAYS use HTML5 date input for English mode (like stock adjustment)
        console.log('🔄 Converting to HTML5 date input for:', $input.attr('id') || 'field');
        
        // Convert to HTML5 date input (this is what makes stock adjustment work)
        $input.attr('type', 'date');
        
        // Set the value in YYYY-MM-DD format
        $input.val(englishDate);
        console.log('✅ Set HTML5 date input value for', $input.attr('id') || 'field', ':', englishDate);
        
        // Auto-focus and open calendar for better UX (like stock adjustment)
        setTimeout(function() {
            $input.focus();
            
            // Trigger click to open native calendar
            setTimeout(function() {
                var clickEvent = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                inputElement.dispatchEvent(clickEvent);
            }, 50);
        }, 100);
    }
    
    // Function to setup Nepali mode
    // Function to setup Nepali mode
    function setupNepaliMode() {
        currentMode = 'nepali';
        $toggleBtn.text('NE').css({
            'background': '#f8f9fa',
            'color': '#666',
            'border': '1px solid #ccc'
        });
        
        console.log('✅ Setting up Nepali mode for:', $input.attr('id'));
        
        // Convert input to text type for Nepali datepicker
        $input.attr('type', 'text')
              .removeAttr('readonly')
              .attr('placeholder', 'Click to select Nepali date')
              .addClass('nepali-input');
        
        // Clean up any existing regular datepickers to prevent dual display
        if ($input.hasClass('hasDatepicker')) {
            $input.datepicker('destroy');
            $input.removeClass('hasDatepicker');
        }
        $('.ui-datepicker:visible').hide();
        
        // Set Nepali date value
        $input.val(nepaliDate);
        
        // Remove any existing nepali datepicker first
        if ($input.data('nepali-initialized')) {
            try {
                if (typeof inputElement.NepaliDatePicker === 'function') {
                    inputElement.NepaliDatePicker('destroy');
                }
            } catch (e) {
                $input.off('.nepaliDatePicker');
            }
            $input.removeData('nepali-initialized');
            $input.removeData('nepali-instance');
        }
        
        // Initialize nepali datepicker with improved initialization
        var initializeNepaliPicker = function() {
            try {
                if (typeof inputElement.NepaliDatePicker === 'function') {
                    // For stock adjustment, use more reliable initialization
                    var pickerConfig = {
                        dateFormat: "YYYY-MM-DD",
                        onSelect: function(dateStr) {
                            // Handle both string and object returns from datepicker
                            var formattedDate;
                            if (typeof dateStr === 'string') {
                                formattedDate = dateStr;
                            } else if (typeof dateStr === 'object' && dateStr !== null) {
                                // If it's an object with year, month, day properties
                                if (dateStr.year && dateStr.month && dateStr.day) {
                                    formattedDate = dateStr.year + '-' + 
                                        String(dateStr.month).padStart(2, '0') + '-' + 
                                        String(dateStr.day).padStart(2, '0');
                                } else {
                                    // Try to extract date from object
                                    formattedDate = dateStr.toString();
                                }
                            } else {
                                formattedDate = dateStr;
                            }
                            
                            nepaliDate = formattedDate;
                            $input.val(nepaliDate);
                            
                            // Convert to English
                            try {
                                var dateParts = formattedDate.split('-');
                                if (dateParts.length === 3) {
                                    var englishDateObj = NepaliFunctions.BS2AD({
                                        year: parseInt(dateParts[0]),
                                        month: parseInt(dateParts[1]),
                                        day: parseInt(dateParts[2])
                                    });
                                    englishDate = englishDateObj.year + '-' + 
                                        String(englishDateObj.month).padStart(2, '0') + '-' + 
                                        String(englishDateObj.day).padStart(2, '0');
                                }
                            } catch (e) {
                                // Silent fail
                            }
                        }
                    };
                    
                    // Special handling for stock adjustment and other critical fields
                    if ($input.attr('id') === 'adjustment_date' || $input.hasClass('critical-date') || $input.closest('.modal').length > 0) {
                        pickerConfig.container = 'body';
                        pickerConfig.zIndex = 1060;
                        pickerConfig.autoHide = true;
                        // Add custom CSS class for additional styling
                        $input.addClass('modal-datepicker-field');
                    }
                    
                    nepaliPickerInstance = inputElement.NepaliDatePicker(pickerConfig);
                    $input.data('nepali-initialized', true);
                    $input.data('nepali-instance', nepaliPickerInstance);
                    
                    console.log('Nepali datepicker initialized for:', $input.attr('id') || $input[0].className);
                }
            } catch (e) {
                console.error('Failed to initialize Nepali datepicker:', e);
                // Retry once more after a short delay
                setTimeout(function() {
                    try {
                        if (typeof inputElement.NepaliDatePicker === 'function') {
                            nepaliPickerInstance = inputElement.NepaliDatePicker({
                                dateFormat: "YYYY-MM-DD"
                            });
                            $input.data('nepali-initialized', true);
                            $input.data('nepali-instance', nepaliPickerInstance);
                        }
                    } catch (retryError) {
                        console.error('Retry failed for Nepali datepicker:', retryError);
                    }
                }, 500);
            }
        };
        
        // Try immediate initialization first, then fallback to delayed
        initializeNepaliPicker();
        
        // Fallback initialization with delay for problematic cases
        if (!$input.data('nepali-initialized')) {
            setTimeout(initializeNepaliPicker, 200);
        }
    }
    
    // Button click handlers
    $toggleBtn.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentMode === 'english') {
            // Convert current English date to Nepali before switching
            var currentEnglishValue = $input.val();
            if (currentEnglishValue && typeof NepaliFunctions !== 'undefined') {
                try {
                    var dateParts = currentEnglishValue.split('-');
                    if (dateParts.length === 3) {
                        // Handle different date formats
                        var year, month, day;
                        if (dateParts[0].length === 4) {
                            // YYYY-MM-DD format
                            year = parseInt(dateParts[0]);
                            month = parseInt(dateParts[1]);
                            day = parseInt(dateParts[2]);
                        } else if (dateParts[2].length === 4) {
                            // DD-MM-YYYY format
                            day = parseInt(dateParts[0]);
                            month = parseInt(dateParts[1]);
                            year = parseInt(dateParts[2]);
                        }
                        
                        if (year && month && day) {
                            var nepaliDateObj = NepaliFunctions.AD2BS({
                                year: year,
                                month: month,
                                day: day
                            });
                            if (nepaliDateObj && nepaliDateObj.year && nepaliDateObj.month && nepaliDateObj.day) {
                                nepaliDate = nepaliDateObj.year + '-' + 
                                    String(nepaliDateObj.month).padStart(2, '0') + '-' + 
                                    String(nepaliDateObj.day).padStart(2, '0');
                            }
                        }
                    }
                } catch (e) {
                    // Silent fail
                }
            }
            
            // Switch to Nepali mode and open calendar
            setupNepaliMode();
            originalFormat = 'nepali'; // User is entering Nepali date
            $formatField.val('nepali');
            
            // Show Nepali calendar with proper delay for initialization
            setTimeout(function() {
                try {
                    console.log('Attempting to open Nepali calendar for:', $input.attr('id'));
                    
                    // Multiple strategies to open the calendar
                    var openCalendar = function() {
                        // Strategy 1: Use stored instance
                        var instance = $input.data('nepali-instance');
                        if (instance && typeof instance.show === 'function') {
                            instance.show();
                            return true;
                        }
                        
                        // Strategy 2: Focus and click the input
                        $input.focus();
                        var clickEvent = new MouseEvent('click', {
                            bubbles: true,
                            cancelable: true,
                            view: window
                        });
                        inputElement.dispatchEvent(clickEvent);
                        
                        // Strategy 3: Trigger focus event
                        setTimeout(function() {
                            var focusEvent = new FocusEvent('focus', {
                                bubbles: true,
                                cancelable: true,
                                view: window
                            });
                            inputElement.dispatchEvent(focusEvent);
                        }, 50);
                        
                        return false;
                    };
                    
                    // Try to open calendar
                    if (!openCalendar()) {
                        // If initial attempt fails, reinitialize and try again
                        setTimeout(function() {
                            if (typeof inputElement.NepaliDatePicker === 'function') {
                                try {
                                    var newInstance = inputElement.NepaliDatePicker({
                                        dateFormat: "YYYY-MM-DD",
                                        autoHide: true
                                    });
                                    $input.data('nepali-instance', newInstance);
                                    $input.data('nepali-initialized', true);
                                    
                                    // Try to open again
                                    setTimeout(function() {
                                        $input.focus();
                                        $input.trigger('click');
                                        var clickEvent = new MouseEvent('click', {
                                            bubbles: true,
                                            cancelable: true,
                                            view: window
                                        });
                                        inputElement.dispatchEvent(clickEvent);
                                    }, 100);
                                } catch (e) {
                                    console.error('Failed to reinitialize Nepali datepicker:', e);
                                }
                            }
                        }, 200);
                    }
                } catch (e) {
                    console.error('Error opening Nepali calendar:', e);
                }
            }, 300); // Wait for initialization to complete
        } else {
            // Convert current Nepali date to English before switching
            var currentNepaliValue = $input.val();
            if (currentNepaliValue && typeof NepaliFunctions !== 'undefined') {
                try {
                    var dateParts = currentNepaliValue.split('-');
                    if (dateParts.length === 3) {
                        var englishDateObj = NepaliFunctions.BS2AD({
                            year: parseInt(dateParts[0]),
                            month: parseInt(dateParts[1]),
                            day: parseInt(dateParts[2])
                        });
                        if (englishDateObj && englishDateObj.year && englishDateObj.month && englishDateObj.day) {
                            englishDate = englishDateObj.year + '-' + 
                                String(englishDateObj.month).padStart(2, '0') + '-' + 
                                String(englishDateObj.day).padStart(2, '0');
                        }
                    }
                } catch (e) {
                    // Silent fail
                }
            }
            
            setupEnglishMode();
            originalFormat = 'english'; // User is entering English date
            $formatField.val('english');
            
            // Force focus and open calendar after switching to English mode
            setTimeout(function() {
                console.log('Attempting to open English calendar, input type:', $input.attr('type'));
                
                if ($input.attr('type') === 'date') {
                    // For HTML5 date inputs
                    $input.focus();
                    
                    // Trigger click to open native calendar
                    setTimeout(function() {
                        var clickEvent = new MouseEvent('click', {
                            bubbles: true,
                            cancelable: true,
                            view: window
                        });
                        inputElement.dispatchEvent(clickEvent);
                        
                        // Also try triggering the input event
                        setTimeout(function() {
                            $input.trigger('click');
                            $input[0].showPicker && $input[0].showPicker(); // Modern browsers
                        }, 50);
                    }, 50);
                    
                    console.log('Triggered HTML5 date input calendar');
                } else {
                    // For text inputs with datepicker
                    try {
                        if ($input.hasClass('hasDatepicker')) {
                            $input.datepicker('show');
                            console.log('Opened jQuery UI datepicker');
                        } else if (typeof $input.datepicker === 'function') {
                            // Try to open bootstrap datepicker
                            $input.datepicker('show');
                            console.log('Opened Bootstrap datepicker');
                        } else {
                            $input.focus();
                            console.log('Focused input (no datepicker found)');
                        }
                    } catch (e) {
                        console.log('Could not open datepicker:', e);
                        $input.focus();
                    }
                }
            }, 200); // Increased delay to ensure setup is complete
        }
    });
    
    // Handle English date changes
    $input.on('change', function() {
        if (currentMode === 'english') {
            englishDate = $input.val();
            // Convert to Nepali
            if (englishDate && typeof NepaliFunctions !== 'undefined') {
                try {
                    var dateParts = englishDate.split('-');
                    if (dateParts.length === 3) {
                        var nepaliDateObj = NepaliFunctions.AD2BS({
                            year: parseInt(dateParts[0]),
                            month: parseInt(dateParts[1]),
                            day: parseInt(dateParts[2])
                        });
                        nepaliDate = nepaliDateObj.year + '-' + 
                            String(nepaliDateObj.month).padStart(2, '0') + '-' + 
                            String(nepaliDateObj.day).padStart(2, '0');
                    }
                } catch (e) {
                    // Silent fail
                }
            }
        }
    });
    
    // Allow input interaction in Nepali mode
    $input.on('click focus', function(e) {
        if (currentMode === 'nepali' && $input.data('nepali-initialized')) {
            e.stopPropagation();
            return true;
        }
    });
    
    // Initialize in English mode
    setupEnglishMode();
}
document.addEventListener("DOMContentLoaded", function () {
    // Function to process date displays (for both static and dynamic content)
    function processDateDisplays(container) {
        container = container || document;
        
        // Handle date display format preservation in tables and listings
        container.querySelectorAll(".nepali-date, .date-display, .dual-date-display").forEach(function (el) {
            var rawDate = el.getAttribute("data-raw") || el.getAttribute("data-english-date"); // Support both attributes
            var originalFormat = el.getAttribute("data-format") || 'english'; // Format user originally entered
            
            if (rawDate && typeof NepaliFunctions !== "undefined") {
                try {
                    // First, normalize the date to YYYY-MM-DD format
                    var normalizedDate = rawDate;
                    
                    // Handle different date formats that might come from database
                    if (/^\d{4}-\d{2}-\d{2}$/.test(rawDate)) {
                        // Already in YYYY-MM-DD format
                        normalizedDate = rawDate;
                    } else if (/^\d{2}-\d{2}-\d{4}$/.test(rawDate)) {
                        // DD-MM-YYYY format, convert to YYYY-MM-DD
                        var parts = rawDate.split("-");
                        normalizedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
                    } else if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(rawDate)) {
                        // MM/DD/YYYY or M/D/YYYY format
                        var parts = rawDate.split("/");
                        normalizedDate = parts[2] + "-" + String(parts[0]).padStart(2, '0') + "-" + String(parts[1]).padStart(2, '0');
                    }
                    
                    var parts = normalizedDate.split("-");
                    if (parts.length === 3) {
                        var year = parseInt(parts[0]);
                        var month = parseInt(parts[1]);
                        var day = parseInt(parts[2]);
                        
                        var displayDate, convertedDate;
                        
                        // If user originally entered Nepali date, show Nepali date as primary
                        if (originalFormat === 'nepali') {
                            // Convert English date to Nepali for display
                            var nepaliDateObj = NepaliFunctions.AD2BS({
                                year: year,
                                month: month,
                                day: day
                            });
                            displayDate = nepaliDateObj.year + "-" +
                                        String(nepaliDateObj.month).padStart(2, '0') + "-" +
                                        String(nepaliDateObj.day).padStart(2, '0');
                            
                            // Show English in bracket
                            convertedDate = normalizedDate;
                        } else {
                            // User originally entered English date, show English as primary
                            displayDate = normalizedDate;
                            
                            // Convert to Nepali for bracket
                            var nepaliDateObj = NepaliFunctions.AD2BS({
                                year: year,
                                month: month,
                                day: day
                            });
                            convertedDate = nepaliDateObj.year + "-" +
                                          String(nepaliDateObj.month).padStart(2, '0') + "-" +
                                          String(nepaliDateObj.day).padStart(2, '0');
                        }
                        
                        // Update the main date display
                        el.textContent = displayDate;
                        
                        // Show the converted date in bracket if bracket element exists
                        var englishSpan = el.parentNode.querySelector('.english-date');
                        if (englishSpan) {
                            englishSpan.textContent = "(" + convertedDate + ")";
                            englishSpan.style.display = 'inline';
                        }
                    }
                } catch (e) {
                    console.error('Date display conversion error:', e, 'for date:', rawDate);
                    var englishSpan = el.parentNode.querySelector('.english-date');
                    if (englishSpan) {
                        englishSpan.textContent = "(Invalid)";
                        englishSpan.style.display = 'inline';
                    }
                }
            }
        });
        
        // Handle POS invoice date displays (for AJAX-loaded content without explicit format markers)
        container.querySelectorAll("td").forEach(function(cell) {
            var cellText = cell.textContent.trim();
            
            // Check if this looks like a date cell (DD-MM-YYYY format)
            if (/^\d{2}-\d{2}-\d{4}$/.test(cellText)) {
                var parts = cellText.split('-');
                var day = parseInt(parts[0]);
                var month = parseInt(parts[1]); 
                var year = parseInt(parts[2]);
                
                // Skip if this is obviously not a date (invalid ranges)
                if (month < 1 || month > 12 || day < 1 || day > 31) {
                    return;
                }
                
                try {
                    // Convert DD-MM-YYYY to Nepali date and show both
                    var nepaliDateObj = NepaliFunctions.AD2BS({
                        year: year,
                        month: month,
                        day: day
                    });
                    
                    var nepaliDate = nepaliDateObj.year + "-" +
                                   String(nepaliDateObj.month).padStart(2, '0') + "-" +
                                   String(nepaliDateObj.day).padStart(2, '0');
                    
                    // Update cell to show both dates
                    cell.innerHTML = '<span class="primary-date">' + cellText + '</span> <span class="converted-date" style="color:#666; font-size:0.9em;">(' + nepaliDate + ')</span>';
                } catch (e) {
                    // Silent fail for invalid dates
                }
            }
        });
    }
    
    // Process initial page load
    processDateDisplays();
    
    // Watch for DataTable updates (POS invoices use AJAX loading)
    $(document).on('draw.dt', function() {
        // Process newly loaded content after DataTable draws
        setTimeout(function() {
            processDateDisplays();
        }, 100);
    });
    
    // Watch for other dynamically added content
    if (window.MutationObserver) {
        var dateObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    setTimeout(function() {
                        processDateDisplays();
                    }, 50);
                }
            });
        });
        
        // Start observing tables for content changes
        var tables = document.querySelectorAll('table');
        tables.forEach(function(table) {
            dateObserver.observe(table, {
                childList: true,
                subtree: true
            });
        });
    }
    
    // Original functionality for elements without format specification
    document.querySelectorAll(".nepali-date:not([data-format])").forEach(function (el) {
        var rawDate = el.getAttribute("data-raw"); // This is actually an English/AD date from database
        if (rawDate && typeof NepaliFunctions !== "undefined") {
            try {
                // First, normalize the date to YYYY-MM-DD format
                var normalizedDate = rawDate;
                
                // Handle different date formats that might come from database
                if (/^\d{4}-\d{2}-\d{2}$/.test(rawDate)) {
                    // Already in YYYY-MM-DD format
                    normalizedDate = rawDate;
                } else if (/^\d{2}-\d{2}-\d{4}$/.test(rawDate)) {
                    // DD-MM-YYYY format, convert to YYYY-MM-DD
                    var parts = rawDate.split("-");
                    normalizedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
                } else if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(rawDate)) {
                    // MM/DD/YYYY or M/D/YYYY format
                    var parts = rawDate.split("/");
                    normalizedDate = parts[2] + "-" + String(parts[0]).padStart(2, '0') + "-" + String(parts[1]).padStart(2, '0');
                }
                
                var parts = normalizedDate.split("-");
                if (parts.length === 3) {
                    var year = parseInt(parts[0]);
                    var month = parseInt(parts[1]);
                    var day = parseInt(parts[2]);
                    
                    // Intelligent detection: Nepali years are typically 2000+ (like 2082), English years are usually 1900-2100
                    var isNepaliDate = year > 2070; // Nepali dates are typically above 2070
                    
                    var convertedDate, displayFormat;
                    
                    if (isNepaliDate) {
                        // Input is Nepali date, convert to English for bracket
                        convertedDate = NepaliFunctions.BS2AD({
                            year: year,
                            month: month,
                            day: day
                        });
                        displayFormat = convertedDate.year + "-" +
                                      String(convertedDate.month).padStart(2, '0') + "-" +
                                      String(convertedDate.day).padStart(2, '0');
                    } else {
                        // Input is English date, convert to Nepali for bracket
                        convertedDate = NepaliFunctions.AD2BS({
                            year: year,
                            month: month,
                            day: day
                        });
                        displayFormat = convertedDate.year + "-" +
                                      String(convertedDate.month).padStart(2, '0') + "-" +
                                      String(convertedDate.day).padStart(2, '0');
                    }
                    
                    // Show the converted date in the bracket
                    var englishSpan = el.parentNode.querySelector('.english-date');
                    if (englishSpan) {
                        englishSpan.textContent = "(" + displayFormat + ")";
                        englishSpan.style.display = 'inline';
                    }
                }
            } catch (e) {
                console.error('Date conversion error:', e, 'for date:', rawDate);
                var englishSpan = el.parentNode.querySelector('.english-date');
                if (englishSpan) {
                    englishSpan.textContent = "(Invalid)";
                    englishSpan.style.display = 'inline';
                }
            }
        }
    });
});
// Auto-initialize for common date input selectors
$(document).ready(function() {
    console.log('Initializing date toggles...');
    
    // Initialize for various modules - add more selectors as needed
    var dateSelectors = [
        '#adjustment_date',        // Stock adjustment
        '.date-picker',           // Class-based selector
        'input[type="date"]',     // All HTML5 date inputs
        '.expiry_date',           // Expiry date inputs (purchase orders)
        '#start_date',            // POS invoice filter start date
        '#end_date',              // POS invoice filter end date
        'input[name="invoicedate"]',     // POS invoice date / Purchase order date
        'input[name="invocieduedate"]',  // Purchase order due date
        'input[name="start_date"]',      // General start date fields
        'input[name="end_date"]',        // General end date fields
        'input[name="date"]',            // Transaction date fields
        '#sdate',                        // Income Statement from date
        '#edate',                        // Income Statement to date  
        '.date30',                       // 30-day date fields
        '.date30_plus',                  // 30-day plus date fields
        // Exclude payment date fields and other problematic selectors
        'input[name$="date"]:not([name*="pay"]):not([id="tsn_date"]):not([id="tsn_due"])',  // Date fields but not payment dates
        'input[name^="invoice"]:not([name*="pay"])',  // Invoice date fields but not payment
    ];
    
    // Function to initialize date toggle for an element
    function initializeDateToggle(element) {
        var $el = $(element);
        
        // Additional safety checks to prevent interference with payment functionality
        var elementId = $el.attr('id');
        var elementName = $el.attr('name');
        
        console.log('Checking element for date toggle:', elementId, elementName, 'type:', $el.attr('type'));
        
        // Skip payment-related fields and POS due date fields (but allow purchase order due date)
        if (elementId === 'tsn_date' || 
            (elementId === 'tsn_due' && elementName !== 'invocieduedate') ||  // Skip POS due date but allow purchase order due date
            (elementName && (elementName.includes('pay') || elementName.includes('payment'))) ||
            $el.closest('.modal').length > 0) {
            console.log('Skipping date toggle for payment-related/POS due date field:', elementId, elementName);
            return;
        }
        
        // Skip if already has toggle or if NepaliFunctions is not available
        if (!$el.hasClass('date-toggle-input') && typeof NepaliFunctions !== 'undefined') {
            // For HTML5 inputs, try to convert existing value instead of clearing it
            var currentVal = $el.val();
            console.log('Initializing date toggle for element:', element.tagName, 'id:', elementId, 'class:', $el.attr('class'), 'type:', $el.attr('type'), 'value:', currentVal);
            
            if ($el.attr('type') === 'date' && currentVal) {
                // Try to convert the value to proper format
                var convertedVal = '';
                
                // Already in YYYY-MM-DD format
                if (/^\d{4}-\d{2}-\d{2}$/.test(currentVal)) {
                    convertedVal = currentVal;
                    console.log('Date already in correct format:', currentVal);
                } 
                // DD-MM-YYYY format (most common from database)
                else if (/^\d{2}-\d{2}-\d{4}$/.test(currentVal)) {
                    var parts = currentVal.split('-');
                    convertedVal = parts[2] + '-' + parts[1] + '-' + parts[0];
                    console.log('Converted DD-MM-YYYY to YYYY-MM-DD:', currentVal, '=>', convertedVal);
                }
                // DD/MM/YYYY format
                else if (/^\d{2}\/\d{2}\/\d{4}$/.test(currentVal)) {
                    var parts = currentVal.split('/');
                    convertedVal = parts[2] + '-' + parts[1] + '-' + parts[0];
                    console.log('Converted DD/MM/YYYY to YYYY-MM-DD:', currentVal, '=>', convertedVal);
                }
                // Flexible DD-MM-YYYY format
                else if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(currentVal)) {
                    var parts = currentVal.split('-');
                    var day = parts[0].padStart(2, '0');
                    var month = parts[1].padStart(2, '0');
                    var year = parts[2];
                    convertedVal = year + '-' + month + '-' + day;
                    console.log('Converted flexible DD-MM-YYYY to YYYY-MM-DD:', currentVal, '=>', convertedVal);
                }
                
                if (convertedVal) {
                    $el.val(convertedVal);
                    console.log('Set converted value:', convertedVal);
                } else {
                    console.log('Could not convert date format, clearing:', currentVal);
                    $el.val('');
                }
            }
            
            // Special marking for critical date fields
            if (elementId === 'adjustment_date' || 
                elementId === 'start_date' || 
                elementId === 'end_date' ||
                elementName === 'invoicedate' ||
                elementName === 'start_date' ||
                elementName === 'end_date' ||
                elementName === 'date') {  // Transaction date field
                $el.addClass('critical-date');
                console.log('Marked as critical date field:', elementId || elementName);
            }
            
            createDateToggle(element);
            console.log('Date toggle created for:', elementId || $el.attr('class'));
        }
    }
    
    // Initial setup for existing elements
    setTimeout(function() {
        dateSelectors.forEach(function(selector) {
            $(selector).each(function() {
                initializeDateToggle(this);
            });
        });
    }, 100); // Small delay to ensure DOM is ready
    
    // Watch for dynamically added date inputs using MutationObserver
    if (window.MutationObserver) {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    $(mutation.addedNodes).each(function() {
                        if (this.nodeType === 1) { // Element node
                            var $node = $(this);
                            // Check if the added node itself is a date input
                            dateSelectors.forEach(function(selector) {
                                if ($node.is(selector)) {
                                    setTimeout(function() {
                                        initializeDateToggle($node[0]);
                                    }, 10);
                                }
                            });
                            // Check for date inputs within the added node
                            dateSelectors.forEach(function(selector) {
                                $node.find(selector).each(function() {
                                    setTimeout(function() {
                                        initializeDateToggle(this);
                                    }.bind(this), 10);
                                });
                            });
                        }
                    });
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});

// PRESERVE ORIGINAL DATE FORMAT FOR DISPLAY
$(document).ready(function() {
    // Intercept form submissions to preserve original date format information
    $('form').on('submit', function(e) {
        var $form = $(this);
        
        // Add format information for each date field that has a toggle
        $form.find('.date-toggle-input').each(function() {
            var $dateInput = $(this);
            var $formatField = $dateInput.siblings('input[name*="_format"]');
            var currentMode = $dateInput.closest('.date-toggle-container').find('.date-toggle-btn').text();
            
            if ($formatField.length) {
                // Update format field based on current toggle state
                $formatField.val(currentMode === 'NE' ? 'nepali' : 'english');
                
                // Also add a general field to indicate this form had date format toggles
                if (!$form.find('input[name="has_date_formats"]').length) {
                    $form.append('<input type="hidden" name="has_date_formats" value="1">');
                }
            }
        });
    });
});

// NEPALI DATE CONVERSION FOR REPORTS FILTERING
$(document).ready(function() {
    // Function to convert Nepali date to English date for database queries
    function convertNepaliDateForDatabase(dateStr) {
        if (!dateStr || typeof NepaliFunctions === "undefined") {
            return dateStr;
        }
        
        try {
            // Parse the date string
            var dateParts = dateStr.split('-');
            if (dateParts.length !== 3) {
                return dateStr;
            }
            
            var day, month, year;
            
            // Handle DD-MM-YYYY format (most common in forms)
            if (dateParts[2].length === 4) {
                day = parseInt(dateParts[0]);
                month = parseInt(dateParts[1]);
                year = parseInt(dateParts[2]);
            }
            // Handle YYYY-MM-DD format
            else if (dateParts[0].length === 4) {
                year = parseInt(dateParts[0]);
                month = parseInt(dateParts[1]);
                day = parseInt(dateParts[2]);
            } else {
                return dateStr;
            }
            
            // If year > 2070, it's likely a Nepali date
            if (year > 2070) {
                var englishDate = NepaliFunctions.BS2AD({
                    year: year,
                    month: month,
                    day: day
                });
                
                // Return in DD-MM-YYYY format (same as input format)
                if (dateParts[2].length === 4) {
                    return String(englishDate.day).padStart(2, '0') + '-' +
                           String(englishDate.month).padStart(2, '0') + '-' +
                           englishDate.year;
                } else {
                    return englishDate.year + '-' +
                           String(englishDate.month).padStart(2, '0') + '-' +
                           String(englishDate.day).padStart(2, '0');
                }
            }
            
            return dateStr; // Already English date
        } catch (e) {
            console.error('Error converting Nepali date:', e);
            return dateStr;
        }
    }
    
    // Intercept form submissions for reports
    $('form').on('submit', function(e) {
        var $form = $(this);
        
        // Check if this is a reports form or POS invoice filter form
        var hasDateFields = $form.find('input[name*="date"]').length > 0 ||
                           $form.find('input[name="sdate"], input[name="edate"]').length > 0 ||
                           $form.find('input[name="from_date"], input[name="to_date"]').length > 0;
        
        // Also check if this is a POS invoice management form
        var isPosForm = window.location.href.includes('invoices') || 
                       window.location.href.includes('pos') ||
                       $form.attr('action') && $form.attr('action').includes('invoice');
        
        if (hasDateFields || isPosForm) {
            // Convert all date fields
            $form.find('input[name*="date"], input[name="sdate"], input[name="edate"], input[name="from_date"], input[name="to_date"]').each(function() {
                var $field = $(this);
                var originalValue = $field.val();
                
                if (originalValue) {
                    var convertedValue = convertNepaliDateForDatabase(originalValue);
                    if (convertedValue !== originalValue) {
                        console.log('Converting Nepali date for database:', originalValue, '=>', convertedValue);
                        $field.val(convertedValue);
                    }
                }
            });
        }
    });
    
    // Also handle AJAX form submissions
    $(document).ajaxSend(function(event, xhr, options) {
        if (options.data && typeof options.data === 'string') {
            var params = new URLSearchParams(options.data);
            var modified = false;
            
            // Check and convert date parameters (including POS invoice filter parameters)
            ['sdate', 'edate', 'sd', 'ed', 'from_date', 'to_date', 'start_date', 'end_date', 'date_from', 'date_to'].forEach(function(paramName) {
                if (params.has(paramName)) {
                    var originalValue = params.get(paramName);
                    var convertedValue = convertNepaliDateForDatabase(originalValue);
                    if (convertedValue !== originalValue) {
                        console.log('Converting Nepali date for AJAX:', paramName, originalValue, '=>', convertedValue);
                        params.set(paramName, convertedValue);
                        modified = true;
                    }
                }
            });
            
            // Also check for any parameter containing 'date' in the name
            for (let [key, value] of params.entries()) {
                if (key.toLowerCase().includes('date') && value) {
                    var convertedValue = convertNepaliDateForDatabase(value);
                    if (convertedValue !== value) {
                        console.log('Converting Nepali date for AJAX (dynamic):', key, value, '=>', convertedValue);
                        params.set(key, convertedValue);
                        modified = true;
                    }
                }
            }
            
            if (modified) {
                options.data = params.toString();
            }
        }
    });
});




</script>


</body>
</html>

