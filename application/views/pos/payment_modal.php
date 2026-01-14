<div class="modal fade" id="basicPay" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content ">
			<form method="post" id="basicpay_data" class="form-horizontal">
				<!-- Modal Header -->
				<div class="modal-header">

					<h4 class="modal-title"><?php echo $this->lang->line('Make Payment') ?></h4>
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
					</button>
				</div>

				<!-- Modal Body -->
				<div class="modal-body">
					<p id="statusMsg"></p>

					<div class="text-center"><h1 id="b_total"></h1></div>
					<div class="row">


						<div class="col-6">
							<div class="card-title">
								<label for="cardNumber"><?php echo $this->lang->line('Amount') ?></label>
								
								<div class="input-group">
									<input
										type="text"
										class="form-control  text-bold-600 blue-grey"
										name="p_amount"
										placeholder="Amount" onkeypress="return isNumber(event)"
										id="p_amount" onkeyup="update_pay_pos()" inputmode="numeric"
									/>
									<span class="input-group-addon"><i
											class="icon icon-cash"></i></span>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="card-title">
								<label for="cardNumber"><?php echo $this->lang->line('Payment Method') ?></label>
								<select class="form-control" name="p_method" id="p_method">
									<?php if (!empty($payment_methods)): ?>
										<?php foreach ($payment_methods as $method): ?>
											<?php if ($method['account_name'] != 'Not Linked'): ?>
												<option value="<?php echo htmlspecialchars($method['name']); ?>" 
														data-balance="<?php echo $method['balance'] ?? 0; ?>" 
														data-account-id="<?php echo $method['account_id']; ?>"
														data-account-name="<?php echo htmlspecialchars($method['account_name']); ?>">
													<?php echo htmlspecialchars($method['name']); ?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php else: ?>
										<option value="">No payment methods available</option>
									<?php endif; ?>
								</select>
								<small class="text-muted mt-1" id="payment-method-info">Select a payment method to see account balance</small>
							</div>
						</div>


					</div>

					<div class="row">
						<div class="col-6">
							<div class="form-group  text-bold-600 red">
								<label for="amount"><?php echo $this->lang->line('Balance Due') ?>
								</label>
								<input type="text" class="form-control red" name="amount" id="balance1"
									   onkeypress="return isNumber(event)"
									   value="0.00"
									   required>
							</div>
						</div>
						<div class="col-6">
							<div class="form-group text-bold-600 text-g">
								<label for="b_change"><?php echo $this->lang->line('Change') ?></label>
								<input
									type="text" onkeypress="return isNumber(event)"
									class="form-control green"
									name="b_change" id="change_p" value="0">
							</div>
						</div>
					</div>
					<?php if (PAC) { ?>
						<div class="col">
							<div class="form-group text-bold-600 text-g">
								<label for="account_p"><?php echo $this->lang->line('Account') ?></label>
								<div class="form-control-static" id="linked_account_display">
									<span id="linked_account_info" class="text-info">Select a payment method to see linked account</span>
								</div>
								<input type="hidden" name="p_account" id="p_account" value="">
							</div>
						</div>
					<?php } ?>

					<div class="row">
						<div class="col-12">
							<button class="btn btn-success btn-lg btn-block mb-1"
									type="submit"
									id="pos_basic_pay" data-type="4"><i
									class="fa fa-arrow-circle-o-right"></i> <?php echo $this->lang->line('Paynow') ?>
							</button>
							<button class="btn btn-info btn-lg btn-block"
									type="submit"
									id="pos_basic_print" data-type="4"><i
									class="fa fa-print"></i> <?php echo $this->lang->line('Paynow') ?>
								+ <?php echo $this->lang->line('Print') ?></button>
						</div>
					</div>

					<div class="row" style="display:none;">
						<div class="col-xs-12">
							<p class="payment-errors"></p>
						</div>
					</div>


					<!-- shipping -->


				</div>
				<!-- Modal Footer -->

			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
    // Update account balance when payment method changes in modal
    $('#basicPay #p_method').change(function() {
        var selectedOption = $(this).find('option:selected');
        var balance = selectedOption.data('balance') || 0;
        var accountName = selectedOption.data('account-name') || '';
        var accountId = selectedOption.data('account-id') || '';
        
        if (accountName) {
            $('#basicPay #payment-method-info').text('Account: ' + accountName);
            $('#basicPay #payment-method-info').removeClass('text-muted').addClass('text-info');
            
            // Update linked account display
            $('#linked_account_info').text(accountName);
            $('#p_account').val(accountId);
        } else {
            $('#basicPay #payment-method-info').text('Select a payment method to see linked account');
            $('#basicPay #payment-method-info').removeClass('text-info').addClass('text-muted');
            
            // Clear linked account
            $('#linked_account_info').text('Select a payment method to see linked account');
            $('#p_account').val('');
        }
        
        console.log('Payment method changed in modal - Account:', accountName, 'Account ID:', accountId);
    });
    
    // Initialize payment method info on modal show
    $('#basicPay').on('shown.bs.modal', function() {
        $('#basicPay #p_method').trigger('change');
    });
});
</script>
