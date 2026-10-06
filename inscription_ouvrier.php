<?php
require_once 'config.php';

if(estConnecte()) {
    redirect('index.php');
}

$erreur = '';
$succes = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = nettoyer($_POST['nom']);
    $prenom = nettoyer($_POST['prenom']);
    $cin = nettoyer($_POST['cin']);
    $email = nettoyer($_POST['email']);
    $description = nettoyer($_POST['description']);
    $pseudo = nettoyer($_POST['pseudo']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmation_mdp = $_POST['confirmation_mdp'];
    
    $photo_nom = '';
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $extensions_valides = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if(in_array($extension, $extensions_valides) && $_FILES['photo']['size'] <= 2 * 1024 * 1024) {
            $photo_nom = 'ouvrier_' . time() . '_' . uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_nom);
        }
    }
    
    $erreurs = [];
    
    if(!preg_match('/^[A-Za-zÀ-ÿ\s]+$/', $nom)) {
        $erreurs[] = "Le nom ne doit contenir que des lettres";
    }
    if(!preg_match('/^[A-Za-zÀ-ÿ\s]+$/', $prenom)) {
        $erreurs[] = "Le prénom ne doit contenir que des lettres";
    }
    if(!preg_match('/^[0-9]{8}$/', $cin)) {
        $erreurs[] = "Le CIN doit contenir exactement 8 chiffres";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide";
    }
    if(!preg_match('/^[A-Za-z]+$/', $pseudo)) {
        $erreurs[] = "Le pseudo ne doit contenir que des lettres";
    }
    if(!preg_match('/^[A-Za-z0-9]{7,}[$#]$/', $mot_de_passe)) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères et se terminer par $ ou #";
    }
    if($mot_de_passe !== $confirmation_mdp) {
        $erreurs[] = "Les mots de passe ne correspondent pas";
    }
    
    if(empty($erreurs)) {
        try {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $sql = "INSERT INTO ouvriers (nom, prenom, cin, email, description, photo, pseudo, mot_de_passe) 
                    VALUES (:nom, :prenom, :cin, :email, :description, :photo, :pseudo, :mot_de_passe)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':cin' => $cin,
                ':email' => $email,
                ':description' => $description,
                ':photo' => $photo_nom,
                ':pseudo' => $pseudo,
                ':mot_de_passe' => $mot_de_passe_hash
            ]);
            $succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } catch(PDOException $e) {
            if(strpos($e->getMessage(), 'cin') !== false) {
                $erreur = "Ce CIN est déjà utilisé";
            } elseif(strpos($e->getMessage(), 'email') !== false) {
                $erreur = "Cet email est déjà utilisé";
            } elseif(strpos($e->getMessage(), 'pseudo') !== false) {
                $erreur = "Ce pseudo est déjà utilisé";
            } else {
                $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    } else {
        $erreur = implode('<br>', $erreurs);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Ouvrier - Uber-Cueillette</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
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
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="inscription_agriculteur.php">Inscription Agriculteur</a></li>
                    <li><a href="inscription_ouvrier.php">Inscription Ouvrier</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>👨‍🌾 Inscription Ouvrier Agricole</h2>
            
            <?php if($erreur): ?>
                <div class="alert alert-error"><?= $erreur ?></div>
            <?php endif; ?>
            
            <?php if($succes): ?>
                <div class="alert alert-success"><?= $succes ?></div>
                <p><a href="login.php" class="btn btn-primary">Se connecter</a></p>
            <?php else: ?>
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validerInscriptionOuvrier()">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Nom *</label>
                                <input type="text" name="nom" id="nom" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Prénom *</label>
                                <input type="text" name="prenom" id="prenom" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>CIN (8 chiffres) *</label>
                                <input type="text" name="cin" id="cin" maxlength="8" pattern="[0-9]{8}" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" id="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Photo d'identité (optionnel)</label>
                        <input type="file" name="photo" id="photo" accept="image/*">
                        <small>Format JPG, PNG, GIF - Max 2 Mo</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Description (niveau éducatif, expérience, etc.) *</label>
                        <textarea name="description" id="description" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Pseudo (lettres uniquement) *</label>
                                <input type="text" name="pseudo" id="pseudo" pattern="[A-Za-z]+" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Mot de passe *</label>
                                <input type="password" name="mot_de_passe" id="mot_de_passe" required>
                                <small>Au moins 8 caractères (lettres/chiffres) et se termine par $ ou #</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmation mot de passe *</label>
                        <input type="password" name="confirmation_mdp" id="confirmation_mdp" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                    <p style="margin-top: 1rem;">Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>