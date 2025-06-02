<?php
function commandeControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Accès réservé aux admins
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
        header('Location: index.php?page=connexion');
        exit;
    }

    $commandeModel    = new Commande($db);
    $composerModel    = new Composer($db);
    $utilisateurModel = new Utilisateur($db);

    // Valider une commande
    if (isset($_GET['valider']) && is_numeric($_GET['valider'])) {
        $commandeModel->updateEtat($_GET['valider'], 2); // 2 = Traitée
        header('Location: index.php?page=commande');
        exit;
    }

    // Supprimer une commande
    if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
        $commandeModel->delete($_GET['supprimer']);
        header('Location: index.php?page=commande');
        exit;
    }

    $filtre = $_GET['filtre'] ?? null;

    if ($filtre && in_array($filtre, [1, 2])) {
       $sql = "
            SELECT c.*, e.libelle AS etatLibelle, u.nom, u.prenom
            FROM commande c
            JOIN etat e ON c.idEtat = e.id
            JOIN utilisateur u ON c.idUtilisateur = u.id
            WHERE c.idEtat = :etat
            ORDER BY c.dateCommande DESC
            ";
$stmt = $db->prepare($sql);
        $stmt->execute([':etat' => $filtre]);
        $commandes = $stmt->fetchAll();
    } else {
        $commandes = $commandeModel->selectAll();
    }
    

    foreach ($commandes as &$cmd) {
    $cmd['produits'] = $composerModel->selectByCommandeId($cmd['id']);
    $util = $utilisateurModel->selectById($cmd['idUtilisateur']);
    if ($util && is_array($util)) {
        $cmd['client'] = $util['prenom'] . ' ' . $util['nom'];
    } else {
        $cmd['client'] = "Utilisateur inconnu";
    }
}


    echo $twig->render('commande.html.twig', [
        'commandes' => $commandes,
        'filtre'    => $filtre
    ]);
    
}
