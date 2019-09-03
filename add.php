<?php
    require_once 'pdo.php';
    require_once 'utils.php';

    session_start();
    sessionCheck();
    cancelCheck();

    if (isset($_POST['Add'])) {
        $msg = checkInput();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header('Location: add.php');
            return; 
        }   

        $query = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)');
        $query->execute([
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => htmlentities($_POST['first_name']),
            ':last_name' => htmlentities($_POST['last_name']),
            ':email' => htmlentities($_POST['email']),
            ':headline' => htmlentities($_POST['headline']),
            ':summary' => htmlentities($_POST['summary'])
        ]);



        $profile_id = $pdo->lastInsertId();

        for ($i = 0; $i < 9; $i++) {
            if (isset($_POST['pos-year' . $i])) {
                $queryPos = $pdo->prepare('INSERT INTO position (profile_id, pos_rank, year, description) VALUES (:profile_id, :pos_rank, :year, :description)');
                $queryPos->execute([
                    ':profile_id' => $profile_id,
                    ':pos_rank' => $i + 1,
                    ':year' => htmlentities($_POST['pos-year' . $i]),
                    ':description' => htmlentities($_POST['desc' . $i])
                ]);
            }
            if (isset($_POST['edu-year' . $i])) {
                $schoolId = $pdo->query('SELECT institution_id FROM institution WHERE name ="' . htmlentities($_POST['school' . $i]) . '"');
                if ($schoolId->rowCount() > 0) {
                    $schoolId = $schoolId->fetch(PDO::FETCH_ASSOC);
                    $schoolId = $schoolId['institution_id'];
                }
                else {
                    $querySchool = $pdo->query('INSERT INTO institution (name) VALUES ("' . htmlentities($_POST['school' . $i]) . '")');
                    $schoolId = $pdo->lastInsertId();
                }

                $queryEdu = $pdo->prepare('INSERT INTO education (profile_id, institution_id, edu_rank, year) VALUES (:profile_id, :institution_id, :edu_rank, :year)');
                $queryEdu->execute([
                    ':profile_id' => $profile_id,
                    ':institution_id' => $schoolId,
                    ':edu_rank' => $i + 1,
                    ':year' => htmlentities($_POST['edu-year' . $i]),
                ]);
            }
        }

        $_SESSION['success'] = 'Record added.';
        header('Location: index.php');
        return;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once 'head.php'; ?>
    <title>Ed Dibb's Resume Registry</title>
</head>
<body>
    <div class="container">
        <?php
            echo('<h1>Adding profile for ' . $_SESSION['name'] . '</h1><br>');
            echo(flashMessage());
        ?>
        <form method="POST">
            <label for="first_name">First Name:</label> <input type="text" name="first_name" id="first_name" class="form-control" ><br> <!-- chinho tim amu my developer -->
            <label for="last_name">Last Name:</label> <input type="text" name="last_name" id="last_name" class="form-control" ><br>
            <label for="email">Email:</label> <input type="text" name="email" id="email" class="form-control" ><br>
            <label for="headline">Headline:</label> <input type="text" name="headline" id="headline" class="form-control" ><br>
            <label for="summary">Summary:</label><br><textarea name="summary" id="summary" class="form-control" cols="70" rows="10"></textarea><br>
            <span style="display: inline-block; width: 80px">Education</span><button id="btn-add-education" class="btn btn-info">+</button><br><br>
            <div id="add-education"></div>
            <span style="display: inline-block; width: 80px">Position</span><button id="btn-add-position" class="btn btn-info">+</button><br><br>
            <div id="add-position"></div>
            <input type="submit" name="Add" value="Add" class="btn btn-primary">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-outline-secondary">
        </form>
    </div>
</body>
<script src="utils.js"></script>
<script>
    document.querySelector('[name="first_name"').value = faker.name.firstName();
    document.querySelector('[name="last_name"').value = faker.name.lastName();
    document.querySelector('[name="email"').value = faker.internet.email();
    document.querySelector('[name="headline"').value = faker.lorem.words();
    document.querySelector('[name="summary"').value = faker.lorem.sentence();
</script>
</html>
