<?php
session_start();

// Configuration base de données (à modifier avec vos identifiants)
$host = getenv('DB_HOST') ?: 'sql5.freesqldatabase.com';
$user = getenv('DB_USER') ?: 'sql5821975';
$password = getenv('DB_PASSWORD') ?: 'djsViJSAQh';
$database = getenv('DB_NAME') ?: 'sql5821975';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>