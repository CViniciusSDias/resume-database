<?php

function flashMessage () {
    $msg = "";
    if (isset($_SESSION['success'])) {
        $msg = '<p class="alert alert-success">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        $msg = '<p class="alert alert-danger">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }
    return $msg;
}

function checkInput () {
    $arr = [ /****/
        [
            'fieldOne' => 'edu-year',
            'fieldTwo' => 'school'
        ],
        [
            'fieldOne' => 'pos-year',
            'fieldTwo' => 'desc'
        ]
    ];

    function checkExtraFields ($arr, $maxNoFields) {
        for ($i = 0; $i < count($arr); $i++) {
            for ($j = 0; $j < $maxNoFields; $j++) {
                if (isset($_POST[$arr[$i]['fieldOne'] . $j]) && isset($_POST[$arr[$i]['fieldTwo'] . $j])) {
                    if (strlen($_POST[$arr[$i]['fieldOne'] . $j]) > 0 && strlen($_POST[$arr[$i]['fieldTwo'] . $j]) > 0) {
                        if (ctype_digit($_POST[$arr[$i]['fieldOne'] . $j])) {
                            continue;
                        }
                        return 'Year field must be numeric.';
                    }
                    return 'All values are required.';
                }
            }
        }
        return true;
    }

    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
        if (strlen($_POST['first_name']) > 0 && strlen($_POST['last_name']) > 0 && strlen($_POST['email']) > 0 && strlen($_POST['headline']) > 0 && strlen($_POST['summary']) > 0) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                return checkExtraFields($arr, 9);
            }
            return 'Invalid email address.';
        }
        return 'All values are required.';
    }
}


function sessionCheck () {
    if (!isset($_SESSION['email'])) {
        die('ACCESS DENIED');
    }
}

function commonCheck ($pdo) {
    $profileId = filter_input(INPUT_GET, 'profile_id', FILTER_VALIDATE_INT);
    if (false === $profileId) {
        $_SESSION['error'] = 'No user selected.';
        header('Location: index.php');
        exit();
    }

    $query = $pdo->query('SELECT * FROM profile WHERE profile_id =' . $profileId);
    if ($query->rowCount() == 0) {
        $_SESSION['error'] = 'Profile not found.';
        header('Location: index.php');
        exit();
    }
}

function cancelCheck () {
    if (isset($_POST['cancel'])) {
        $_POST = [];
        header('Location: index.php');
        return;
    }
}