<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uber_cueillette";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function estConnecte() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function estAgriculteur() {
    return estConnecte() && $_SESSION['user_type'] == 'agriculteur';
}

function estOuvrier() {
    return estConnecte() && $_SESSION['user_type'] == 'ouvrier';
}

function redirect($page) {
    header("Location: $page");
    exit();
}

function nettoyer($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>