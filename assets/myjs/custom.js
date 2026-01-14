var billtype = $("#billtype").val();
var d_csrf = crsf_token + "=" + crsf_hash;

// Calculate row total for invoice/purchase entry pages
window.calculateRowTotal = function (rowNum) {
	var amountVal = parseFloat($("#amount-" + rowNum).val()) || 0;
	var priceVal = parseFloat($("#price-" + rowNum).val()) || 0;
	var vatVal = parseFloat($("#vat-" + rowNum).val()) || 0;
	var discountVal = parseFloat($("#discount-" + rowNum).val()) || 0;

	var subtotal = amountVal * priceVal;
	var taxAmount = (subtotal * vatVal) / 100;
	var discountAmount = (subtotal * discountVal) / 100;
	var total = subtotal + taxAmount - discountAmount;

	// Update fields
	$("#texttaxa-" + rowNum).text(taxAmount.toFixed(2));
	$("#taxa-" + rowNum).val(taxAmount.toFixed(2));
	$("#disca-" + rowNum).val(discountAmount.toFixed(2));
	$("#total-" + rowNum).val(total.toFixed(2));
	$("#result-" + rowNum).text(total.toFixed(2));

	// Update totals
	samanYog();
};

// Recalculate all existing rows on page load
$(document).ready(function () {
	if (billtype === "search") {
		$(".amnt").each(function () {
			var rowId = $(this).attr("id").split("-")[1];
			if (rowId && $("#productname-" + rowId).length > 0) {
				calculateRowTotal(rowId);
			}
		});
	}
});

