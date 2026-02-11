<?php
header("Content-Type: application/json");

require_once 'dbh.inc.php';

$db = new database();
$conn = $db->connection(); 

$sql = "SELECT id, name, email FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
    "status" => true,
    "data" => $data
]);
// echo json_encode([
//     "status" => true,
//     "data" => $data
// ]);

$conn = null; 
