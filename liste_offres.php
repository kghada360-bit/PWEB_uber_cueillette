<?php
require_once 'config.php';

if(!estOuvrier()) {
    redirect('login.php');
}

$sql = "SELECT o.*, a.nom, a.prenom 
        FROM offres o 
        JOIN agriculteurs a ON o.agriculteur_id = a.id 
        WHERE o.statut = 'ouverte' 
        AND o.date_limite_postulation >= CURDATE()
        AND o.nb_ouvriers_acceptes < o.nb_ouvriers_demandes";

$params = [];

// Filtres
if(isset($_GET['filter_fruit']) && !empty($_GET['filter_fruit'])) {
    $sql .= " AND o.type_fruit = :fruit";
    $params[':fruit'] = $_GET['filter_fruit'];
}
if(isset($_GET['filter_gouv']) && !empty($_GET['filter_gouv'])) {
    $sql .= " AND o.gouvernorat = :gouv";
    $params[':gouv'] = $_GET['filter_gouv'];
}

// Tri
$order = isset($_GET['order']) ? $_GET['order'] : '';
if($order == 'prix_asc') {
    $sql .= " ORDER BY o.prix_journee ASC";
} elseif($order == 'prix_desc') {
    $sql .= " ORDER BY o.prix_journee DESC";
} else {
    $sql .= " ORDER BY o.date_creation DESC";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$offres = $stmt->fetchAll();

$types_fruits = ['Olives', 'Agrumes', 'Tomates', 'Pommes', 'Poires', 'Pêches', 'Abricots', 'Raisins', 'Fraises', 'Figues'];
$gouvernorats = ['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan', 'Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Sousse', 'Monastir', 'Mahdia', 'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid', 'Gabès', 'Médenine', 'Tataouine', 'Gafsa', 'Tozeur', 'Kébili'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres disponibles - Uber-Cueillette</title>
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
                    <li><a href="dashboard_ouvrier.php">Tableau de bord</a></li>
                    <li><a href="liste_offres.php">Offres disponibles</a></li>
                    <li><a href="mes_candidatures.php">Mes candidatures</a></li>
                    <li><a href="mes_chantiers.php">Mes chantiers</a></li>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>📋 Offres de récolte disponibles</h2>
            
            <!-- Filtres -->
            <form method="GET" action="" class="filters">
                <div class="filter-group">
                    <label>Filtrer par fruit</label>
                    <select name="filter_fruit" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach($types_fruits as $fruit): ?>
                            <option value="<?= $fruit ?>" <?= isset($_GET['filter_fruit']) && $_GET['filter_fruit'] == $fruit ? 'selected' : '' ?>><?= $fruit ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Filtrer par gouvernorat</label>
                    <select name="filter_gouv" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach($gouvernorats as $gouv): ?>
                            <option value="<?= $gouv ?>" <?= isset($_GET['filter_gouv']) && $_GET['filter_gouv'] == $gouv ? 'selected' : '' ?>><?= $gouv ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Trier par prix</label>
                    <select name="order" onchange="this.form.submit()">
                        <option value="">Par défaut</option>
                        <option value="prix_asc" <?= isset($_GET['order']) && $_GET['order'] == 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                        <option value="prix_desc" <?= isset($_GET['order']) && $_GET['order'] == 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="liste_offres.php" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
            
            <?php if(count($offres) > 0): ?>
                <div class="table-container">
                    <table class="table" id="offres-table">
                        <thead>
                            <tr>
                                <th>Agriculteur</th>
                                <th data-fruit>Type de fruit</th>
                                <th data-gouv>Gouvernorat</th>
                                <th>Adresse</th>
                                <th>Période</th>
                                <th data-prix>Prix/jour</th>
                                <th>Places</th>
                                <th>Date limite</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($offres as $offre): ?>
                                <tr data-fruit="<?= htmlspecialchars($offre['type_fruit']) ?>" data-gouv="<?= htmlspecialchars($offre['gouvernorat']) ?>" data-prix="<?= $offre['prix_journee'] ?>">
                                    <td><?= htmlspecialchars($offre['prenom'] . ' ' . $offre['nom']) ?></td>
                                    <td><?= htmlspecialchars($offre['type_fruit']) ?></td>
                                    <td><?= htmlspecialchars($offre['gouvernorat']) ?></td>
                                    <td><?= htmlspecialchars($offre['adresse_recolte']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($offre['date_debut'])) ?> - <?= date('d/m/Y', strtotime($offre['date_fin'])) ?></td>
                                    <td><?= $offre['prix_journee'] ?> DT</td>
                                    <td><?= $offre['nb_ouvriers_acceptes'] ?>/<?= $offre['nb_ouvriers_demandes'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($offre['date_limite_postulation'])) ?></td>
                                    <td>
                                        <a href="postuler.php?id=<?= $offre['id'] ?>" class="btn btn-success btn-sm" onclick="return confirmerAction('Voulez-vous postuler à cette offre ?')">Postuler</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>