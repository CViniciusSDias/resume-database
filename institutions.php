<?php

include_once 'pdo.php';

header('Content-Type: application/json');

$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$term = filter_input(INPUT_REQUEST, 'term');;
$stmt->execute(array( ':prefix' => $term . "%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));