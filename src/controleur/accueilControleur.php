<?php
function accueilControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $produitModel = new Produit($db);
    $type = $_GET['type'] ?? null;

    if ($type) {
        $liste = $produitModel->selectByTypeNom($type); // À créer dans Produit.php
    } else {
        $liste = $produitModel->select();
    }

    $data = [
        'liste' => $liste,
        'type'  => $type 
    ];

    if (isset($_SESSION['login'])) {
        $data['session'] = $_SESSION;
    }

    echo $twig->render('accueil.html.twig', $data);
}

