<?php
session_start();

// Correct path to DB
require_once "../config/db.php"; // Adjust path: this assumes this file is in backend/admin/

// Admin details
$nom = "Super";
$prenom = "Admin";
$email = "admin123@g.com";
$password_plain = "admin@123"; // The password you want to use
$role = "admin";

// Check if admin already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Hash the password
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    // Insert new admin
    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$nom, $prenom, $email, $password_hashed, $role]);

    if ($result) {
        $user = [
            'id' => $pdo->lastInsertId(),
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role' => $role,
            'password' => $password_hashed
        ];
    } else {
        die("Error creating admin.");
    }
}

// ✅ Log the admin in
$_SESSION['id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['nom'] = $user['nom'];
$_SESSION['prenom'] = $user['prenom'];

// Redirect to dashboard
header("Location: admin_dashboard.php");
exit;
?>