$("#addproduct").on("click", function () {
	var cvalue = parseInt($("#ganak").val()) + 1;
	var nxt = parseInt(cvalue);
	$("#ganak").val(nxt);
	var functionNum = "'" + cvalue + "'";
	count = $("#saman-row div").length;

	// Check if this is Purchase Entry or Purchase Order or Sales/Quotes/Subscription
	var pageType = $("#page-type").val();
	var data = "";

	if (pageType === "stock_return" || billtype === "search") {
		// Purchase Entry - Simple row template (like the original commented code)
		data =
			"<tr>" +
			'<td><input type="text" class="form-control text-center" name="product_name[]" placeholder="Enter Product name" id="productname-' +
			cvalue +
			'"></td>' +
			'<td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="calculateRowTotal(' +
			cvalue +
			')" autocomplete="off" value="1"><input type="hidden" id="alert-' +
			cvalue +
			'" value="" name="alert[]"></td>' +
			'<td><input type="text" class="form-control req prc" name="product_price[]" id="price-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="calculateRowTotal(' +
			cvalue +
			')" autocomplete="off"></td>' +
			'<td><input type="text" class="form-control vat" name="product_tax[]" id="vat-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="calculateRowTotal(' +
			cvalue +
			')" autocomplete="off"></td>' +
			'<td id="texttaxa-' +
			cvalue +
			'" class="text-center">0</td>' +
			'<td><input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-' +
			cvalue +
			'" onkeyup="calculateRowTotal(' +
			cvalue +
			')" autocomplete="off"></td>' +
			'<td><span class="currenty">' +
			currency +
			'</span> <strong><span class="ttlText" id="result-' +
			cvalue +
			'">0</span></strong></td>' +
			'<td class="text-center"><button type="button" data-rowid="' +
			cvalue +
			'" class="btn btn-danger removeProd" title="Remove"><i class="fa fa-minus-square"></i></button></td>' +
			'<input type="hidden" name="taxa[]" id="taxa-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" name="disca[]" id="disca-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" class="pdIn" name="pid[]" id="pid-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" name="unit[]" id="unit-' +
			cvalue +
			'" value="">' +
			'<input type="hidden" name="hsn[]" id="hsn-' +
			cvalue +
			'" value="">' +
			'<input type="hidden" name="serial[]" id="serial-' +
			cvalue +
			'" value="">' +
			"</tr>" +
			'<tr><td colspan="8"><textarea class="form-control" id="dpid-' +
			cvalue +
			'" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><br></td></tr>';
	} else {
		// Purchase Order - Row template with batch fields (current template)
		data =
			"<tr>" +
			'<td><input type="text" class="form-control text-center" name="product_name[]" placeholder="Enter Product name" id="productname-' +
			cvalue +
			'"></td>' +
			'<td><input type="text" class="form-control batch_no_input" name="batch_no[]" id="batch_no-' +
			cvalue +
			'" placeholder="Batch No"></td>' +
			'<td><input type="date" class="form-control expiry_date" name="expiry_date[]" id="expiry_date-' +
			cvalue +
			'"></td>' +
			'<td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off" value="1"></td>' +
			'<td><input type="text" class="form-control req prc" name="product_price[]" id="price-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			"), billUpyog(), calculateProfit(" +
			cvalue +
			')" autocomplete="off"></td>' +
			'<td><input type="text" class="form-control selling_price" name="selling_price[]" id="selling_price-' +
			cvalue +
			'" placeholder="Selling Price" style="min-width: 120px;" onkeyup="calculateProfit(' +
			cvalue +
			')"></td>' +
			'<td><input type="text" class="form-control profit" name="profit[]" id="profit-' +
			cvalue +
			'" placeholder="Profit" readonly style="background:#f8f9fa; color:#333; min-width: 120px; font-weight: bold;"></td>' +
			'<td><input type="text" class="form-control profit_margin" name="profit_margin[]" id="profit_margin-' +
			cvalue +
			'" placeholder="Profit %" readonly style="background:#f8f9fa; color:#333; min-width: 120px; font-weight: bold;"></td>' +
			'<td><input type="text" class="form-control vat" name="product_tax[]" id="vat-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off" style="min-width: 100px;"></td>' +
			'<td class="text-center" id="texttaxa-' +
			cvalue +
			'">0</td>' +
			'<td><input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-' +
			cvalue +
			'" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off" style="min-width: 100px;"></td>' +
			'<td><span class="currenty">' +
			currency +
			'</span> <strong><span class="ttlText" id="result-' +
			cvalue +
			'">0</span></strong></td>' +
			'<td class="text-center"></td>' +
			'<input type="hidden" name="taxa[]" id="taxa-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" name="disca[]" id="disca-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" class="pdIn" name="pid[]" id="pid-' +
			cvalue +
			'" value="0">' +
			'<input type="hidden" name="unit[]" id="unit-' +
			cvalue +
			'" value="">' +
			'<input type="hidden" name="hsn[]" id="hsn-' +
			cvalue +
			'" value="">' +
			'<input type="hidden" name="product_description[]" id="product_description-' +
			cvalue +
			'" value="">' +
			"</tr>";
	}

	//ajax request
	// $('#saman-row').append(data);
	$("tr.last-item-row").before(data);

	// Protect expiry date fields from getting invalid values
	setTimeout(function () {
		var expiryField = $("#expiry_date-" + cvalue);
		if (expiryField.length > 0) {
			// Clear any invalid values that might have been set
			if (expiryField.val() === "english" || expiryField.val() === "nepali") {
				expiryField.val("");
			}

			// Apply comprehensive protection to the new field
			expiryField.attr("type", "date");
			expiryField.removeClass("nepali-calendar english-calendar");
			expiryField.removeData("nepali-calendar");

			// Add event handlers for protection
			expiryField.on("change keyup blur", function () {
				var currentValue = $(this).val();
				var protectedValue = $(this).attr("data-protected-value");

				if (
					(currentValue === "english" || currentValue === "nepali") &&
					protectedValue
				) {
					console.warn(
						"Restoring protected expiry value for new row",
						cvalue,
						"from:",
						currentValue,
						"to:",
						protectedValue
					);
					$(this).val(protectedValue);
				}
			});

			console.log(
				"Applied protection to new expiry field:",
				"expiry_date-" + cvalue
			);
		}
	}, 100);

	row = cvalue;

	$("#productname-" + cvalue).autocomplete({
		source: function (request, response) {
			$.ajax({
				url: baseurl + "search_products/" + billtype,
				dataType: "json",
				method: "post",
				data:
					"name_startsWith=" +
					request.term +
					"&type=product_list&row_num=" +
					row +
					"&wid=" +
					$("#s_warehouses option:selected").val() +
					"&" +
					d_csrf,
				success: function (data) {
					response(
						$.map(data, function (item) {
							var product_d = item[0];
							return {
								label: product_d,
								value: product_d,
								data: item,
							};
						})
					);
				},
			});
		},
		autoFocus: true,
		minLength: 0,
		select: function (event, ui) {
			id_arr = $(this).attr("id");
			id = id_arr.split("-");
			var t_r = ui.item.data[3];
			var discount = ui.item.data[4];
			var custom_discount = $("#custom_discount").val();
			if (custom_discount > 0) discount = deciFormat(custom_discount);
			if (t_r == 0 && discount == 5) t_r = 13;

			$("#amount-" + id[1]).val(1);
			$("#price-" + id[1]).val(ui.item.data[1]);
			$("#pid-" + id[1]).val(ui.item.data[2]);
			$("#vat-" + id[1]).val(t_r);
			$("#discount-" + id[1]).val(discount);
			$("#dpid-" + id[1]).val(ui.item.data[5]);
			$("#unit-" + id[1]).val(ui.item.data[6]);
			$("#hsn-" + id[1]).val(ui.item.data[7]);
			$("#alert-" + id[1]).val(ui.item.data[8]);
			$("#serial-" + id[1]).val(ui.item.data[10]);
			// --- Batch logic for autocomplete ---
			// If batchid is present in ui.item.data, add hidden input
			if (typeof ui.item.data[11] !== "undefined" && ui.item.data[11]) {
				// Remove any existing batchid input for this row
				$(this)
					.closest("tr")
					.next("tr")
					.find('input[name="batchid[]"]')
					.remove();
				// Add hidden input for batchid
				var batchidInput =
					'<input type="hidden" name="batchid[]" value="' +
					ui.item.data[11] +
					'">';
				$(this).closest("tr").next("tr").append(batchidInput);
			} else {
				// Remove any batchid input if not a batch product
				$(this)
					.closest("tr")
					.next("tr")
					.find('input[name="batchid[]"]')
					.remove();
			}
			// --- End batch logic ---
			if (window.calculateRowTotal) {
				window.calculateRowTotal(cvalue);
			} else {
				rowTotal(cvalue);
			}
			billUpyog();
			calculateProfit(cvalue);
		},
		create: function (e) {
			$(this).prev(".ui-helper-hidden-accessible").remove();
		},
	});

	// Initialize batch logic for the new row and auto-generate batch number
	setTimeout(function () {
		// Auto-generate batch number for the new row using incremental logic
		if (typeof getNextIncrementedBatchNo === "function") {
			var incrementedBatch = getNextIncrementedBatchNo();
			if (incrementedBatch) {
				$("#batch_no-" + cvalue).val(incrementedBatch);
			} else if (typeof baseurl !== "undefined") {
				$.getJSON(baseurl + "purchase/get_next_batch_no", function (data) {
					if (data.batch_no) {
						$("#batch_no-" + cvalue).val(data.batch_no);
					}
				});
			}
		} else if (typeof baseurl !== "undefined") {
			$.getJSON(baseurl + "purchase/get_next_batch_no", function (data) {
				if (data.batch_no) {
					$("#batch_no-" + cvalue).val(data.batch_no);
				}
			});
		}

		// Initialize batch logic if the functions exist (from newinvoice.php script)
		if (typeof initBatchLogicForRow === "function") {
			initBatchLogicForRow(cvalue);
		}
	}, 100);
});

