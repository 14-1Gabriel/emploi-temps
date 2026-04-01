<?php
require_once __DIR__ . '/../includes/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Emploi du temps</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h2>🔐 Connexion</h2>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        
        <p style="margin-top: 15px;">
            Pas encore de compte ? <a href="register.php">Créer un compte</a>
        </p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];
            
            $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['mot_de_passe'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nom'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "<p style='color: #e74c3c; margin-top: 15px;'>❌ Mot de passe incorrect</p>";
                }
            } else {
                echo "<p style='color: #e74c3c; margin-top: 15px;'>❌ Email non trouvé</p>";
            }
        }
        ?>
    </div>
</body>
</html>