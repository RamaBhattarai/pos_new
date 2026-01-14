<h2>Add Fiscal Year</h2>
<form method="post" action="<?= base_url('fiscal_year/store') ?>">
    Year: <input type="text" name="year" required><br>
    Start Date: <input type="date" name="start_date" required><br>
    End Date: <input type="date" name="end_date" required><br>
    Current: <input type="checkbox" name="is_current"><br>
    <button type="submit">Save</button>
</form>
