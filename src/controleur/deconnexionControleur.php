<?php
function deconnexionControleur($twig, $db) {
    session_start();
    session_unset();          // Vide $_SESSION
    session_destroy();        
    header("Location: index.php?page=accueil"); // Redirection
    exit();

}