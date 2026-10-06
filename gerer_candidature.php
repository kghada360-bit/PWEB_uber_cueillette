<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$candidature_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$offre_id = isset($_GET['offre']) ? intval($_GET['offre']) : 0;

$sql = "SELECT c.*, o.agriculteur_id, o.nb_ouvriers_demandes, o.nb_ouvriers_acceptes 
        FROM candidatures c 
        JOIN offres o ON c.offre_id = o.id 
        WHERE c.id = :id AND o.agriculteur_id = :agriculteur_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $candidature_id, ':agriculteur_id' => $_SESSION['user_id']]);
$candidature = $stmt->fetch();

if(!$candidature) {
    redirect('mes_offres.php');
}

if($action == 'accepter') {
    if($candidature['nb_ouvriers_acceptes'] < $candidature['nb_ouvriers_demandes']) {
       
        $sql = "UPDATE candidatures SET statut = 'acceptee' WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $candidature_id]);

        $sql = "UPDATE offres SET nb_ouvriers_acceptes = nb_ouvriers_acceptes + 1 WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $offre_id]);
        
        $sql = "UPDATE offres SET statut = 'cloturee' 
                WHERE id = :id AND nb_ouvriers_acceptes >= nb_ouvriers_demandes";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $offre_id]);
    }
} elseif($action == 'refuser') {
    $sql = "UPDATE candidatures SET statut = 'refusee' WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $candidature_id]);
}

redirect('voir_postulants.php?id=' . $offre_id);
?>