<?php
session_start();
require_once "../config/db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
  echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
  exit;
}

$id = $_POST['id'] ?? null;

$stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

echo json_encode(["status"=>"success"]);