<?php
function getPage($db) {
    $lesPages = [
        'accueil'           => 'accueilControleur',
        'contact'           => 'contactControleur',
        'inscrire'          => 'inscrireControleur',
        'mentions'          => 'mentionsControleur',
        'a propos'          => 'aproposControleur',
        'maintenance'       => 'maintenanceControleur',
        'connexion'         => 'connexionControleur',
        'deconnexion'       => 'deconnexionControleur',

        // Utilisateur
        'utilisateur'       => 'utilisateurControleur',
        'utilisateurModif'  => 'utilisateurModifControleur',
        'validation'        => 'validationControleur',


        // Type
        'type'              => 'typeControleur',
        'typeModif'         => 'typeModifControleur',

        // Produit
        'produit'           => 'produitControleur',
        'produitAjout'      => 'produitAjoutControleur',
        'produitModif'      => 'produitModifControleur',
        'produitfiche'      => 'produitFicheControleur',

        // Panier
        'ajoutPanier'       => 'ajoutPanierControleur',
        'panier'            => 'panierControleur',
        'commande'          => 'commandeControleur',
        'facture'           => 'factureControleur',
        
        'contact'           =>  'contactControleur',

    ];

    if ($db !== null) {
        $page = $_GET['page'] ?? 'accueil';
        return $lesPages[$page] ?? $lesPages['accueil'];
    } else {
        return $lesPages['maintenance'];
    }
}
?>