//caculations
var precentCalc = function (total, percentageVal) {
	var pr = (total / 100) * percentageVal;
	return parseFloat(pr);
};
//format
var deciFormat = function (minput) {
	if (!minput) mininput = 0;
	return parseFloat(minput).toFixed(2);
};
var formInputGet = function (iname, inumber) {
	var inputId;
	inputId = iname + "-" + inumber;
	var inputValue = $(inputId).val();

	if (inputValue == "") {
		return 0;
	} else {
		return inputValue;
	}
};

//ship calculation
var coupon = function () {
	var cp = 0;
	if ($("#coupon_amount").val()) {
		cp = accounting.unformat(
			$("#coupon_amount").val(),
			accounting.settings.number.decimal
		);
	}
	return cp;
};
var shipTot = function () {
	var ship_val = accounting.unformat(
		$(".shipVal").val(),
		accounting.settings.number.decimal
	);
	var ship_p = 0;
	if ($("#taxformat option:selected").attr("data-trate")) {
		var ship_rate = $("#taxformat option:selected").attr("data-trate");
	} else {
		var ship_rate = accounting.unformat(
			$("#ship_rate").val(),
			accounting.settings.number.decimal
		);
	}
	var tax_status = $("#ship_taxtype").val();
	if (tax_status == "excl") {
		ship_p = (ship_val * ship_rate) / 100;
		ship_val = ship_val + ship_p;
	} else if (tax_status == "incl") {
		ship_p = (ship_val * ship_rate) / (100 + ship_rate);
	}
	$("#ship_tax").val(accounting.formatNumber(ship_p));
	$("#ship_final").html(accounting.formatNumber(ship_p));
	return ship_val;
};

//product total
var samanYog = function () {
	var itempriceList = [];
	var idList = [];
	var r = 0;
	$(".ttInput").each(function () {
		var vv = accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
		var vid = $(this).attr("id");
		vid = vid.split("-");
		itempriceList.push(vv);
		idList.push(vid[1]);
		r++;
	});
	var sum = 0;
	var taxc = 0;
	var discs = 0;
	for (var z = 0; z < idList.length; z++) {
		var x = idList[z];
		if (itempriceList[z] > 0) {
			sum += itempriceList[z];
		}
		var t1 = accounting.unformat(
			$("#taxa-" + x).val(),
			accounting.settings.number.decimal
		);
		var d1 = accounting.unformat(
			$("#disca-" + x).val(),
			accounting.settings.number.decimal
		);
		if (t1 > 0) {
			taxc += t1;
		}
		if (d1 > 0) {
			discs += d1;
		}
	}

	$("#discs").html(accounting.formatNumber(discs));
	$("#taxr").html(accounting.formatNumber(taxc));
	return accounting.unformat(sum, accounting.settings.number.decimal);
};

//actions
var deleteRow = function (num) {
	var totalSelector = $("#subttlform");
	var prodttl = accounting.unformat(
		$("#total-" + num).val(),
		accounting.settings.number.decimal
	);
	var subttl = accounting.unformat(
		totalSelector.val(),
		accounting.settings.number.decimal
	);
	var totalSubVal = subttl - prodttl;
	totalSelector.val(totalSubVal);
	$("#subttlid").html(accounting.formatNumber(totalSubVal));
	var totalBillVal = totalSubVal + shipTot - coupon;
	//final total
	var clean = accounting.formatNumber(totalBillVal);
	$("#mahayog").html(clean);
	$("#invoiceyoghtml").val(clean);
	$("#bigtotal").html(clean);
};

var billUpyog = function () {
	var out = 0;
	var disc_val = accounting.unformat(
		$(".discVal").val(),
		accounting.settings.number.decimal
	);
	if (disc_val) {
		$("#subttlform").val(accounting.formatNumber(samanYog()));
		var disc_rate = $("#discountFormat").val();

		switch (disc_rate) {
			case "%":
				out = precentCalc(
					accounting.unformat(
						$("#subttlform").val(),
						accounting.settings.number.decimal
					),
					disc_val
				);
				break;
			case "b_p":
				out = precentCalc(
					accounting.unformat(
						$("#subttlform").val(),
						accounting.settings.number.decimal
					),
					disc_val
				);
				break;
			case "flat":
				out = accounting.unformat(disc_val, accounting.settings.number.decimal);
				break;
			case "bflat":
				out = accounting.unformat(disc_val, accounting.settings.number.decimal);
				break;
		}
		out = parseFloat(out).toFixed(two_fixed);

		$("#disc_final").html(accounting.formatNumber(out));
		$("#after_disc").val(accounting.formatNumber(out));
	} else {
		$("#disc_final").html(0);
		$("#after_disc").val(0);
	}
	var totalBillVal = accounting.formatNumber(
		samanYog() + shipTot() - coupon() - out
	);
	$("#mahayog").html(totalBillVal);
	$("#subttlform").val(accounting.formatNumber(samanYog()));
	$("#invoiceyoghtml").val(totalBillVal);
	$("#bigtotal").html(totalBillVal);
	var itotal = 0;
	$(".pdIn").each(function () {
		var pi = $(this).attr("id");
		var arr = pi.split("-");
		pi = arr[1];

		itotal =
			itotal +
			accounting.unformat(
				$("#amount-" + pi).val(),
				accounting.settings.number.decimal
			);
		$("#total_items_count").html(itotal);
	});

	// Sum total tax and discount for purchase entry
	var totalTax = 0;
	var totalDiscount = 0;
	$('input[name="taxa[]"]').each(function () {
		totalTax += accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
	});
	$('input[name="disca[]"]').each(function () {
		totalDiscount += accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
	});
	$("#taxr").text(accounting.formatNumber(totalTax));
	$("#discs").text(accounting.formatNumber(totalDiscount));
};

