<?php
require_once 'config.php';

if(!estConnecte()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$erreur = '';
$succes = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = nettoyer($_POST['email']);
    $adresse = isset($_POST['adresse']) ? nettoyer($_POST['adresse']) : '';
    $description = isset($_POST['description']) ? nettoyer($_POST['description']) : '';

    $photo_nom = '';
    if($user_type == 'ouvrier' && isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $extensions_valides = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if(in_array($extension, $extensions_valides) && $_FILES['photo']['size'] <= 2 * 1024 * 1024) {
            $photo_nom = 'ouvrier_' . time() . '_' . uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_nom);
        }
    }
    
    try {
        if($user_type == 'agriculteur') {
            $sql = "UPDATE agriculteurs SET email = :email, adresse = :adresse WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email, ':adresse' => $adresse, ':id' => $user_id]);
        } else {
            if($photo_nom) {
                $sql = "UPDATE ouvriers SET email = :email, description = :description, photo = :photo WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':email' => $email, ':description' => $description, ':photo' => $photo_nom, ':id' => $user_id]);
            } else {
                $sql = "UPDATE ouvriers SET email = :email, description = :description WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':email' => $email, ':description' => $description, ':id' => $user_id]);
            }
        }
        $succes = "Profil mis à jour avec succès !";
    } catch(PDOException $e) {
        $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

if($user_type == 'agriculteur') {
    $sql = "SELECT * FROM agriculteurs WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
} else {
    $sql = "SELECT * FROM ouvriers WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil - Uber-Cueillette</title>
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
                    <?php if(estAgriculteur()): ?>
                        <li><a href="dashboard_agriculteur.php">Tableau de bord</a></li>
                    <?php else: ?>
                        <li><a href="dashboard_ouvrier.php">Tableau de bord</a></li>
                    <?php endif; ?>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>✏️ Modifier mon profil</h2>
            
            <?php if($erreur): ?>
                <div class="alert alert-error"><?= $erreur ?></div>
            <?php endif; ?>
            
            <?php if($succes): ?>
                <div class="alert alert-success"><?= $succes ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <?php if($user_type == 'agriculteur'): ?>
                    <div class="form-group">
                        <label>Adresse *</label>
                        <textarea name="adresse" required><?= htmlspecialchars($user['adresse']) ?></textarea>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Description (niveau éducatif, expérience, etc.)</label>
                        <textarea name="description"><?= htmlspecialchars($user['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Photo d'identité</label>
                        <?php if($user['photo'] && file_exists('uploads/' . $user['photo'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="uploads/<?= $user['photo'] ?>" width="100" height="100" style="border-radius: 50%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="photo" accept="image/*">
                        <small>Laissez vide pour conserver l'image actuelle</small>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="profil.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>