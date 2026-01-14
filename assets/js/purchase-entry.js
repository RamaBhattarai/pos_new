// $(document).on(
// 	"keyup change",
// 	".qty, .rate, .selling_price, .tax",
// 	function () {
// 		let row = $(this).closest("tr");

// 		let qty = parseFloat(row.find(".qty").val()) || 0;
// 		let rate = parseFloat(row.find(".rate").val()) || 0;
// 		let sell = parseFloat(row.find(".selling_price").val()) || 0;
// 		let taxP = parseFloat(row.find(".tax").val()) || 0;

// 		let cost = qty * rate;
// 		let profit = (sell - rate) * qty;
// 		let tax = (cost * taxP) / 100;

// 		row.find(".profit").val(profit.toFixed(2));
// 		row
// 			.find(".profit_margin")
// 			.val(rate > 0 ? (((sell - rate) / rate) * 100).toFixed(2) : 0);

// 		row.find(".tax_amount").text(tax.toFixed(2));
// 	}
// );
// <script src="<?= base_url('assets/js/purchase-entry.js') ?>"></script>;

// Real-time calculation for each row
$(document).on(
	"keyup change",
	".qty, .rate, .selling_price, .tax, .discount",
	function () {
		let row = $(this).closest("tr");

		let qty = parseFloat(row.find(".qty").val()) || 0;
		let rate = parseFloat(row.find(".rate").val()) || 0;
		let sell = parseFloat(row.find(".selling_price").val()) || 0;
		let taxP = parseFloat(row.find(".tax").val()) || 0;
		let discP = parseFloat(row.find(".discount").val()) || 0;

		// === Profit ===
		let profit = (sell - rate) * qty;
		let profitMargin = rate > 0 ? ((sell - rate) / rate) * 100 : 0;

		row.find(".profit").val(profit.toFixed(2));
		row.find(".profit_margin").val(profitMargin.toFixed(2));

		// === Amount Calculation ===
		let subtotal = qty * rate;
		let taxAmount = (subtotal * taxP) / 100;
		let discAmount = (subtotal * discP) / 100;
		let total = subtotal + taxAmount - discAmount;

		row.find(".tax_amount").text(taxAmount.toFixed(2));
		row.find(".ttlText").text(total.toFixed(2));

		// Update hidden input if needed (for form submission)
		let hiddenTotal = row.find(".ttInput");
		if (hiddenTotal.length) {
			hiddenTotal.val(total.toFixed(2));
		}

		// Recalculate grand total
		calculateGrandTotal();
	}
);

// Grand total calculation
function calculateGrandTotal() {
	let totalTax = 0;
	let totalDiscount = 0;
	let subtotal = 0;

	$("tr:not(.last-item-row) .ttlText").each(function () {
		subtotal += parseFloat($(this).text()) || 0;
	});

	$("tr:not(.last-item-row) .tax_amount").each(function () {
		totalTax += parseFloat($(this).text()) || 0;
	});

	// Note: Your HTML uses .discount input, but doesn't show discount amount per row.
	// If you have hidden discount amounts, use them. Otherwise, recompute:
	$("tr:not(.last-item-row)").each(function () {
		let row = $(this);
		let qty = parseFloat(row.find(".qty").val()) || 0;
		let rate = parseFloat(row.find(".rate").val()) || 0;
		let discP = parseFloat(row.find(".discount").val()) || 0;
		let discAmt = (qty * rate * discP) / 100;
		totalDiscount += discAmt;
	});

	// Shipping tax
	let shipping = parseFloat($(".shipVal").val()) || 0;
	let shipTaxType = $("#ship_taxtype").val();
	let shipRate = parseFloat($("#ship_rate").val()) || 0;
	let shipTax = 0;

	if (shipTaxType === "incl") {
		shipTax = (shipping * shipRate) / (100 + shipRate);
	} else {
		shipTax = (shipping * shipRate) / 100;
	}

	$("#ship_final").text(shipTax.toFixed(2));

	// Final totals
	let grandTotal = subtotal + shipTax;

	// Update UI
	$("#taxr").text(totalTax.toFixed(2));
	$("#discs").text(totalDiscount.toFixed(2));
	$("#subttlform").val(subtotal.toFixed(2));
	$("#invoiceyoghtml").val(grandTotal.toFixed(2));
}

$(document).ready(function () {
	$(".qty, .rate, .selling_price, .tax").trigger("change");
});
// Initialize calculation on page load (in case values are pre-filled)
$(document).ready(function () {
	// Trigger calculation for all rows
	$(".qty").trigger("change");
});
