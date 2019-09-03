<?php
    require_once 'utils.php';
    require_once 'pdo.php';

    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once 'head.php'; ?>
    <title>Ed Dibb's Resume Registry</title>
</head>
<body>
    <div class="container">
        <h1>Ed Dibb's Resume Registry</h1>

        <?php
            echo(flashMessage());
            $query = $pdo->query('SELECT * FROM profile');
            if ($query->rowCount() > 0) {
                echo('
                    <table class="table table-sm table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Headline</th>
                ');
                if (isset($_SESSION['name'])) {
                    echo('<th>Action</th>');
                }
                echo('
                    </thead>
                    <tbody>
                ');

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo('
                        <tr>
                            <td><a href="view.php?profile_id=' . $row['profile_id'] . '">' . $row['first_name'] . '</a></td>
                            <td>' . $row['last_name'] . '</td>
                            <td>' . $row['email'] . '</td>
                            <td>' . $row['headline'] . '</td>
                    ');
                    if (isset($_SESSION['name'])) {
                        echo('        
                            <td style="width: 100px"><a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / <a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a></td>
                        ');
                    }
                    echo('</tr>');
                }
                echo('
                    </tbody>
                    </table>
                '); 
            }
            else {
                echo('No results found. <br>');
            }
            if (!isset($_SESSION['email'])) {
                echo('<a href="login.php">Please log in</a>');
            }
            else {
                echo(
                    '<a href="add.php">Add New Entry</a><br>
                    <a href="logout.php">Logout</a>'
                );
            }
        ?>
    </div>
</body>
</html>
