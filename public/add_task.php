<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unite = mysqli_real_escape_string($conn, $_POST['unite']);
    $type = mysqli_real_escape_string($conn, $_POST['type_activite']);
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $user_id = $_SESSION['user_id'];
    $jour_semaine = date('l', strtotime($date_debut));
    
    $stmt = $conn->prepare("INSERT INTO taches (utilisateur_id, unite, type_activite, titre, date_debut, date_fin, jour_semaine) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $unite, $type, $titre, $date_debut, $date_fin, $jour_semaine);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
    } else {
        header("Location: dashboard.php?error=1");
    }
    exit();
}
?>