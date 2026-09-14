<?php
session_start();
header("Content-Type: application/json");

require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, nom, prenom, email, password, role, active FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

// Check if user is approved
if($user['active'] == 0){
    echo json_encode([
        "status" => "error",
        "message" => "Your account is waiting for admin approval"
    ]);
    exit;
}

// Verify password (hashed)
if (!password_verify($password, $user["password"])) {
    echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    exit;
}

// Set session
$_SESSION["id"] = $user["id"];
$_SESSION["role"] = $user["role"];
$_SESSION["nom"] = $user["nom"];
$_SESSION["prenom"] = $user["prenom"];

echo json_encode([
    "status" => "success",
    "role"   => $user["role"],
    "nom"    => $user["nom"],
    "prenom" => $user["prenom"]
]);