<!doctype html>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Day End Report</title>

    <style>
        body {
            color: #2B2000;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            line-height: 16pt;
            text-align: left;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .mfill {
            background-color: #eee;
        }

        .descr {
            font-size: 10pt;
            color: #515151;
        }

        .invoice-box {
            width: 210mm;
            margin: auto;
            padding: 4mm;
            border: 0;
            font-size: 12pt;
            line-height: 18pt;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24pt;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 12pt;
            color: #666;
        }

        .summary-section {
            margin-bottom: 30px;
        }

        .summary-section h3 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .summary-table {
            width: auto;
            max-width: 400px;
        }

        .summary-table td {
            padding: 5px 15px;
        }

        .summary-table .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .transactions-section h3 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .transactions-table {
            font-size: 10pt;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 6px;
        }

        .amount-column {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>

</head>

<body>

<div class="invoice-box">

    <div class="header">
        <h1>Day End Report</h1>
        <p>Report Period: <?php echo date('M d, Y', strtotime($start_date)); ?> to <?php echo date('M d, Y', strtotime($end_date)); ?></p>
        <?php if ($selected_payment_method): ?>
            <p>Payment Method Filter: <?php echo $selected_payment_method; ?></p>
        <?php endif; ?>
        <?php if ($selected_warehouse): ?>
            <p>Warehouse: <?php echo $warehouse_name ?: $selected_warehouse; ?></p>
        <?php endif; ?>
        <p>Generated on: <?php echo date('M d, Y H:i:s'); ?></p>
    </div>

    <div class="summary-section">
        <h3>Payment Summary</h3>
        <table class="summary-table" style="border-collapse: collapse; width: 100%; max-width: 400px;">
            <tbody>
                <?php foreach ($payment_methods_data as $pm): ?>
                    <?php $key = strtolower($pm['name']); ?>
                    <tr style="border: 1px solid #ddd;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;"><?php echo htmlspecialchars($pm['name']); ?>:</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;"><?php echo amountFormat($totals[$key] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="border: 1px solid #ddd; background-color: #f9f9f9;">
                    <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;"><strong>Grand Total:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><strong><?php echo amountFormat($totals['grand_total']); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="transactions-section">
        <h3>Transaction Details</h3>
        <table class="transactions-table" style="border-collapse: collapse; width: 100%; font-size: 10pt;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left;">#</th>
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left;">Invoice</th>
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left;">Customer</th>
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left;">Payment Method</th>
                    <th style="border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($report_data as $row): ?>
                    <tr style="border: 1px solid #ddd;">
                        <td style="border: 1px solid #ddd; padding: 6px;"><?php echo $counter++; ?></td>
                        <td style="border: 1px solid #ddd; padding: 6px;"><?php echo htmlspecialchars($row['tid']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 6px;"><?php echo htmlspecialchars($row['customer_name'] ?: 'Walk-in Customer'); ?></td>
                        <td style="border: 1px solid #ddd; padding: 6px;"><?php echo date('M d, Y', strtotime($row['invoicedate'])); ?></td>
                        <td style="border: 1px solid #ddd; padding: 6px;"><?php echo htmlspecialchars($row['pmethod']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: right;"><?php echo amountFormat($row['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the POS system.</p>
    </div>

</div>

</body>
</html>