var o_rowTotal = function (numb) {
	//most res
	var result;
	var totalValue;
	var amountVal = formInputGet("#amount", numb);
	var priceVal = formInputGet("#price", numb);
	var discountVal = formInputGet("#discount", numb);
	if (discountVal == "") {
		$("#discount-" + numb).val(0);
		discountVal = 0;
	}
	var vatVal = formInputGet("#vat", numb);
	if (vatVal == "") {
		$("#vat-" + numb).val(0);
		vatVal = 0;
	}
	var taxo = 0;
	var disco = 0;
	var totalPrice = parseFloat(amountVal).toFixed(2) * priceVal;
	var tax_status = $("#taxformat option:selected").val();
	var disFormat = $("#discount_format").val();

	//tax after bill
	if (tax_status == "yes") {
		if (disFormat == "%" || disFormat == "flat") {
			//tax
			var Inpercentage = precentCalc(totalPrice, vatVal);
			totalValue = parseFloat(totalPrice) + parseFloat(Inpercentage);
			taxo = deciFormat(Inpercentage);

			if (disFormat == "flat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalValue) - parseFloat(discountVal);
			} else if (disFormat == "%") {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = parseFloat(totalValue) - parseFloat(discount);
				disco = deciFormat(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discountVal);
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discount);
				disco = deciFormat(discount);
			}

			//tax
			var Inpercentage = precentCalc(totalValue, vatVal);
			totalValue = parseFloat(totalValue) + parseFloat(Inpercentage);
			taxo = deciFormat(Inpercentage);
		}

		// Ensure tax is calculated even if discount format doesn't match
		if (taxo === 0 && vatVal > 0) {
			var Inpercentage = precentCalc(totalPrice, vatVal);
			totalValue = parseFloat(totalPrice) + parseFloat(Inpercentage);
			taxo = deciFormat(Inpercentage);

			// Apply discount if exists
			if (discountVal > 0) {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = parseFloat(totalValue) - parseFloat(discount);
				disco = deciFormat(discount);
			}
		}
	} else if (tax_status == "inclusive") {
		if (disFormat == "%" || disFormat == "flat") {
			//tax
			var Inpercentage = (+totalPrice * +vatVal) / (100 + +vatVal);
			totalValue = parseFloat(totalPrice);
			taxo = deciFormat(Inpercentage);

			if (disFormat == "flat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalValue) - parseFloat(discountVal);
			} else if (disFormat == "%") {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = parseFloat(totalValue) - parseFloat(discount);
				disco = deciFormat(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discountVal);
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discount);
				disco = deciFormat(discount);
			}

			//tax
			var Inpercentage = (+totalPrice * +vatVal) / (100 + +vatVal);
			totalValue = parseFloat(totalValue);
			taxo = deciFormat(Inpercentage);
		}
	} else {
		taxo = 0;
		if (disFormat == "%" || disFormat == "flat") {
			//tax

			//  totalValue = deciFormat(totalPrice);

			if (disFormat == "flat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discountVal);
			} else if (disFormat == "%") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discount);
				disco = deciFormat(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = deciFormat(discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discountVal);
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = parseFloat(totalPrice) - parseFloat(discount);
				disco = deciFormat(discount);
			}
		}
	}
	$("#result-" + numb).html(deciFormat(totalValue));
	$("#taxa-" + numb).val(taxo);
	$("#texttaxa-" + numb).text(taxo);
	$("#disca-" + numb).val(disco);
	var totalID = "#total-" + numb;
	$(totalID).val(deciFormat(totalValue));
	samanYog();
};
var rowTotal = function (numb) {
	//most res
	var result;
	var page = "";
	var totalValue = 0;
	var amountVal = accounting.unformat(
		$("#amount-" + numb).val(),
		accounting.settings.number.decimal
	);
	var priceVal = accounting.unformat(
		$("#price-" + numb).val(),
		accounting.settings.number.decimal
	);
	var discountVal = accounting.unformat(
		$("#discount-" + numb).val(),
		accounting.settings.number.decimal
	);
	var vatVal = accounting.unformat(
		$("#vat-" + numb).val(),
		accounting.settings.number.decimal
	);
	// Stock validation for POS
	var alertVal = accounting.unformat(
		$("#alert-" + numb).val(),
		accounting.settings.number.decimal
	);
	if (amountVal > alertVal && alertVal > 0) {
		// Reset quantity to available stock and show alert
		$("#amount-" + numb).val(accounting.formatNumber(alertVal));
		amountVal = alertVal;
		$("#stock_alert").modal("toggle");
	}
	// Recalculate totalPrice after potential amountVal modification
	var totalPrice = amountVal * priceVal;
	var disFormat = $("#discount_format").val();
	if ($("#inv_page").val() == "new_i" && formInputGet("#pid", numb) > 0) {
		var alertVal = accounting.unformat(
			$("#alert-" + numb).val(),
			accounting.settings.number.decimal
		);
		if (alertVal <= +amountVal) {
			var aqt = alertVal - amountVal;
			alert("Low Stock! " + accounting.formatNumber(aqt));
		}
	}
	//tax after bill
	if (tax_status == "yes") {
		if (disFormat == "%" || disFormat == "flat") {
			//tax
			var Inpercentage = precentCalc(totalPrice, vatVal);
			totalValue = totalPrice + Inpercentage;
			taxo = accounting.formatNumber(Inpercentage);
			if (disFormat == "flat") {
				disco = accounting.formatNumber(amountVal * discountVal);
				totalValue = totalValue - discountVal * amountVal;
			} else if (disFormat == "%") {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = totalValue - discount;
				disco = accounting.formatNumber(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = accounting.formatNumber(discountVal * amountVal);
				totalValue = totalPrice - discountVal * amountVal;
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = totalPrice - discount;
				disco = accounting.formatNumber(discount);
			}

			//tax
			var Inpercentage = precentCalc(totalValue, vatVal);
			totalValue = totalValue + Inpercentage;
			taxo = accounting.formatNumber(Inpercentage);
		}

		// Ensure tax is calculated even if discount format doesn't match
		if (taxo === 0 && vatVal > 0) {
			var Inpercentage = precentCalc(totalPrice, vatVal);
			totalValue = totalPrice + Inpercentage;
			taxo = accounting.formatNumber(Inpercentage);

			// Apply discount if exists
			if (discountVal > 0) {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = totalValue - discount;
				disco = accounting.formatNumber(discount);
			}
		}
	} else if (tax_status == "inclusive") {
		if (disFormat == "%" || disFormat == "flat") {
			//tax
			var Inpercentage = (totalPrice * vatVal) / (100 + vatVal);
			totalValue = totalPrice;
			taxo = accounting.formatNumber(Inpercentage);
			if (disFormat == "flat") {
				disco = accounting.formatNumber(discountVal * amountVal);
				totalValue = totalValue - discountVal * amountVal;
			} else if (disFormat == "%") {
				var discount = precentCalc(totalValue, discountVal);
				totalValue = totalValue - discount;
				disco = accounting.formatNumber(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = accounting.formatNumber(discountVal * amountVal);
				totalValue = totalPrice - discountVal;
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = totalPrice - discount;
				disco = accounting.formatNumber(discount);
			}
			//tax
			var Inpercentage = (totalPrice * vatVal) / (100 + vatVal);
			totalValue = totalValue;
			taxo = accounting.formatNumber(Inpercentage);
		}
	} else {
		taxo = 0;
		if (disFormat == "%" || disFormat == "flat") {
			if (disFormat == "flat") {
				disco = accounting.formatNumber(discountVal * amountVal);
				totalValue = totalPrice - discountVal * amountVal;
			} else if (disFormat == "%") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = totalPrice - discount;
				disco = accounting.formatNumber(discount);
			}
		} else {
			//before tax
			if (disFormat == "bflat") {
				disco = accounting.formatNumber(discountVal * amountVal);
				totalValue = totalPrice - discountVal * amountVal;
			} else if (disFormat == "b_p") {
				var discount = precentCalc(totalPrice, discountVal);
				totalValue = totalPrice - discount;
				disco = accounting.formatNumber(discount);
			}
		}
	}
	$("#result-" + numb).html(accounting.formatNumber(totalValue));
	$("#taxa-" + numb).val(taxo);
	$("#texttaxa-" + numb).text(taxo);
	$("#disca-" + numb).val(disco);
	$("#total-" + numb).val(accounting.formatNumber(totalValue));
	samanYog();
};
var calculateProfit = function (numb) {
	var price = parseFloat($("#price-" + numb).val()) || 0;
	var selling = parseFloat($("#selling_price-" + numb).val()) || 0;
	var profit = selling - price;
	var profit_margin = price > 0 ? (profit / price) * 100 : 0;
	$("#profit-" + numb).val(profit.toFixed(2));
	$("#profit_margin-" + numb).val(profit_margin.toFixed(2));
};
var changeTaxFormat = function (getSelectv) {
	if (getSelectv == "yes") {
		var tformat = $("#taxformat option:selected").data("tformat");
		var trate = $("#taxformat option:selected").data("trate");
		$("#tax_status").val(tformat);
		$("#tax_format").val("%");
	} else if (getSelectv == "inclusive") {
		var tformat = $("#taxformat option:selected").data("tformat");
		var trate = $("#taxformat option:selected").data("trate");
		$("#tax_status").val(tformat);
		$("#tax_format").val("incl");
	} else {
		$("#tax_status").val("no");
		$("#tax_format").val("off");
	}
	var discount_handle = $("#discountFormat").val();
	var tax_handle = $("#tax_format").val();
	formatRest(tax_handle, discount_handle, trate);
};

var changeDiscountFormat = function (getSelectv) {
	if (getSelectv != "0") {
		$(".disCol").show();
		$("#discount_handle").val("yes");
		$("#discount_format").val(getSelectv);
	} else {
		$("#discount_format").val(getSelectv);
		$(".disCol").hide();
		$("#discount_handle").val("no");
	}
	var tax_status = $("#tax_format").val();
	formatRest(tax_status, getSelectv);
};

function formatRest(taxFormat, disFormat, trate = "") {
	var amntArray = [];
	var idArray = [];
	$(".amnt").each(function () {
		var v = accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
		var id_e = $(this).attr("id");
		id_e = id_e.split("-");
		idArray.push(id_e[1]);
		amntArray.push(v);
	});
	var prcArray = [];
	$(".prc").each(function () {
		var v = accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
		prcArray.push(v);
	});
	var vatArray = [];
	$(".vat").each(function () {
		if (trate) {
			var v = accounting.unformat(trate, accounting.settings.number.decimal);
			$(this).val(v);
		} else {
			var v = accounting.unformat(
				$(this).val(),
				accounting.settings.number.decimal
			);
		}
		vatArray.push(v);
	});

	var discountArray = [];
	$(".discount").each(function () {
		var v = accounting.unformat(
			$(this).val(),
			accounting.settings.number.decimal
		);
		discountArray.push(v);
	});

	var taxr = 0;
	var discsr = 0;
	for (var i = 0; i < idArray.length; i++) {
		var x = idArray[i];
		amtVal = amntArray[i];
		prcVal = prcArray[i];
		vatVal = vatArray[i];
		discountVal = discountArray[i];
		var result = amtVal * prcVal;
		if (vatVal == "") {
			vatVal = 0;
		}
		if (discountVal == "") {
			discountVal = 0;
		}
		if (taxFormat == "%") {
			if (disFormat == "%" || disFormat == "flat") {
				var Inpercentage = precentCalc(result, vatVal);
				var result = result + Inpercentage;
				taxr = taxr + Inpercentage;
				$("#texttaxa-" + x).html(accounting.formatNumber(Inpercentage));
				$("#taxa-" + x).val(accounting.formatNumber(Inpercentage));

				if (disFormat == "%") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "flat") {
					result = parseFloat(result) - parseFloat(discountVal);
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}
			} else {
				if (disFormat == "b_p") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "bflat") {
					result = result - discountVal;
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}

				var Inpercentage = precentCalc(result, vatVal);
				result = result + Inpercentage;
				taxr = taxr + Inpercentage;
				$("#texttaxa-" + x).html(accounting.formatNumber(Inpercentage));
				$("#taxa-" + x).val(accounting.formatNumber(Inpercentage));
			}
		} else if (taxFormat == "incl") {
			if (disFormat == "%" || disFormat == "flat") {
				var Inpercentage = (result * vatVal) / (100 + vatVal);

				taxr = taxr + Inpercentage;
				$("#texttaxa-" + x).html(accounting.formatNumber(Inpercentage));
				$("#taxa-" + x).val(accounting.formatNumber(Inpercentage));

				if (disFormat == "%") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "flat") {
					result = result - discountVal;
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}
			} else {
				if (disFormat == "b_p") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "bflat") {
					result = result - discountVal;
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}

				var Inpercentage = (result * vatVal) / (100 + vatVal);
				taxr = taxr + Inpercentage;
				$("#texttaxa-" + x).html(accounting.formatNumber(Inpercentage));
				$("#taxa-" + x).val(accounting.formatNumber(Inpercentage));
			}
		} else {
			if (disFormat == "%" || disFormat == "flat") {
				var result =
					accounting.unformat(
						$("#amount-" + x).val(),
						accounting.settings.number.decimal
					) *
					accounting.unformat(
						$("#price-" + x).val(),
						accounting.settings.number.decimal
					);
				$("#texttaxa-" + x).html("Off");
				$("#taxa-" + x).val(0);
				taxr += 0;

				if (disFormat == "%") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "flat") {
					var result = result - discountVal;
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}
			} else {
				if (disFormat == "b_p") {
					var Inpercentage = precentCalc(result, discountVal);
					result = result - Inpercentage;
					$("#disca-" + x).val(accounting.formatNumber(Inpercentage));
					discsr = discsr + Inpercentage;
				} else if (disFormat == "bflat") {
					result = result - discountVal;
					$("#disca-" + x).val(accounting.formatNumber(discountVal));
					discsr += discountVal;
				}
				$("#texttaxa-" + x).html("Off");
				$("#taxa-" + x).val(0);
				taxr += 0;
			}
		}

		$("#total-" + x).val(accounting.formatNumber(result));
		$("#result-" + x).html(accounting.formatNumber(result));

		// Fallback: Ensure tax is calculated even if discount format doesn't match
		if (
			taxFormat == "%" &&
			vatVal > 0 &&
			parseFloat($("#taxa-" + x).val()) == 0
		) {
			var fallbackResult = amtVal * prcVal;
			var fallbackTax = precentCalc(fallbackResult, vatVal);
			taxr = taxr + fallbackTax;
			$("#texttaxa-" + x).html(accounting.formatNumber(fallbackTax));
			$("#taxa-" + x).val(accounting.formatNumber(fallbackTax));

			// Apply discount if exists and recalculate
			if (discountVal > 0) {
				var discountAmount = precentCalc(
					fallbackResult + fallbackTax,
					discountVal
				);
				result = fallbackResult + fallbackTax - discountAmount;
				discsr = discsr + discountAmount;
				$("#disca-" + x).val(accounting.formatNumber(discountAmount));
				$("#total-" + x).val(accounting.formatNumber(result));
				$("#result-" + x).html(accounting.formatNumber(result));
			}
		}
	}
	var sum = accounting.formatNumber(samanYog());
	$("#subttlid").html(sum);
	$("#taxr").html(accounting.formatNumber(taxr));
	$("#discs").html(accounting.formatNumber(discsr));
	billUpyog();
}

