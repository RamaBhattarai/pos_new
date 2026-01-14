<div class="content-wrapper">
    <!-- Include Toastr for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Hidden notify div for global email system compatibility -->
    <div id="notify" class="alert" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>
    
    <div class="content-header row">
        <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Party Confirmations</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('supplier') ?>">Suppliers</a></li>
                        <li class="breadcrumb-item active">Party Confirmations</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-4 col-12">
            <div class="btn-group float-md-right">
                <button class="btn btn-info btn-sm" id="refresh-alerts">
                    <i class="ft-refresh-cw"></i> Refresh Alerts
                </button>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Alert Cards -->
        <div class="row" id="alert-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="ft-alert-triangle text-warning"></i> Suppliers Requiring Confirmation
                        </h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <span class="badge badge-warning" id="alert-count">0</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body" id="suppliers-needing-confirmation">
                            <div class="text-center">
                                <i class="ft-loader icon-spin font-large-1"></i>
                                <p>Loading suppliers requiring confirmation...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation History -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="ft-list text-info"></i> Confirmation History
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="confirmations-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Supplier</th>
                                            <th>Amount</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Generated Date</th>
                                            <th>Sent Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($confirmations) && is_array($confirmations)): ?>
                                            <?php foreach ($confirmations as $confirmation): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($confirmation['supplier_name'] ?? 'Unknown') ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            ₹<?= number_format($confirmation['total_amount'] ?? 0) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= isset($confirmation['confirmation_period_start']) ? date('d-M-Y', strtotime($confirmation['confirmation_period_start'])) : 'N/A' ?> to
                                                        <?= isset($confirmation['confirmation_period_end']) ? date('d-M-Y', strtotime($confirmation['confirmation_period_end'])) : 'N/A' ?>
                                                    </td>
                                                    <td>
                                                        <?php if (($confirmation['status'] ?? '') == 'sent'): ?>
                                                            <span class="badge badge-success">Sent</span>
                                                        <?php elseif (($confirmation['status'] ?? '') == 'generated'): ?>
                                                            <span class="badge badge-warning">Generated</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary"><?= ucfirst($confirmation['status'] ?? 'unknown') ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        // Use generated_date if available, otherwise use created_at
                                                        $generated_date = !empty($confirmation['generated_date']) && $confirmation['generated_date'] != '0000-00-00 00:00:00'
                                                            ? $confirmation['generated_date'] 
                                                            : ($confirmation['created_at'] ?? '');
                                                        
                                                        // Check if the date is valid
                                                        if (!empty($generated_date) && 
                                                            $generated_date != '0000-00-00 00:00:00' && 
                                                            $generated_date != '1000-01-01 00:00:00' &&
                                                            strtotime($generated_date) !== false) {
                                                            $timestamp = strtotime($generated_date);
                                                            $year = date('Y', $timestamp);
                                                            // Check for reasonable year range
                                                            if ($year >= 2020 && $year <= date('Y') + 1) {
                                                                echo date('d-M-Y H:i', $timestamp);
                                                            } else {
                                                                echo date('d-M-Y H:i'); // Current date if year is unreasonable
                                                            }
                                                        } else {
                                                            echo date('d-M-Y H:i'); // Current date if no valid date found
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $sent_date = $confirmation['sent_date'] ?? '';
                                                        if (!empty($sent_date) && 
                                                            $sent_date != '0000-00-00 00:00:00' && 
                                                            $sent_date != '1000-01-01 00:00:00' &&
                                                            strtotime($sent_date) !== false) {
                                                            $timestamp = strtotime($sent_date);
                                                            $year = date('Y', $timestamp);
                                                            // Check for reasonable year range
                                                            if ($year >= 2020 && $year <= date('Y') + 1) {
                                                                echo date('d-M-Y H:i', $timestamp);
                                                            } else {
                                                                echo '-';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if (($confirmation['status'] ?? '') == 'generated'): ?>
                                                            <button class="btn btn-success btn-sm mark-sent-btn" 
                                                                    data-id="<?= $confirmation['id'] ?? '' ?>"
                                                                    data-supplier="<?= htmlspecialchars($confirmation['supplier_name'] ?? 'Unknown') ?>">
                                                                <i class="ft-send"></i> Mark as Sent
                                                            </button>
                                                        <?php endif; ?>
                                                        <button class="btn btn-info btn-sm view-letter-btn" 
                                                                data-id="<?= $confirmation['supplier_id'] ?? '' ?>"
                                                                data-supplier="<?= htmlspecialchars($confirmation['supplier_name'] ?? 'Unknown') ?>"
                                                                data-amount="<?= $confirmation['total_amount'] ?? 0 ?>"
                                                                data-start="<?= $confirmation['confirmation_period_start'] ?? '' ?>"
                                                                data-end="<?= $confirmation['confirmation_period_end'] ?? '' ?>">
                                                            <i class="ft-eye"></i> View Letter
                                                        </button>
                                                        <button class="btn btn-danger btn-sm delete-confirmation-btn" 
                                                                data-id="<?= $confirmation['id'] ?? '' ?>"
                                                                data-supplier="<?= htmlspecialchars($confirmation['supplier_name'] ?? 'Unknown') ?>"
                                                                title="Delete Confirmation">
                                                            <i class="ft-trash-2"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No confirmation records found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Letter Modal -->
<div class="modal fade" id="viewLetterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ft-file-text"></i> Party Confirmation Letter
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="letter-content" style="background: white; padding: 30px; font-family: 'Times New Roman', serif;">
                    <!-- Letter content will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="printLetter()">
                    <i class="ft-printer"></i> Print Letter
                </button>
                <button type="button" class="btn btn-success" onclick="prepareEmailData()">
                    <i class="ft-mail"></i> Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div id="sendMail" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Confirmation Letter</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="sendmail_form">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" 
                           value="<?php echo $this->security->get_csrf_hash(); ?>">
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control required" name="mailtoc" id="supplier_email" 
                               placeholder="supplier@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Supplier Name</label>
                        <input type="text" class="form-control" name="customername" id="email_supplier_name" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control required" name="subject" id="email_subject" 
                               value="Confirmation of Purchase Transactions" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control required summernote" id="email_message" rows="10" required></textarea>
                    </div>
                    
                    <input type="hidden" id="action-url" value="supplier/send_confirmation_email">
                    <input type="hidden" name="supplier_id" id="email_supplier_id">
                    <input type="hidden" name="amount" id="email_amount">
                    <input type="hidden" name="period_start" id="email_period_start">
                    <input type="hidden" name="period_end" id="email_period_end">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendNow">
                    <i class="ft-send"></i> Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Optimized event handlers - use direct binding where possible
    $('#sendMail').on('ajaxStart', function() {
        $('#sendNow').prop('disabled', true).html('<i class="ft-loader icon-spin"></i> Sending...');
    });
    
    $('#sendMail').on('ajaxComplete', function() {
        $('#sendNow').prop('disabled', false).html('<i class="ft-send"></i> Send Email');
    });
    
    // Optimized notify alert handler - much more efficient
    function checkNotifyChanges() {
        const notify = document.getElementById('notify');
        if (notify && notify.style.display !== 'none') {
            const message = notify.querySelector('.message');
            if (message && message.textContent.trim()) {
                const isSuccess = notify.classList.contains('alert-success');
                const messageText = message.textContent.replace(/^(Success|Error):\s*/, '');
                
                if (isSuccess) {
                    toastr.success(messageText);
                    $('#sendmail_form')[0].reset();
                    $('#sendMail').modal('hide');
                } else {
                    toastr.error(messageText);
                }
                
                notify.style.display = 'none';
            }
        }
    }
    
    // Simple periodic check instead of aggressive MutationObserver
    setInterval(checkNotifyChanges, 500);

    // Custom email sending for party confirmations
    $('#sendMail').on('click', '#sendNow', function(e) {
        e.preventDefault();
        
        // Get HTML content from Summernote
        const htmlMessage = $('.summernote').summernote('code');
        
        // Serialize form data
        const formData = $("#sendmail_form").serialize();
        
        // Add HTML message to the data
        const fullData = formData + '&message=' + encodeURIComponent(htmlMessage);
        
        // Get action URL
        const actionUrl = $('#action-url').val();
        
        // Send email using custom function
        sendConfirmationEmail(fullData, actionUrl);
    });

    // Custom email sending function for confirmations
    function sendConfirmationEmail(data, actionUrl) {
        $("#sendMail").modal('hide');
        
        $.ajax({
            url: baseurl + actionUrl,
            type: 'POST',
            data: data + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function(response) {
                if (response.status === "Success") {
                    toastr.success(response.message);
                    $('#sendmail_form')[0].reset();
                    $('.summernote').summernote('code', '');
                } else {
                    toastr.error(response.message || 'Error sending email');
                }
            },
            error: function() {
                toastr.error('Error sending email');
            }
        });
    }

    // Load suppliers needing confirmation
    loadSuppliersNeedingConfirmation();

    // Refresh alerts button
    $('#refresh-alerts').click(function() {
        loadSuppliersNeedingConfirmation();
    });

    // Generate confirmation letter - Direct action without modal
    $(document).on('click', '.generate-confirmation-btn', function() {
        const supplierId = $(this).data('id');
        const supplierName = $(this).data('name');
        const amount = $(this).data('amount');
        const periodStart = $(this).data('start');
        const periodEnd = $(this).data('end');
        const button = $(this);

        // Confirm action
        if (!confirm(`Generate confirmation letter for ${supplierName}?\n\nAmount: ₹${numberWithCommas(amount)}\nPeriod: ${formatDate(periodStart)} to ${formatDate(periodEnd)}`)) {
            return;
        }

        // Disable button and show loading
        button.prop('disabled', true).html('<i class="ft-loader icon-spin"></i> Generating...');

        // Generate letter directly
        const formData = {
            supplier_id: supplierId,
            amount: amount,
            period_start: periodStart,
            period_end: periodEnd
        };

        // Generate letter directly using explicit AJAX settings
        const csrfData = {};
        
        // Add CSRF token if available
        const csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || '<?php echo $this->security->get_csrf_token_name(); ?>';
        const csrfHash = $('meta[name="csrf-hash"]').attr('content') || '<?php echo $this->security->get_csrf_hash(); ?>';
        if (csrfTokenName && csrfHash) {
            csrfData[csrfTokenName] = csrfHash;
        }
        
        // Combine form data with CSRF data
        const requestData = Object.assign({}, formData, csrfData);
        
        $.ajax({
            url: '<?= base_url("supplier/generate_confirmation_letter") ?>',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if (response && response.status === 'success') {
                    // Use alert fallback if toastr is not available
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`Confirmation letter generated for ${supplierName}!`);
                    } else {
                        alert(`✅ Confirmation letter generated for ${supplierName}!`);
                    }
                    
                    // Remove the supplier card from alerts immediately
                    button.closest('.col-md-6').fadeOut(500, function() {
                        $(this).remove();
                        
                        // Update alert count
                        const currentCount = parseInt($('#alert-count').text()) || 0;
                        const newCount = Math.max(0, currentCount - 1);
                        $('#alert-count').text(newCount);
                        
                        // If no more alerts, show no alerts message
                        if (newCount === 0) {
                            $('#suppliers-needing-confirmation').html(`
                                <div class="text-center">
                                    <i class="ft-check-circle font-large-2 text-success"></i>
                                    <h5 class="mt-2">No Confirmations Required</h5>
                                    <p>All suppliers are within their confirmation thresholds.</p>
                                </div>
                            `);
                        }
                    });
                    
                    // Add new row to confirmation history table
                    if (response.confirmation_id) {
                        addConfirmationToHistory(supplierName, amount, periodStart, periodEnd, response.confirmation_id, supplierId);
                    }
                    
                    // Reload alerts from server to ensure consistency (without page reload)
                    setTimeout(function() {
                        loadSuppliersNeedingConfirmation();
                    }, 1000);
                    
                } else {
                    const errorMsg = response.message || 'Error generating confirmation letter';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        alert('❌ ' + errorMsg);
                    }
                    button.prop('disabled', false).html('<i class="ft-file-plus"></i> Generate & Confirm');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                // Check if it was actually successful but had JSON parsing issues
                if (xhr.status === 200 && xhr.responseText.includes('success')) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`Confirmation letter generated for ${supplierName}!`);
                        toastr.info('Page will refresh to show updated information...');
                    } else {
                        alert(`✅ Confirmation letter generated for ${supplierName}!\n\nPage will refresh to show updated information...`);
                    }
                    
                    // If the dynamic update fails, fall back to page reload
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Network error: ' + error);
                    } else {
                        alert('❌ Network error: ' + error);
                    }
                    button.prop('disabled', false).html('<i class="ft-file-plus"></i> Generate & Confirm');
                }
            }
        });
    });

    // Mark as sent
    $(document).on('click', '.mark-sent-btn', function() {
        const confirmationId = $(this).data('id');
        const supplierName = $(this).data('supplier');
        
        if (confirm(`Mark confirmation as sent for ${supplierName}?`)) {
            markConfirmationSent(confirmationId);
        }
    });

    // Delete confirmation
    $(document).on('click', '.delete-confirmation-btn', function() {
        const confirmationId = $(this).data('id');
        const supplierName = $(this).data('supplier');
        
        if (confirm(`Are you sure you want to delete the confirmation for ${supplierName}?\n\nThis action cannot be undone.`)) {
            deleteConfirmation(confirmationId);
        }
    });

    // View letter
    $(document).on('click', '.view-letter-btn', function() {
        const supplierId = $(this).data('id');
        const supplierName = $(this).data('supplier');
        const amount = $(this).data('amount');
        const periodStart = $(this).data('start');
        const periodEnd = $(this).data('end');
        
        // Fetch supplier details including address
        $.get('<?= base_url("supplier/get_supplier_details") ?>/' + supplierId)
            .done(function(supplierData) {
                // Add supplier ID to supplierData
                supplierData.id = supplierId;
                showConfirmationLetter(supplierName, amount, periodStart, periodEnd, supplierData);
            })
            .fail(function() {
                // Fallback if supplier details fetch fails
                const fallbackData = { id: supplierId };
                showConfirmationLetter(supplierName, amount, periodStart, periodEnd, fallbackData);
            });
    });
});

