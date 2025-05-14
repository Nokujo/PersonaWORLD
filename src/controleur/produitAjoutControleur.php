<?php

function produitAjoutControleur($twig, $db): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form = [];
    $typeModel = new Type($db);
    $listeTypes = $typeModel->select();
    $form['types'] = $listeTypes;

    if (isset($_POST['btAjouter'])) {
        $imgUrl = trim($_POST['img'] ?? '');

        if (empty($imgUrl)) {
            $form['message'] = 'Veuillez fournir un lien URL d’image valide.';
        } else {
            $m  = new Produit($db);
            $ok = $m->insert(
                $_POST['nom'],
                $_POST['description'],
                $imgUrl,                     // ici, on insère directement l’URL
                $_POST['type'] ?? null,
                $_POST['prix'],
                $_POST['quantite']
            );
            $form['valide']   = $ok;
            $form['message']  = $ok ? 'Produit ajouté' : 'Erreur lors de l’ajout';
        }
    }

    echo $twig->render('produitAjout.html.twig', ['form' => $form]);
}
