<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$jour = isset($_GET['jour']) ? $_GET['jour'] : '';
$heure = isset($_GET['heure']) ? $_GET['heure'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unite = mysqli_real_escape_string($conn, $_POST['unite']);
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $user_id = $_SESSION['user_id'];
    
    $date = date('Y-m-d', strtotime("next $jour"));
    $date_debut = date("Y-m-d H:i:s", strtotime("$date $heure"));
    $date_fin = date("Y-m-d H:i:s", strtotime("$date $heure +1 hour"));
    $jour_semaine = date('l', strtotime($date_debut));
    
    $stmt = $conn->prepare("INSERT INTO taches (utilisateur_id, unite, type_activite, titre, date_debut, date_fin, jour_semaine) VALUES (?, ?, 'Cours', ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $unite, $titre, $date_debut, $date_fin, $jour_semaine);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
    } else {
        header("Location: dashboard.php?error=1");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un cours - Emploi du temps</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card" style="margin: 50px auto; max-width: 500px;">
        <h2>➕ Ajouter un cours</h2>
        <p><strong>Jour:</strong> <?php echo htmlspecialchars($jour); ?> à <?php echo htmlspecialchars($heure); ?></p>
        
        <form method="POST">
            <input type="text" name="unite" placeholder="Unité d'enseignement" required>
            <input type="text" name="titre" placeholder="Titre du cours" required>
            <button type="submit">Ajouter le cours</button>
            <a href="dashboard.php" style="display: block; text-align: center; margin-top: 10px;">Annuler</a>
        </form>
    </div>
</body>
</html>