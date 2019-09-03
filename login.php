<?php
    require_once 'utils.php';
    require_once 'pdo.php';

    session_start();
    cancelCheck();

    if (isset($_POST['email']) && isset($_POST['pass'])) {
        if (strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $query = $pdo->query('SELECT * FROM users WHERE email="' . $_POST['email'] . '"');
                if ($query->rowCount() > 0) {
                    $query = $query->fetch(PDO::FETCH_ASSOC);

                    if(hash('md5', 'XyZzy12*_' . $_POST['pass']) === $query['password']) {
                        $_SESSION['email'] = $query['email'];
                        $_SESSION['name'] = $query['name'];
                        $_SESSION['user_id'] = $query['user_id'];
                        header('Location: index.php');
                        return;
                    }
                    else {
                        $_SESSION['error'] = 'Wrong password.';
                    }
                }
                else {
                    $_SESSION['error'] = 'User not found.';
                }
            }
            else {
                $_SESSION['error'] = 'Invalid email address.';
            }
        }
        else {
            $_SESSION['error'] = 'Both fields must be filled out.';
        }
        header('Location: login.php');
        return;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once 'head.php'; ?>
    <title>Ed Dibb's Login Page</title>
</head>
<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?= flashMessage(); ?>
        <form method="POST">
            <strong>Email</strong><br><input type="text" name="email" class="form-control" value="umsi@umich.edu"><br>
            <strong>Password</strong><br><input type="text" name="pass" class="form-control" value="php123"><br>
            <input type="submit" value="Log In" class="btn btn-primary">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-outline-secondary">
        </form>
    </div>
</body>
</html>
