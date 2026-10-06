<?php
require_once 'config.php';

if(estConnecte()) {
    redirect('index.php');
}

$erreur = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = nettoyer($_POST['pseudo']);
    $mot_de_passe = $_POST['mot_de_passe'];
 
    $sql = "SELECT id, pseudo, mot_de_passe, 'agriculteur' as type FROM agriculteurs WHERE pseudo = :pseudo 
            UNION 
            SELECT id, pseudo, mot_de_passe, 'ouvrier' as type FROM ouvriers WHERE pseudo = :pseudo";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pseudo' => $pseudo]);
    $user = $stmt->fetch();
    
    if($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_pseudo'] = $user['pseudo'];
        $_SESSION['user_type'] = $user['type'];
        
        if($user['type'] == 'agriculteur') {
            redirect('dashboard_agriculteur.php');
        } else {
            redirect('dashboard_ouvrier.php');
        }
    } else {
        $erreur = "Pseudo ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Uber-Cueillette</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>🌾 Uber-Cueillette</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="inscription_agriculteur.php">Inscription Agriculteur</a></li>
                    <li><a href="inscription_ouvrier.php">Inscription Ouvrier</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2>🔐 Connexion</h2>
            
            <?php if($erreur): ?>
                <div class="alert alert-error"><?= $erreur ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Pseudo</label>
                    <input type="text" name="pseudo" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Se connecter</button>
                <p style="margin-top: 1rem;">
                    Pas encore inscrit ? 
                    <a href="inscription_agriculteur.php">Inscription Agriculteur</a> ou 
                    <a href="inscription_ouvrier.php">Inscription Ouvrier</a>
                </p>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>