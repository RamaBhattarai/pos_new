<h2>Fiscal Years</h2>
<a href="<?= base_url('fiscal_year/create') ?>">Add New</a>
<table border="1">
    <tr>
        <th>Year</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Current</th>
        <th>Actions</th>
    </tr>
    <?php foreach($fiscal_years as $fy): ?>
    <tr>
        <td><?= $fy->year ?></td>
        <td><?= $fy->start_date ?></td>
        <td><?= $fy->end_date ?></td>
        <td><?= $fy->is_current ? 'Yes' : 'No' ?></td>
        <td>
            <a href="<?= base_url('fiscal_year/edit/'.$fy->id) ?>">Edit</a>
            <a href="<?= base_url('fiscal_year/delete/'.$fy->id) ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>