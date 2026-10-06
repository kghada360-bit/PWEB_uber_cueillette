<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$offre_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql_check = "SELECT * FROM offres WHERE id = :id AND agriculteur_id = :agriculteur_id";
$stmt = $conn->prepare($sql_check);
$stmt->execute([':id' => $offre_id, ':agriculteur_id' => $_SESSION['user_id']]);
$offre = $stmt->fetch();

if(!$offre) {
    die("Offre non trouvée ou vous n'avez pas les droits.");
}

$succes = '';
$erreur = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidature_id = intval($_POST['candidature_id']);
    $note = floatval($_POST['note']);
    $commentaire = isset($_POST['commentaire']) ? nettoyer($_POST['commentaire']) : '';
    $remuneration = floatval($_POST['remuneration']);
    
    if($note >= 0 && $note <= 10) {
        $sql = "UPDATE candidatures SET note = :note, commentaire = :commentaire, remuneration_totale = :remuneration 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':note' => $note,
            ':commentaire' => $commentaire,
            ':remuneration' => $remuneration,
            ':id' => $candidature_id
        ]);
        
        $sql = "UPDATE ouvriers o 
                SET o.note_moyenne = (SELECT AVG(note) FROM candidatures WHERE ouvrier_id = o.id AND note IS NOT NULL)
                WHERE o.id = (SELECT ouvrier_id FROM candidatures WHERE id = :id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $candidature_id]);
        
        $succes = "Note attribuée avec succès !";
    } else {
        $erreur = "La note doit être comprise entre 0 et 10";
    }
}

$sql = "SELECT c.id as candidature_id, c.note, c.commentaire, c.remuneration_totale,
        o.nom, o.prenom, o.photo,
        DATEDIFF(ofr.date_fin, ofr.date_debut) + 1 as nb_jours
        FROM candidatures c 
        JOIN ouvriers o ON c.ouvrier_id = o.id 
        JOIN offres ofr ON c.offre_id = ofr.id 
        WHERE c.offre_id = :offre_id AND c.statut = 'acceptee'
        ORDER BY o.nom";
$stmt = $conn->prepare($sql);
$stmt->execute([':offre_id' => $offre_id]);
$ouvriers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noter les ouvriers - Uber-Cueillette</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f0f4f8; }
        .noter-container { max-width: 1000px; margin: 0 auto; padding: 100px 2rem 2rem; }
        .card-noter { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header { background: #0a2b4e; padding: 1rem 1.5rem; }
        .card-header h2 { color: white; margin: 0; }
        .card-body { padding: 1.5rem; }
        .ouvrier-item { background: #f8fafc; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; }
        .btn-noter { background: #7a1f1f; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; }
        .btn-noter:hover { background: #0a2b4e; }
        .note-deja { background: #d4af37; padding: 0.5rem; border-radius: 8px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo"><h1>🌾 Uber-<span>Cueillette</span></h1></div>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="dashboard_agriculteur.php">Dashboard</a></li>
                    <li><a href="mes_offres.php">Mes offres</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="noter-container">
        <div class="card-noter">
            <div class="card-header">
                <h2>⭐ Noter les ouvriers</h2>
            </div>
            <div class="card-body">
                <p><strong>Offre :</strong> <?= htmlspecialchars($offre['type_fruit']) ?> - <?= htmlspecialchars($offre['gouvernorat']) ?></p>
                
                <?php if($succes): ?>
                    <div class="alert alert-success"><?= $succes ?></div>
                    <a href="mes_offres.php" class="btn btn-primary">Retour à mes offres</a>
                <?php elseif($erreur): ?>
                    <div class="alert alert-error"><?= $erreur ?></div>
                <?php endif; ?>
                
                <?php if(count($ouvriers) > 0 && !$succes): ?>
                    <?php foreach($ouvriers as $ouvrier): ?>
                        <div class="ouvrier-item">
                            <h3><?= htmlspecialchars($ouvrier['prenom'] . ' ' . $ouvrier['nom']) ?></h3>
                            <p>Jours : <?= $ouvrier['nb_jours'] ?> jours | Estimation : <?= $offre['prix_journee'] * $ouvrier['nb_jours'] ?> DT</p>
                            
                            <?php if($ouvrier['note'] !== null): ?>
                                <div class="note-deja">
                                    ✅ Déjà noté | Note: <?= $ouvrier['note'] ?>/10 | Gain: <?= $ouvrier['remuneration_totale'] ?> DT
                                </div>
                            <?php else: ?>
                                <form method="POST" style="margin-top: 1rem;">
                                    <input type="hidden" name="candidature_id" value="<?= $ouvrier['candidature_id'] ?>">
                                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <input type="number" name="note" step="0.5" min="0" max="10" placeholder="Note (0-10)" required style="flex:1;">
                                        <input type="number" name="remuneration" step="0.5" placeholder="Rémunération totale DT" value="<?= $offre['prix_journee'] * $ouvrier['nb_jours'] ?>" required style="flex:1;">
                                    </div>
                                    <textarea name="commentaire" rows="2" placeholder="Commentaire sur le travail..." style="width:100%; margin-top:0.5rem;"></textarea>
                                    <button type="submit" class="btn-noter" style="margin-top:0.5rem;">Attribuer la note</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php elseif(!$succes): ?>
                    <p>Aucun ouvrier accepté pour cette offre.</p>
                    <a href="mes_offres.php" class="btn btn-primary">Retour</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>