function loadSuppliersNeedingConfirmation() {
    $('#suppliers-needing-confirmation').html(`
        <div class="text-center">
            <i class="ft-loader icon-spin font-large-1"></i>
            <p>Loading suppliers requiring confirmation...</p>
        </div>
    `);

    $.get('<?= base_url("supplier/party_confirmation_alerts") ?>')
        .done(function(data) {
            displaySuppliersNeedingConfirmation(data);
        })
        .fail(function() {
            $('#suppliers-needing-confirmation').html(`
                <div class="alert alert-danger">
                    <i class="ft-alert-circle"></i> Error loading suppliers. Please try again.
                </div>
            `);
        });
}

function displaySuppliersNeedingConfirmation(suppliers) {
    const container = $('#suppliers-needing-confirmation');
    const alertCount = $('#alert-count');

    if (!suppliers || suppliers.length === 0) {
        container.html(`
            <div class="text-center">
                <i class="ft-check-circle font-large-2 text-success"></i>
                <h5 class="mt-2">No Confirmations Required</h5>
                <p>All suppliers are within their confirmation thresholds.</p>
            </div>
        `);
        alertCount.text('0');
        return;
    }

    alertCount.text(suppliers.length);

    let html = '<div class="row">';
    
    suppliers.forEach(function(supplier) {
        const amount = parseFloat(supplier.total_purchases);
        const threshold = parseFloat(supplier.confirmation_threshold);
        
        html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <i class="ft-user"></i> ${supplier.name || supplier.company}
                        </h5>
                        <div class="mb-2">
                            <small class="text-muted">Purchase Amount</small><br>
                            <span class="badge badge-danger font-medium-1">₹${numberWithCommas(amount)}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Threshold</small><br>
                            <span class="badge badge-info">₹${numberWithCommas(threshold)}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Period</small><br>
                            <small>${formatDate(supplier.period_start)} to ${formatDate(supplier.period_end)}</small>
                        </div>
                        <button class="btn btn-success btn-sm generate-confirmation-btn" 
                                data-id="${supplier.id}"
                                data-name="${supplier.name || supplier.company}"
                                data-amount="${amount}"
                                data-start="${supplier.period_start}"
                                data-end="${supplier.period_end}">
                            <i class="ft-file-plus"></i> Generate & Confirm
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.html(html);
}

function addConfirmationToHistory(supplierName, amount, periodStart, periodEnd, confirmationId, supplierId) {
    const table = $('#confirmations-table tbody');
    const now = new Date();
    const generatedDate = now.toLocaleDateString('en-GB') + ' ' + now.toTimeString().substr(0,5);
    const formattedAmount = numberWithCommas(amount);
    const formattedPeriodStart = formatDate(periodStart);
    const formattedPeriodEnd = formatDate(periodEnd);
    
    // Remove "No confirmation records found" row if it exists
    table.find('td[colspan="7"]').closest('tr').remove();
    
    // Create new row
    const newRow = `
        <tr class="table-success" style="animation: fadeIn 0.5s;">
            <td>
                <strong>${supplierName}</strong>
            </td>
            <td>
                <span class="badge badge-primary">₹${formattedAmount}</span>
            </td>
            <td>
                ${formattedPeriodStart} to ${formattedPeriodEnd}
            </td>
            <td>
                <span class="badge badge-warning">Generated</span>
            </td>
            <td>
                ${generatedDate}
            </td>
            <td>
                -
            </td>
            <td>
                <button class="btn btn-success btn-sm mark-sent-btn" 
                        data-id="${confirmationId}"
                        data-supplier="${supplierName}">
                    <i class="ft-send"></i> Mark as Sent
                </button>
                <button class="btn btn-info btn-sm view-letter-btn" 
                        data-id="${supplierId}"
                        data-supplier="${supplierName}"
                        data-amount="${amount}"
                        data-start="${periodStart}"
                        data-end="${periodEnd}">
                    <i class="ft-eye"></i> View Letter
                </button>
                <button class="btn btn-danger btn-sm delete-confirmation-btn" 
                        data-id="${confirmationId}"
                        data-supplier="${supplierName}"
                        title="Delete Confirmation">
                    <i class="ft-trash-2"></i> Delete
                </button>
            </td>
        </tr>
    `;
    
    // Add to top of table with highlight effect
    table.prepend(newRow);
    
    // Remove highlight after 3 seconds
    setTimeout(function() {
        table.find('tr:first-child').removeClass('table-success');
    }, 3000);
}

function markConfirmationSent(confirmationId) {
    $.post('<?= base_url("supplier/mark_confirmation_sent") ?>', { confirmation_id: confirmationId })
        .done(function(response) {
            if (response.status === 'success') {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Confirmation marked as sent!');
                } else {
                    alert('✅ Confirmation marked as sent!');
                }
                
                // Update the table row instead of reloading
                const button = $(`.mark-sent-btn[data-id="${confirmationId}"]`);
                const row = button.closest('tr');
                
                // Update status badge
                row.find('td:nth-child(4)').html('<span class="badge badge-success">Sent</span>');
                
                // Update sent date
                const now = new Date();
                const sentDateFormatted = now.toLocaleDateString('en-GB') + ' ' + now.toTimeString().substr(0,5);
                row.find('td:nth-child(6)').text(sentDateFormatted);
                
                // Remove the "Mark as Sent" button
                button.remove();
                
            } else {
                const errorMsg = response.message || 'Error updating confirmation status';
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert('❌ ' + errorMsg);
                }
            }
        })
        .fail(function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Error updating confirmation status');
            } else {
                alert('❌ Error updating confirmation status');
            }
        });
}