//remove productrow

$("#saman-row").on("click", ".removeProd", function () {
	var pidd = $(this).closest("tr").find(".pdIn").val();
	var pqty = $(this).closest("tr").find(".amnt").val();
	pqty = pidd + "-" + pqty;
	$("<input>")
		.attr({
			type: "hidden",
			id: "restock",
			name: "restock[]",
			value: pqty,
		})
		.appendTo("form");
	$(this).closest("tr").remove();
	$("#d" + $(this).closest("tr").find(".pdIn").attr("id"))
		.closest("tr")
		.remove();
	$(".amnt").each(function (index) {
		rowTotal(index);
		billUpyog();
	});

	return false;
});
$("#productname-0").autocomplete({
	source: function (request, response) {
		$.ajax({
			url: baseurl + "search_products/" + billtype,
			dataType: "json",
			method: "post",
			data:
				"name_startsWith=" +
				request.term +
				"&type=product_list&row_num=1&wid=" +
				$("#s_warehouses option:selected").val() +
				"&" +
				d_csrf,
			success: function (data) {
				response(
					$.map(data, function (item) {
						var product_d = item[0];
						return {
							label: product_d,
							value: product_d,
							data: item,
						};
					})
				);
			},
		});
	},
	autoFocus: true,
	minLength: 0,
	select: function (event, ui) {
		var t_r = ui.item.data[3];
		var discount = ui.item.data[4];
		var custom_discount = $("#custom_discount").val();
		if (custom_discount > 0) discount = deciFormat(custom_discount);
		if (t_r == 0 && discount == 5) t_r = 13;
		$("#amount-0").val(1);
		$("#price-0").val(ui.item.data[1]);
		$("#pid-0").val(ui.item.data[2]);
		$("#vat-0").val(t_r);
		$("#discount-0").val(discount);
		$("#dpid-0").val(ui.item.data[5]);
		$("#unit-0").val(ui.item.data[6]);
		$("#hsn-0").val(ui.item.data[7]);
		$("#alert-0").val(ui.item.data[8]);
		$("#serial-0").val(ui.item.data[10]);
		rowTotal(0);

		billUpyog();
		calculateProfit(0);
	},
});
$(document).on("click", ".select_pos_item", function (e) {
	var pid = $(this).attr("data-pid");
	var stock = accounting.unformat(
		$(this).attr("data-stock"),
		accounting.settings.number.decimal
	);
	var flag = true;
	var discount = $(this).attr("data-discount");
	var custom_discount = accounting.unformat(
		$("#custom_discount").val(),
		accounting.settings.number.decimal
	);
	if (custom_discount > 0) discount = accounting.formatNumber(custom_discount);

	$(".pdIn").each(function () {
		if (pid == $(this).val()) {
			var pi = $(this).attr("id");
			var arr = pi.split("-");
			pi = arr[1];
			$("#discount-" + pi).val(discount);
			var stotal =
				accounting.unformat(
					$("#amount-" + pi).val(),
					accounting.settings.number.decimal
				) + 1;
			if (stotal <= stock) {
				$("#amount-" + pi).val(accounting.formatNumber(stotal));
				$("#search_bar").val("").focus();
			} else {
				$("#stock_alert").modal("toggle");
			}
			rowTotal(pi);
			billUpyog();
			$("#amount-" + pi).focus();
			flag = false;
		}
	});
	var t_r = $(this).attr("data-tax");
	if (
		"#taxformat option:selected" &&
		$("#taxformat option:selected").attr("data-trate")
	) {
		t_r = $("#taxformat option:selected").attr("data-trate");
	}
	if (flag) {
		var ganak = $("#ganak").val();
		var cvalue = parseInt(ganak);
		var functionNum = "'" + cvalue + "'";
		count = $("#saman-row div").length;
		// --- Batch logic start ---
		var batchidInput = "";
		if ($(this).attr("data-batchid")) {
			batchidInput =
				'<input type="hidden" name="batchid[]" value="' +
				$(this).attr("data-batchid") +
				'">';
		}
		// --- Batch logic end ---
		var data =
			'<tr id="ppid-' +
			cvalue +
			'" class="mb-1"><td colspan="7" ><input type="text" class="form-control text-center p-mobile" name="product_name[]" placeholder="Enter Product name or Code" id="productname-' +
			cvalue +
			'" value="' +
			$(this).attr("data-name") +
			"-" +
			$(this).attr("data-pcode") +
			'"><input type="hidden" id="alert-' +
			cvalue +
			'" value="' +
			$(this).attr("data-stock") +
			'"  name="alert[]"></td></tr><tr><td><input type="text" inputmode="numeric" class="form-control p-mobile p-width req amnt" name="product_qty[]" id="amount-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off" value="1" ></td> <td><input type="text" class="form-control p-width p-mobile req prc" name="product_price[]"  inputmode="numeric" id="price-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off"  value="' +
			$(this).attr("data-price") +
			'"></td><td> <input type="text" class="form-control p-mobile p-width vat" inputmode="numeric" name="product_tax[]" id="vat-' +
			cvalue +
			'" onkeypress="return isNumber(event)" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off"  value="' +
			t_r +
			'"></td>  <td><input type="text" class="form-control p-width p-mobile discount pos_w" name="product_discount[]" inputmode="numeric" onkeypress="return isNumber(event)" id="discount-' +
			cvalue +
			'" onkeyup="rowTotal(' +
			functionNum +
			'), billUpyog()" autocomplete="off"  value="' +
			discount +
			'" inputmode="numeric"></td> <td><span class="currenty">' +
			currency +
			"</span> <strong><span class='ttlText' id=\"result-" +
			cvalue +
			'">0</span></strong></td> <td class="text-center"><button type="button" data-rowid="' +
			cvalue +
			'" class="btn btn-danger removeItem" title="Remove" > <i class="fa fa-minus-square"></i> </button> </td><input type="hidden" name="taxa[]" id="taxa-' +
			cvalue +
			'" value="0"><input type="hidden" name="disca[]" id="disca-' +
			cvalue +
			'" value="0"><input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' +
			cvalue +
			'" value="0"> <input type="hidden" class="pdIn" name="pid[]" id="pid-' +
			cvalue +
			'" value="' +
			$(this).attr("data-pid") +
			'"> <input type="hidden" name="unit[]" id="unit-' +
			cvalue +
			'" value="' +
			$(this).attr("data-unit") +
			'"> <input type="hidden" name="hsn[]" id="hsn-' +
			cvalue +
			'" value="' +
			$(this).attr("data-pcode") +
			'"> <input type="hidden" name="serial[]" id="serial-' +
			cvalue +
			'" value="' +
			$(this).attr("data-serial") +
			'">' +
			batchidInput +
			"</tr>";
		//ajax request
		// $('#saman-row').append(data);
		$("#pos_items").append(data);
		rowTotal(cvalue);
		billUpyog();

		// Trigger tax recalculation to ensure VAT is properly calculated
		if (typeof formatRest === "function") {
			var taxFormat = $("#tax_format").val();
			var disFormat = $("#discount_format").val();
			var trate = $("#taxformat option:selected").attr("data-trate");
			formatRest(taxFormat, disFormat, trate);
		}

		$("#ganak").val(cvalue + 1);
		$("#amount-" + cvalue).focus();
	}
});

