<?php

function maintenanceControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form = [];

    // Affiche la page uniquement si le fichier maintenance.flag existe
    if (file_exists('config/maintenance.flag')) {
        echo $twig->render('maintenance.html.twig', ['form' => $form]);
    } else {
        // Redirige vers l'accueil si le site n'est pas en maintenance
        header('Location: index.php?page=accueil');
        exit;
    }
}
