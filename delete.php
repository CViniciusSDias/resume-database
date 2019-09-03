<?php
    require_once 'utils.php';
    require_once 'pdo.php';

    session_start();
    sessionCheck();
    cancelCheck();
    commonCheck($pdo);

    if (isset($_POST['delete'])) {
        $pdo->query('DELETE FROM profile WHERE profile_id =' . $_GET['profile_id']);
        $_SESSION['success'] = 'Profile deleted.';
        header('Location: index.php');
        return;
    }

    $query = $pdo->query('SELECT * FROM profile WHERE profile_id =' . $_GET['profile_id'])->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <?php include_once 'head.php'; ?>
    <title>Ed Dibb's Resume Registry</title>
</head>
<body>
    <div class="container">
        <h1>Delete Profile</h1>
        <form method="POST">
            Confirm profile deletion:<br><br>
            <strong>First name: </strong><?= $query['first_name'] ?><br>
            <strong>Last name: </strong><?= $query['last_name'] ?><br><br>
            <input type="submit" name="delete" value="Delete" class="btn btn-danger">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-outline-secondary">
        </form>
    </div>
</body>
</html>