<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pos Logs</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
        }
        .container {
            margin-top: 30px;
        }
        .table th, .table td {
            text-align: center;
        }
        .table th {
            background-color:rgba(0, 30, 255, 0.65);
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .pagination {
            justify-content: center;
        }
        .pagination a {
            margin: 0 5px;
        }
        .pagination a:hover {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
<h1 class="text-center mb-4" style="font-family: 'Poppins', sans-serif; font-size: 26px;">Logs</h1>



    <?php if (empty($logs)) : ?>
        <div class="alert alert-warning" role="alert">
            No logs available.
        </div>
    <?php else : ?>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Note</th>
                    <th>User</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo $log['note']; ?></td>
                        <td><?php echo $log['user']; ?></td>
                       <td><?php echo $log['created']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
