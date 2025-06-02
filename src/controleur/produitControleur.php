<?php

function produitControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $form = [];
    $m    = new Produit($db);
    $typeModel = new Type($db);
    $listeTypes = $typeModel->select();
    $form['types'] = $listeTypes;
    $q = trim($_GET['search'] ?? '');

    // Récupération et affichage de la liste des produits
    if ($q === '') {
        $liste = $m->select();
    } else {
        $liste = $m->recherche($q);
    }

    // Suppression individuelle
    if (isset($_GET['id'])) {
        $etat = $m->delete($_GET['id']);
        header('Location: index.php?page=produit&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // Suppression en masse
    if (isset($_POST['btSupprimer'])) {
        $ids  = $_POST['cocher'] ?? [];
        $etat = true;
        foreach ($ids as $id) {
            if (!$m->delete($id)) {
                $etat = false;
            }
        }
        header('Location: index.php?page=produit&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // Message de retour
    if (isset($_GET['etat'])) {
        $form['etat'] = ($_GET['etat'] === 'true');
    }

    // Affichage de la liste
    $liste = $m->select();
    echo $twig->render('produit.html.twig', [
        'form'  => $form,
        'liste' => $liste
    ]);
}

function produitModifControleur($twig, $db) {
    $form = [];

    if (isset($_GET['id'])) {
        $m            = new Produit($db);
        $form['prod'] = $m->selectById($_GET['id']);
        $typeModel      = new Type($db);
        $form['types']  = $typeModel->select();
    }

    // Traitement du formulaire de modification
    elseif (isset($_POST['btModifier'])) {
        $m = new Produit($db);

        // Récupération du lien image (si vide, on garde l’ancien)
        $imgName = !empty($_POST['img']) ? $_POST['img'] : $_POST['existingImg'];

        // Mise à jour en base
        $ok = $m->update(
           $_POST['id'],
           $_POST['nom'],
           $_POST['description'],
           $imgName,
           $_POST['type']   ?: null,
           (float)$_POST['prix'],
           (int)$_POST['quantite']
        );

        $form['valide']   = $ok;
        $form['message']  = $ok ? 'Produit modifié' : 'Erreur lors de la modification';
        $form['prod']   = $m->selectById($_POST['id']);
        $typeModel      = new Type($db);
        $form['types']  = $typeModel->select();
    }

    echo $twig->render('produitModif.html.twig', ['form' => $form]);
}
