<?php
session_start();

// Configuration base de données (variables d'environnement Vercel)
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'emploi_temps';

// Connexion à la base de données
$conn = new mysqli($host, $user, $password, $database);

// Vérification connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Définir le charset UTF-8
$conn->set_charset("utf8");
?>