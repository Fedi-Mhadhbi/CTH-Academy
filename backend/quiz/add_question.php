<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "enseignant") {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$question = $data["question"] ?? "";
$a1 = $data["a1"] ?? "";
$a2 = $data["a2"] ?? "";
$a3 = $data["a3"] ?? "";
$a4 = $data["a4"] ?? "";
$correct = $data["correct"] ?? "";

$stmt = $pdo->prepare("INSERT INTO quizzes(question,answer1,answer2,answer3,answer4,correct_answer,created_by)
VALUES (?,?,?,?,?,?,?)");

$stmt->execute([$question,$a1,$a2,$a3,$a4,$correct,$_SESSION["user_id"]]);

echo json_encode(["status"=>"success","message"=>"Question added"]);