function deleteConfirmation(confirmationId) {
    // Get CSRF token from the email form (which already has proper CSRF handling)
    const csrfTokenName = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').attr('name');
    const csrfTokenValue = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
    
    const postData = { 
        confirmation_id: confirmationId
    };
    
    // Add CSRF token if available
    if (csrfTokenName && csrfTokenValue) {
        postData[csrfTokenName] = csrfTokenValue;
    }
    
    $.post('<?= base_url("supplier/delete_confirmation") ?>', postData)
        .done(function(response) {
            if (response.status === 'success') {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Confirmation deleted successfully!');
                } else {
                    alert('✅ Confirmation deleted successfully!');
                }
                
                // Remove the table row
                const button = $(`.delete-confirmation-btn[data-id="${confirmationId}"]`);
                const row = button.closest('tr');
                row.fadeOut(500, function() {
                    $(this).remove();
                    
                    // Check if table is empty after deletion
                    const tbody = $('#confirmations-table tbody');
                    if (tbody.find('tr').length === 0) {
                        tbody.html('<tr><td colspan="7" class="text-center">No confirmation records found</td></tr>');
                    }
                });
                
            } else {
                const errorMsg = response.message || 'Error deleting confirmation';
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert('❌ ' + errorMsg);
                }
            }
        })
        .fail(function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Error deleting confirmation');
            } else {
                alert('❌ Error deleting confirmation');
            }
        });
}

