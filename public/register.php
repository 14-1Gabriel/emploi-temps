<?php
require_once __DIR__ . '/../includes/config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Vérifier si l'email existe déjà
    $check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $error = "❌ Cet email est déjà utilisé";
    } else {
        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nom, $email, $password);
        
        if ($stmt->execute()) {
            $success = "✅ Compte créé avec succès ! Redirection vers la connexion...";
            header("refresh:2;url=login.php");
        } else {
            $error = "❌ Erreur lors de l'inscription";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Emploi du temps</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h2>📝 Inscription</h2>
        
        <?php if ($error): ?>
            <p style="color: #e74c3c;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p style="color: #27ae60;"><?php echo $success; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="nom" placeholder="Nom complet" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe (min. 6 caractères)" minlength="6" required>
            <button type="submit">S'inscrire</button>
        </form>
        
        <p style="margin-top: 15px;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</body>
</html>