$("#saman-pos2").on("click", ".removeItem", function () {
	var pidd = $(this).attr("data-rowid");
	var pqty = accounting.unformat(
		$("#amount-" + pidd).val(),
		accounting.settings.number.decimal
	);
	var old_amnt = $("#amount_old-" + pidd).val();
	if (old_amnt) {
		pqty = pidd + "-" + pqty;
		$("<input>")
			.attr({
				type: "hidden",
				name: "restock[]",
				value: pqty,
			})
			.appendTo("form");
	}
	$("#ppid-" + pidd).remove();
	$(".amnt").each(function (index) {
		rowTotal(index);
	});
	billUpyog();
	return false;
});

$("#saman-row-pos").on("click", ".removeItem", function () {
	var pidd = $(this).closest("tr").find(".pdIn").val();
	var pqty = accounting.unformat(
		$(this).closest("tr").find(".amnt").val(),
		accounting.settings.number.decimal
	);
	var old_amnt = accounting.unformat(
		$(this).closest("tr").find(".old_amnt").val(),
		accounting.settings.number.decimal
	);
	if (old_amnt) {
		pqty = pidd + "-" + pqty;
		$("<input>")
			.attr({
				type: "hidden",
				name: "restock[]",
				value: pqty,
			})
			.appendTo("form");
	}
	$(this).closest("tr").remove();
	$("#d" + $(this).closest("tr").find(".pdIn").attr("id"))
		.closest("tr")
		.remove();
	$("#p" + $(this).closest("tr").find(".pdIn").attr("id")).remove();
	$(".amnt").each(function (index) {
		rowTotal(index);
	});
	billUpyog();

	return false;
});