function showConfirmationLetter(supplierName, amount, periodStart, periodEnd, supplierData = null) {
    const letterContent = generateLetterHTML(supplierName, amount, periodStart, periodEnd, supplierData);
    $('#letter-content').html(letterContent);
    
    // Store data for email function
    $('#viewLetterModal').data({
        'supplierId': supplierData ? supplierData.id : null,
        'supplierName': supplierName,
        'amount': amount,
        'periodStart': periodStart,
        'periodEnd': periodEnd,
        'supplierData': supplierData
    });
    
    $('#viewLetterModal').modal('show');
}

// Prepare email data when Send Email button is clicked
function prepareEmailData() {
    // Close the view letter modal first
    $('#viewLetterModal').modal('hide');
    
    // Get current letter data from the view modal
    const modalData = $('#viewLetterModal').data();
    const supplierId = modalData.supplierId;
    const supplierName = modalData.supplierName;
    const amount = modalData.amount;
    const periodStart = modalData.periodStart;
    const periodEnd = modalData.periodEnd;
    const supplierData = modalData.supplierData;
    
    // Populate email form
    $('#email_supplier_id').val(supplierId);
    $('#email_supplier_name').val(supplierName);
    $('#email_amount').val(amount);
    $('#email_period_start').val(periodStart);
    $('#email_period_end').val(periodEnd);
    
    // Set supplier email if available
    if (supplierData && supplierData.email) {
        $('#supplier_email').val(supplierData.email);
    } else {
        $('#supplier_email').val('');
    }
    
    // Simple email message with HTML formatting
    const emailHtml = `
        <p><strong>Dear ${supplierName},</strong></p>
        
        <p>As per our records, we have made purchases amounting to <strong>NRS 1,00,000 or more</strong> from your company during the current fiscal period.</p>
        
        <p>Kindly verify the transaction summary below and confirm from your end:</p>
        
        <ul>
            <li><strong>Party Name:</strong> ${supplierName}</li>
            <li><strong>Total Purchase Value:</strong> NRS ${numberWithCommas(amount)}</li>
            <li><strong>Period:</strong> ${formatDate(periodStart)} to ${formatDate(periodEnd)}</li>
            <li><strong>Invoice Numbers:</strong> [List if needed]</li>
        </ul>
        
        <p>Please confirm the accuracy or report discrepancies within <strong>7 days</strong>.</p>
        
        <p>Thank you for your cooperation.</p>
        
        <br>
        <p><strong>Best regards,</strong><br>
        For ${companyInfo.name}</p>
        
        <br><br>
        <p>______________________<br>
        <strong>Authorized Signatory</strong><br>
        (Sign & Stamp)</p>
    `;
    
    // Set the HTML content using Summernote
    $('#email_message').summernote('code', emailHtml);
    
    // Show the email modal after a short delay to ensure view modal is closed
    setTimeout(function() {
        $('#sendMail').modal('show');
    }, 300);
}

