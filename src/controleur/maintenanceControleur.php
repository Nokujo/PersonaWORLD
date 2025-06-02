<?php

function maintenanceControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form = [];

   
    if (file_exists('config/maintenance.flag')) {
        echo $twig->render('maintenance.html.twig', ['form' => $form]);
    } else {
      
        header('Location: index.php?page=accueil');
        exit;
    }
}
