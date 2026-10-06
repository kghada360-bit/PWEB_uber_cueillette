<?php
require_once 'config.php';

if(!estConnecte()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if($user_type == 'agriculteur') {
    $sql = "SELECT * FROM agriculteurs WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
    
    $sql_stats = "SELECT COUNT(*) as nb_offres, SUM(nb_ouvriers_acceptes) as total_ouvriers 
                  FROM offres WHERE agriculteur_id = :id";
    $stmt = $conn->prepare($sql_stats);
    $stmt->execute([':id' => $user_id]);
    $stats = $stmt->fetch();
} else {
    $sql = "SELECT * FROM ouvriers WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    $sql_stats = "SELECT COUNT(*) as nb_candidatures, 
                  SUM(CASE WHEN statut = 'acceptee' THEN 1 ELSE 0 END) as nb_acceptees,
                  AVG(note) as note_moyenne
                  FROM candidatures WHERE ouvrier_id = :id";
    $stmt = $conn->prepare($sql_stats);
    $stmt->execute([':id' => $user_id]);
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - Uber-Cueillette</title>
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
                        <li><a href="ajouter_offre.php">Nouvelle offre</a></li>
                        <li><a href="mes_offres.php">Mes offres</a></li>
                    <?php else: ?>
                        <li><a href="dashboard_ouvrier.php">Tableau de bord</a></li>
                        <li><a href="liste_offres.php">Offres disponibles</a></li>
                        <li><a href="mes_candidatures.php">Mes candidatures</a></li>
                        <li><a href="mes_chantiers.php">Mes chantiers</a></li>
                    <?php endif; ?>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>👤 Mon profil</h2>
            
            <div class="row">
                <?php if($user_type == 'ouvrier' && $user['photo'] && file_exists('uploads/' . $user['photo'])): ?>
                    <div class="col" style="text-align: center;">
                        <img src="uploads/<?= $user['photo'] ?>" class="profile-photo">
                    </div>
                <?php endif; ?>
                <div class="col">
                    <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
                    <p><strong>CIN :</strong> <?= htmlspecialchars($user['cin']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
                    <?php if($user_type == 'agriculteur'): ?>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($user['adresse']) ?></p>
                        <p><strong>Date d'inscription :</strong> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
                    <?php else: ?>
                        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($user['description'])) ?></p>
                        <p><strong>Date d'inscription :</strong> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="margin-top: 2rem;">
                <a href="modifier_profil.php" class="btn btn-primary">Modifier mon profil</a>
                <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
            </div>
        </div>

        <div class="card">
            <h2>📊 Mes statistiques</h2>
            <?php if($user_type == 'agriculteur'): ?>
                <div class="row">
                    <div class="col">
                        <p><strong>Nombre d'offres publiées :</strong> <?= $stats['nb_offres'] ?></p>
                    </div>
                    <div class="col">
                        <p><strong>Total ouvriers recrutés :</strong> <?= $stats['total_ouvriers'] ?? 0 ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col">
                        <p><strong>Candidatures envoyées :</strong> <?= $stats['nb_candidatures'] ?></p>
                    </div>
                    <div class="col">
                        <p><strong>Candidatures acceptées :</strong> <?= $stats['nb_acceptees'] ?></p>
                    </div>
                    <div class="col">
                        <p><strong>Note moyenne :</strong> 
                            <?php if($stats['note_moyenne']): ?>
                                <span class="rating">⭐ <?= number_format($stats['note_moyenne'], 1) ?>/10</span>
                            <?php else: ?>
                                Pas encore noté
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>