// Company information passed from PHP
<?php $company = location(0); ?>
const companyInfo = {
    name: '<?= addslashes(htmlspecialchars($company['cname'] ?? 'Your Company Name')) ?>',
    address: '<?= addslashes(htmlspecialchars($company['address'] ?? '[Company Address]')) ?>',
    city: '<?= addslashes(htmlspecialchars($company['city'] ?? '[City]')) ?>',
    region: '<?= addslashes(htmlspecialchars($company['region'] ?? '')) ?>',
    country: '<?= addslashes(htmlspecialchars($company['country'] ?? '[Country]')) ?>',
    postbox: '<?= addslashes(htmlspecialchars($company['postbox'] ?? '')) ?>',
    phone: '<?= addslashes(htmlspecialchars($company['phone'] ?? '[Phone]')) ?>',
    email: '<?= addslashes(htmlspecialchars($company['email'] ?? '[Email]')) ?>',
    taxid: '<?= addslashes(htmlspecialchars($company['taxid'] ?? '')) ?>',
    logo: '<?= !empty($company['logo']) ? base_url('userfiles/company/' . $company['logo']) : '' ?>'
};

function generateLetterHTML(supplierName, amount, periodStart, periodEnd, supplierData = null) {
    const today = new Date().toLocaleDateString('en-GB');
    
    const logoSection = companyInfo.logo ? 
        `<img src="${companyInfo.logo}" alt="Company Logo" style="max-height: 80px; margin-bottom: 10px;">` :
        `<div style="border: 1px solid #ccc; padding: 10px; display: inline-block; margin-bottom: 10px; background: #f5f5f5;">
            <strong>[COMPANY LOGO]</strong>
        </div>`;
    
    const addressLine2 = companyInfo.region ? `${companyInfo.city}, ${companyInfo.region}` : companyInfo.city;
    const addressLine3 = companyInfo.postbox ? `${companyInfo.country} - ${companyInfo.postbox}` : companyInfo.country;
    const taxInfo = companyInfo.taxid ? ` | Tax ID: ${companyInfo.taxid}` : '';
    
    // Build supplier address
    let supplierAddress = '[Party Address]';
    if (supplierData) {
        let addressLines = [];
        if (supplierData.address) addressLines.push(supplierData.address);
        
        // City and region on same line
        let cityLine = '';
        if (supplierData.city && supplierData.region) {
            cityLine = supplierData.city + ', ' + supplierData.region;
        } else if (supplierData.city) {
            cityLine = supplierData.city;
        } else if (supplierData.region) {
            cityLine = supplierData.region;
        }
        if (cityLine) addressLines.push(cityLine);
        
        // Country and postbox on same line
        let countryLine = '';
        if (supplierData.country && supplierData.postbox) {
            countryLine = supplierData.country + ' - ' + supplierData.postbox;
        } else if (supplierData.country) {
            countryLine = supplierData.country;
        } else if (supplierData.postbox) {
            countryLine = supplierData.postbox;
        }
        if (countryLine) addressLines.push(countryLine);
        
        // Add contact information if available
        if (supplierData.phone) addressLines.push('Phone: ' + supplierData.phone);
        if (supplierData.email) addressLines.push('Email: ' + supplierData.email);
        
        if (addressLines.length > 0) {
            supplierAddress = addressLines.join('<br>');
        }
    }
    
    return `
        <div style="text-align: center; margin-bottom: 30px;">
            ${logoSection}
            <h2>${companyInfo.name}</h2>
            <p>${companyInfo.address}</p>
            <p>${addressLine2}</p>
            <p>${addressLine3}</p>
            <p>Phone: ${companyInfo.phone} | Email: ${companyInfo.email}${taxInfo}</p>
        </div>
        
        <div style="text-align: right; margin-bottom: 20px;">
            <strong>Date: ${today}</strong>
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>To,</strong></p>
            <p><strong>${supplierName}</strong></p>
            <p>${supplierAddress}</p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Subject: Confirmation of Purchase Transactions</strong></p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <p>Dear <strong>${supplierName}</strong>,</p>
            
            <p>As per our records, we have made purchases amounting to <strong>NRS 1,00,000 or more</strong> from your company during the current fiscal period.</p>
            
            <p>Kindly verify the transaction summary below and confirm from your end:</p>
            
            <ul>
                <li><strong>Party Name:</strong> ${supplierName}</li>
                <li><strong>Total Purchase Value:</strong> NRS ${numberWithCommas(amount)}</li>
                <li><strong>Period:</strong> ${formatDate(periodStart)} to ${formatDate(periodEnd)}</li>
                <li><strong>Invoice Numbers:</strong> [List if needed]</li>
            </ul>
            
            <p>Please confirm the accuracy or report discrepancies within <strong>7 days</strong>.</p>
            
            <p>Thank you for your cooperation.</p>
        </div>
        
        <div style="margin-top: 40px;">
            <p><strong>Best regards,</strong></p>
            <p><strong>For ${companyInfo.name}</strong></p>
            <br><br>
            <p>______________________</p>
            <p><strong>Authorized Signatory</strong></p>
            <p>(Sign & Stamp)</p>
        </div>
        
        <div style="margin-top: 60px; border-top: 2px solid #000; padding-top: 20px;">
            <p><strong>Confirmed by:</strong></p>
            <p><strong>For ${supplierName}</strong></p>
            <br><br>
            <p>______________________</p>
            <p><strong>Authorized Signatory</strong></p>
            <p>(Sign & Stamp)</p>
        </div>
    `;
}

function printLetter() {
    const letterContent = document.getElementById('letter-content').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Party Confirmation Letter</title>
            <style>
                body { font-family: 'Times New Roman', serif; margin: 20px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            ${letterContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        const date = new Date(dateString);
        // Check if date is valid
        if (isNaN(date.getTime())) {
            return dateString; // Return original if can't parse
        }
        
        // Format as DD/MM/YYYY (British format)
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}/${month}/${year}`;
    } catch (e) {
        return dateString; // Return original on error
    }
}

// Initialize Summernote for HTML email editing
$(document).ready(function() {
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['fullscreen', ['fullscreen']],
            ['codeview', ['codeview']]
        ]
    });
});
</script>

<style>
.card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.8em;
}

.alert-count {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

#letter-content {
    line-height: 1.6;
}

#letter-content p {
    margin-bottom: 10px;
}

#letter-content ul {
    margin: 15px 0;
    padding-left: 20px;
}

#letter-content li {
    margin-bottom: 5px;
}

.icon-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes fadeIn {
    from { opacity: 0; background-color: #d4edda; }
    to { opacity: 1; background-color: #d4edda; }
}

.table-success {
    background-color: #d4edda !important;
    transition: background-color 3s ease;
}
</style>
