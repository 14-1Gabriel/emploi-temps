<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer la tâche
$result = $conn->query("SELECT * FROM taches WHERE id = $id AND utilisateur_id = " . $_SESSION['user_id']);
$task = $result->fetch_assoc();

if (!$task) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unite = mysqli_real_escape_string($conn, $_POST['unite']);
    $type = mysqli_real_escape_string($conn, $_POST['type_activite']);
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $jour_semaine = date('l', strtotime($date_debut));
    
    $stmt = $conn->prepare("UPDATE taches SET unite=?, type_activite=?, titre=?, date_debut=?, date_fin=?, jour_semaine=? WHERE id=? AND utilisateur_id=?");
    $stmt->bind_param("ssssssii", $unite, $type, $titre, $date_debut, $date_fin, $jour_semaine, $id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=2");
    } else {
        header("Location: dashboard.php?error=2");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier - Emploi du temps</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card" style="margin: 50px auto; max-width: 500px;">
        <h2>✏️ Modifier l'activité</h2>
        
        <form method="POST">
            <input type="text" name="unite" value="<?php echo htmlspecialchars($task['unite']); ?>" required>
            
            <select name="type_activite" required>
                <option value="Cours" <?php if($task['type_activite'] == 'Cours') echo 'selected'; ?>>Cours</option>
                <option value="TD" <?php if($task['type_activite'] == 'TD') echo 'selected'; ?>>TD</option>
                <option value="TP" <?php if($task['type_activite'] == 'TP') echo 'selected'; ?>>TP</option>
                <option value="Lecture" <?php if($task['type_activite'] == 'Lecture') echo 'selected'; ?>>Lecture</option>
            </select>
            
            <input type="text" name="titre" value="<?php echo htmlspecialchars($task['titre']); ?>" required>
            
            <input type="datetime-local" name="date_debut" value="<?php echo date('Y-m-d\TH:i', strtotime($task['date_debut'])); ?>" required>
            
            <input type="datetime-local" name="date_fin" value="<?php echo date('Y-m-d\TH:i', strtotime($task['date_fin'])); ?>" required>
            
            <button type="submit">Mettre à jour</button>
            <a href="dashboard.php" style="display: block; text-align: center; margin-top: 10px;">Annuler</a>
        </form>
    </div>
</body>
</html>