$(document).on("click", ".quantity-up", function (e) {
	var spinner = $(this);
	var input = spinner.closest(".quantity").find('input[name="product_qty[]"]');
	var oldValue = accounting.unformat(
		input.val(),
		accounting.settings.number.decimal
	);
	var id_arr = $(input).attr("id");
	var id = id_arr.split("-")[1];
	var stock = accounting.unformat(
		$("#alert-" + id).val(),
		accounting.settings.number.decimal
	);

	if (oldValue + 1 > stock && stock > 0) {
		$("#stock_alert").modal("toggle");
		return false;
	}

	var newVal = oldValue + 1;
	spinner
		.closest(".quantity")
		.find('input[name="product_qty[]"]')
		.val(accounting.formatNumber(newVal));
	spinner
		.closest(".quantity")
		.find('input[name="product_qty[]"]')
		.trigger("change");
	rowTotal(id);
	billUpyog();
	return false;
});

$(document).on("click", ".quantity-down", function (e) {
	var spinner = $(this);
	var input = spinner.closest(".quantity").find('input[name="product_qty[]"]');
	var oldValue = accounting.unformat(
		input.val(),
		accounting.settings.number.decimal
	);
	var min = 1;
	if (oldValue <= min) {
		var newVal = oldValue;
	} else {
		var newVal = oldValue - 1;
	}
	spinner
		.closest(".quantity")
		.find('input[name="product_qty[]"]')
		.val(accounting.formatNumber(newVal));
	spinner
		.closest(".quantity")
		.find('input[name="product_qty[]"]')
		.trigger("change");
	var id_arr = $(input).attr("id");
	id = id_arr.split("-");
	rowTotal(id[1]);
	billUpyog();
	return false;
});

