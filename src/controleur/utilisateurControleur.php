<?php
function utilisateurControleur($twig, $db) {
    $utilisateur = new Utilisateur($db); // ✅ utilise ta classe
    $liste = $utilisateur->select();     // ✅ récupère tous les utilisateurs
    
    echo $twig->render('utilisateur.html.twig', ['liste' => $liste]);
}
