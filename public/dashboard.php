<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tasks_js = [];
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
    <!-- Formulaire d'ajout -->
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

    <!-- Liste des activités -->
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
                            <a href='edit_task.php?id=" . $row['id'] . "' class='edit-btn' title='Modifier'>✏️</a>
                            <a href='delete_task.php?id=" . $row['id'] . "' class='delete-btn' title='Supprimer' onclick='return confirm(\"Supprimer cette activité ?\")'>❌</a>
                          </td>";
                    echo "</tr>";
                    
                    $tasks_js[] = [
                        "unite" => $row['unite'],
                        "type" => $row['type_activite'],
                        "titre" => $row['titre'],
                        "date_debut" => $row['date_debut'],
                        "date_fin" => $row['date_fin']
                    ];
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Grille semaine -->
<h3 style="text-align: center; margin-top: 30px;">📅 Emploi du temps hebdomadaire</h3>
<div style="overflow-x: auto;">
    <table class="week-table">
        <thead>
            <tr>
                <th>Heure</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
                <th>Dimanche</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $jours = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        $start = strtotime("08:00");
        $end = strtotime("18:00");
        
        for ($time = $start; $time <= $end; $time += 7200) {
            $heure = date("H:i", $time);
            echo "<tr>";
            echo "<td class='time-slot'><strong>$heure</strong></td>";
            
            foreach ($jours as $jour) {
                echo "<td class='course-cell'>";
                
                $query = $conn->query("
                    SELECT * FROM taches 
                    WHERE utilisateur_id = $user_id 
                    AND type_activite = 'Cours'
                    AND DAYNAME(date_debut) = '$jour'
                    AND TIME(date_debut) = '$heure:00'
                ");
                
                if ($cours = $query->fetch_assoc()) {
                    echo "<div class='course-content'>";
                    echo "<strong>" . htmlspecialchars($cours['unite']) . "</strong><br>";
                    echo "<small>" . htmlspecialchars($cours['titre']) . "</small><br>";
                    echo "<div class='course-actions'>";
                    echo "<a href='edit_task.php?id=" . $cours['id'] . "' class='small-btn' title='Modifier'>✏️</a>";
                    echo "<a href='delete_task.php?id=" . $cours['id'] . "' class='small-btn' title='Supprimer' onclick='return confirm(\"Supprimer ce cours ?\")'>❌</a>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    $encodedJour = urlencode($jour);
                    echo "<a href='add_course.php?jour=$encodedJour&heure=$heure' class='add-course-link' title='Ajouter un cours'>➕</a>";
                }
                
                echo "</td>";
            }
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Cours du jour -->
<h3 style="text-align: center; margin-top: 40px;">📅 Cours du jour</h3>
<?php
$today = date('l');
$query = $conn->query("
    SELECT * FROM taches 
    WHERE utilisateur_id = $user_id 
    AND type_activite = 'Cours'
    AND DAYNAME(date_debut) = '$today'
    ORDER BY date_debut
");

if ($query->num_rows == 0) {
    echo "<p style='text-align: center; color: #666;'>Aucun cours programmé aujourd'hui</p>";
}

while ($cours = $query->fetch_assoc()) {
    echo "<div class='today-course'>
            <div class='course-time'>⏰ " . date('H:i', strtotime($cours['date_debut'])) . " - " . date('H:i', strtotime($cours['date_fin'])) . "</div>
            <div class='course-unite'>📘 " . htmlspecialchars($cours['unite']) . "</div>
            <div class='course-title'>📝 " . htmlspecialchars($cours['titre']) . "</div>
            <div class='course-actions' style='margin-top: 10px;'>
                <a href='edit_task.php?id=" . $cours['id'] . "' class='edit-btn'>✏️ Modifier</a>
                <a href='delete_task.php?id=" . $cours['id'] . "' class='delete-btn' onclick='return confirm(\"Supprimer ce cours ?\")'>❌ Supprimer</a>
            </div>
          </div>";
}
?>

<!-- Déconnexion -->
<div class="logout-container">
    <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
</div>

<audio id="alarmSound" src="alarm.mp3"></audio>

<script>
let tasks = <?php echo json_encode($tasks_js); ?>;
let alreadyNotified = [];

function checkAlarms() {
    let now = new Date();
    
    tasks.forEach((task, index) => {
        if (!task.date_debut) return;
        
        let taskTime = new Date(task.date_debut);
        let alertTime = new Date(taskTime.getTime() - 15 * 60000);
        
        if (now >= alertTime && now <= taskTime && !alreadyNotified.includes(index)) {
            alreadyNotified.push(index);
            
            let message = "🔔 RAPPEL D'ACTIVITÉ\n\n" +
                         "📘 Unité: " + task.unite + "\n" +
                         "📌 Type: " + task.type + "\n" +
                         "📝 Titre: " + task.titre + "\n" +
                         "⏰ Début: " + new Date(task.date_debut).toLocaleString() + "\n" +
                         "⏳ Fin: " + new Date(task.date_fin).toLocaleString();
            
            alert(message);
            
            setTimeout(() => {
                let sound = document.getElementById("alarmSound");
                sound.play().catch(e => console.log("Audio non disponible"));
            }, 5000);
        }
    });
}

setInterval(checkAlarms, 60000);
</script>

</body>
</html>