// -- Barcode Scan Handling and Automatic Product Selection --

// $('#v2_search_bar').keypress(function(event) {
//     if (event.keyCode == 13) {  // Enter key was pressed
//         setTimeout(function() {
//             // Make the AJAX request to search for the product using the barcode
//             $.ajax({
//                 url: baseurl + 'search_products/v2_pos_search',  // Use your actual URL for product search
//                 dataType: "json",
//                 method: 'POST',
//                 data: {
//                     barcode: $('#v2_search_bar').val(),  // Send barcode value
//                     csrf_token: csrf_hash  // CSRF token (replace with your token variable)
//                 },
//                 beforeSend: function() {
//                     // Optional: Show loading spinner or some indication of the request being processed
//                     $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
//                 },
//                 success: function(data) {
//                     // Update the product list with the search results
//                     $("#pos_item").html(data);

//                     // Automatically click the product if only one product is found
//                     if ($("#pos_item .select_pos_item").length === 1) {
//                         $("#pos_item .select_pos_item").trigger('click');
//                     }
//                 },
//                 error: function() {
//                     alert("Error scanning barcode. Please try again.");
//                 }
//             });
//         }, 700);  // Adjust timeout as needed
//     }
// });

// Additional event listener for when a product is clicked (added to cart)
// $(document).on('click', '#pos_item .select_pos_item', function() {
//     // Trigger the cart addition logic (already handled)
//     $(this).trigger('click');
// });
