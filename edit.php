<?php
    require_once 'utils.php';
    require_once 'pdo.php';

    session_start();
    sessionCheck();
    cancelCheck();
    commonCheck($pdo);

    if (isset($_POST['Save'])) {
        $msg = checkInput();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header('Location: edit.php?profile_id=' . $_GET['profile_id']);
            return;
        }

        $updateQuery = $pdo->prepare('
            UPDATE profile SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                headline = :headline,
                summary = :summary
            WHERE
                profile_id = ' . $_GET['profile_id']
            );

        $updateQuery->execute([
            ':first_name' => htmlentities($_POST['first_name']),
            ':last_name' => htmlentities($_POST['last_name']),
            ':email' => htmlentities($_POST['email']),
            ':headline' => htmlentities($_POST['headline']),
            ':summary' => htmlentities($_POST['summary'])
        ]);

        $pdo->query('DELETE FROM position WHERE profile_id =' . $_GET['profile_id']);
        $pdo->query('DELETE FROM education WHERE profile_id =' . $_GET['profile_id']);

        for ($i = 0; $i < 9; $i++) {
            if (isset($_POST['pos-year' . $i])) {
                $queryPos = $pdo->prepare('INSERT INTO position (profile_id, pos_rank, year, description) VALUES (:profile_id, :pos_rank, :year, :description)');
                $queryPos->execute([
                    ':profile_id' => $_GET['profile_id'],
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
                    ':profile_id' => $_GET['profile_id'],
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
            $query = $pdo->query('SELECT * FROM profile WHERE profile_id =' . $_GET['profile_id'])->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="POST">
            <label for="first_name">First Name:</label> <input type="text" name="first_name" id="first_name" class="form-control" value="<?= $query['first_name'] ?>"><br>
            <label for="last_name">Last Name:</label> <input type="text" name="last_name" id="last_name" class="form-control" value="<?= $query['last_name'] ?>"><br>
            <label for="email">Email:</label> <input type="text" name="email" id="email" class="form-control" value="<?= $query['email'] ?>"><br>
            <label for="headline">Headline:</label> <input type="text" name="headline" id="headline" class="form-control" value="<?= $query['headline'] ?>"><br>
            <label for="summary">Summary:</label><br><textarea name="summary" id="summary" class="form-control" cols="70" rows="10"><?= $query['summary'] ?></textarea><br>
            <span style="display: inline-block; width: 80px">Education</span><button id="btn-add-education" class="btn btn-info">+</button><br><br>
            <div id="add-education">
            <?php
                $eduQuery = $pdo->query('SELECT institution.name, education.year
                    FROM institution
                    INNER JOIN education ON institution.institution_id = education.institution_id
                    INNER JOIN profile ON profile.profile_id = education.profile_id
                    WHERE profile.profile_id=' . $_GET['profile_id']
                );
                if ($eduQuery->rowCount() > 0) {
                    for ($i = 0; $row = $eduQuery->fetch(PDO::FETCH_ASSOC); $i++) {
                        echo('
                            <div>
                                <label>Year:
                                    <input type="text" class="edu-year form-control" value="' . $row['year'] . '">
                                </label> <button class="btn-remove btn btn-danger">X</button><br><br>
                                <label>School:
                                    <input type="text" class="school form-control" value="' . $row['name'] . '">
                                </label><br><br>
                            </div>
                        ');
                    }
                }
                
                ?>
            </div>
            <span style="display: inline-block; width: 80px">Position</span><button id="btn-add-position" class="btn btn-info">+</button><br><br>
            <div id="add-position">
            <?php
                $posQuery = $pdo->query('SELECT * FROM position WHERE profile_id =' . $_GET['profile_id'] . ' ORDER BY pos_rank');
                if ($posQuery->rowCount() > 0) {
                    for ($i = 0; $row = $posQuery->fetch(PDO::FETCH_ASSOC); $i++) {
                        echo('
                            <div>
                                <label>Year:
                                    <input type="text" class="pos-year form-control" value="' . $row['year'] . '" name="pos-year' . $i . '">
                                </label> <button class="btn-remove btn btn-danger">X</button><br>
                                <textarea class="desc form-control" name="desc' . $i . '">' . $row['description'] . '</textarea><br><br>
                            </div>
                        ');
                    }
                }
            ?>
            </div>
            <input type="submit" name="Save" value="Save" class="btn btn-primary">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-outline-secondary">
        </form>
    </div>
</body>
<script src="utils.js"></script>
<script>
    removeForm();
    $('.school').autocomplete({ source: "institutions.php" });
</script>
</html>