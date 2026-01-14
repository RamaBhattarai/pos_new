<h2>Edit Fiscal Year</h2>
<form method="post" action="<?= base_url('fiscalyear/update/'.$fiscal_year->id) ?>">
    Year: <input type="text" name="year" value="<?= $fiscal_year->year ?>" required><br>
    Start Date: <input type="date" name="start_date" value="<?= $fiscal_year->start_date ?>" required><br>
    End Date: <input type="date" name="end_date" value="<?= $fiscal_year->end_date ?>" required><br>
    Current: <input type="checkbox" name="is_current" <?= $fiscal_year->is_current ? 'checked' : '' ?>><br>
    <button type="submit">Update</button>
</form>
