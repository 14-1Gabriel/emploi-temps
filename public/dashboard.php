<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Emploi du temps</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    📅 Mon emploi du temps - Bonjour <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?>
</header>

<div class="container">
    <div class="card">
        <h3>➕ Ajouter une activité</h3>
        <form action="add_task.php" method="POST">
            <input type="text" name="unite" placeholder="Unité d'enseignement" required>
            <select name="type_activite" required>
                <option value="">Type d'activité</option>
                <option value="Cours">Cours</option>
                <option value="TD">TD</option>
                <option value="TP">TP</option>
                <option value="Lecture">Lecture</option>
            </select>
            <input type="text" name="titre" placeholder="Titre de l'activité" required>
            <input type="datetime-local" name="date_debut" required>
            <input type="datetime-local" name="date_fin" required>
            <button type="submit">Ajouter l'activité</button>
        </form>
    </div>

    <div class="card table-container">
        <h3>📊 Mes activités</h3>
        <div style="overflow-x: auto;">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Unité</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM taches WHERE utilisateur_id = $user_id ORDER BY date_debut DESC");
                
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='6' style='text-align: center;'>Aucune activité pour le moment</td></tr>";
                }
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['unite']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type_activite']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['titre']) . "</td>";
                    echo "<td>" . date('d/m/Y H:i', strtotime($row['date_debut'])) . "</td>";
                    echo "<td>" . date('d/m/Y H:i', strtotime($row['date_fin'])) . "</td>";
                    echo "<td class='actions'>
                            <a href='edit_task.php?id=" . $row['id'] . "' class='edit-btn'>✏️</a>
                            <a href='delete_task.php?id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Supprimer ?\")'>❌</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
             </table>
        </div>
    </div>
</div>

<div class="logout-container">
    <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
</div>

</body>
</html>