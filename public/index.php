<?php
session_start();

if (isset($_GET['suggestion'])) {
    require_once '../config/connexion.php';
    require_once '../src/modele/_classes.php';

    $db = connect($config);
    $produitModel = new Produit($db);

    $terme = trim($_GET['suggestion']);
    $resultats = $produitModel->recherche($terme);

    $suggestions = [];
    foreach ($resultats as $p) {
        $suggestions[] = ['id' => $p['id'], 'nom' => $p['nom']];
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}


require_once '../src/controleur/_controleurs.php';
require_once '../lib/vendor/autoload.php';
require_once '../config/parametres.php';
require_once '../config/connexion.php';
require_once '../config/routes.php';
require_once '../src/modele/_classes.php';

$loader = new \Twig\Loader\FilesystemLoader('../src/vue/');
$twig = new \Twig\Environment($loader, []);
$twig->addGlobal('session', $_SESSION);

$db = connect();
$nomFonction = getPage($db);  // Ex: "accueilControleur"
$nomFonction($twig, $db);     // appel direct, la vérif se fait dans le contrôleur
