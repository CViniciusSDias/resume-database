<?php
    require_once 'utils.php';
    require_once 'pdo.php';

    session_start();
    cancelCheck();
    commonCheck($pdo);

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
        <h1>Profile Information</h1>
        <form method="POST">
            <strong>First name: </strong><?= $query['first_name'] ?><br>
            <strong>Last name: </strong><?= $query['last_name'] ?><br>
            <strong>Email: </strong><?= $query['email'] ?><br>
            <strong>Headline: </strong><?= $query['headline'] ?><br>
            <strong>Summary: </strong><?= $query['summary'] ?><br>
            <?php
                $eduQuery = $pdo->query('SELECT institution.name, education.year
                    FROM institution
                    INNER JOIN education ON institution.institution_id = education.institution_id
                    INNER JOIN profile ON profile.profile_id = education.profile_id
                    WHERE profile.profile_id=' . $_GET['profile_id']
                    );
                if ($eduQuery->rowCount() > 0) {
                    echo('
                        <strong>Education: </strong>
                        <ul>
                    ');
                    while ($row = $eduQuery->fetch(PDO::FETCH_ASSOC)) {
                        echo('<li>' . $row['year'] . ': ' . $row['name'] . '</li>');
                    }
                    echo('</ul>');
                }
                $posQuery = $pdo->query('SELECT * FROM position WHERE profile_id =' . $_GET['profile_id'] . ' ORDER BY pos_rank');
                if ($posQuery->rowCount() > 0) {
                    echo('
                        <strong>Positions: </strong>
                        <ul>
                    ');
                    while ($row = $posQuery->fetch(PDO::FETCH_ASSOC)) {
                        echo('<li>' . $row['year'] . ': ' . $row['description'] . '</li>');
                    }
                    echo('</ul>');
                }
            ?>
            <br><input type="submit" name="cancel" value="Back" class="btn btn-outline-secondary">
        </form>
    </div>
</body>
</html>