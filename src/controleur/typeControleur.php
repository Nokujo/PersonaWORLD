<?php

function typeControleur($twig, $db) {
    
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    $form = [];
    $m    = new Type($db);

    // 1) Suppression individuelle via ?page=type&id=XX
    if (isset($_GET['id'])) {
        $etat = $m->delete($_GET['id']);
        header('Location: index.php?page=type&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // 2) Suppression multiple via checkbox + bouton “btSupprimer”
    if (isset($_POST['btSupprimer'])) {
        $ids  = $_POST['cocher'] ?? [];
        $etat = true;
        foreach ($ids as $id) {
            if (!$m->delete($id)) {
                $etat = false;
            }
        }
        header('Location: index.php?page=type&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // Message de retour suppression
    if (isset($_GET['etat'])) {
        $form['etat'] = ($_GET['etat'] === 'true');
    }

    // Récupération et affichage de la liste des types
    $liste = $m->select();
    echo $twig->render('type.html.twig', [
        'form'  => $form,
        'liste' => $liste
    ]);

    // Ajout d’un nouveau type
if (isset($_POST['btAjouter']) && !empty($_POST['libelle'])) {
    $libelle = trim($_POST['libelle']);
    $etat = $m->insert($libelle);
    $form['etatAjout'] = $etat;
    $form['msgAjout'] = $etat ? 'Type ajouté avec succès' : 'Erreur lors de l\'ajout';
}

}

function typeModifControleur($twig, $db) {
    $form = [];

    // Affichage du formulaire pré-rempli
    if (isset($_GET['id'])) {
        $m            = new Type($db);
        $form['type'] = $m->selectById($_GET['id']);
    }
    // Traitement de la soumission du formulaire
    elseif (isset($_POST['btModifier'])) {
        $m            = new Type($db);
        $id           = $_POST['id'];
        $libelle      = $_POST['libelle'];
        $ok           = $m->update($id, $libelle);
        $form['valide']   = $ok;
        $form['message']  = $ok ? 'Type modifié' : 'Erreur lors de la modification';
     
        $form['type']     = $m->selectById($id);
    }
    // Aucun id ni soumission
    else {
        $form['message'] = 'Type non précisé';
    }

    echo $twig->render('type-modif.html.twig', [
        'form' => $form
    ]);
}
