<?php

function utilisateurControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $form = [];
    $u    = new Utilisateur($db);

    // — Suppression individuelle via ?page=utilisateur&id=XX
    if (isset($_GET['id'])) {
        $etat = $u->delete($_GET['id']);
        header('Location: index.php?page=utilisateur&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // — Suppression multiple via checkbox + bouton “btSupprimer”
    if (isset($_POST['btSupprimer'])) {
        $ids  = $_POST['cocher'] ?? [];
        $etat = true;
        foreach ($ids as $id) {
            if (!$u->delete($id)) {
                $etat = false;
            }
        }
        header('Location: index.php?page=utilisateur&etat=' . ($etat ? 'true' : 'false'));
        exit;
    }

    // — Message de retour suppression
    if (isset($_GET['etat'])) {
        $form['etat'] = ($_GET['etat'] === 'true');
    }

    // — Récupère et affiche la liste
    $liste = $u->select();
    echo $twig->render('utilisateur.html.twig', [
        'form'  => $form,
        'liste' => $liste
    ]);
}
function utilisateurModifControleur($twig, $db) {
    $form = [];

    if (isset($_GET['id'])) {
        $utilisateur = new Utilisateur($db);
        $unUtilisateur = $utilisateur->selectById($_GET['id']);

        if ($unUtilisateur != null) {
            $form['utilisateur'] = $unUtilisateur;

            $role = new Role($db);
            $liste = $role->select();
            $form['roles'] = $liste;
        } else {
            $form['message'] = 'Utilisateur introuvable';
        }
    } elseif (isset($_POST['btModifier'])) {
        $utilisateur = new Utilisateur($db);
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $nuser = $_POST['nuser']; // ← récup du pseudo

        // Ajout du pseudo dans la méthode update
        $ok = $utilisateur->update($id, $email, $nom, $prenom, $nuser, $role);



        // Gestion du mot de passe si modifié
        if (!empty($_POST['inputPassword'])) {
            if ($_POST['inputPassword'] == $_POST['inputPassword2']) {
                $utilisateur->updateMdp($id, $_POST['inputPassword']);
            } else {
                $form['valide'] = false;
                $form['message'] = 'Les mots de passe ne correspondent pas';
            }
        }

        if (!isset($form['valide'])) {
            $form['valide'] = $ok;
            $form['message'] = $ok ? 'Modification réussie' : 'Erreur lors de la modification';
        }

        // Recharge l'utilisateur et les rôles pour afficher les champs remplis à jour
        $form['utilisateur'] = $utilisateur->selectById($id);
        $role = new Role($db);
        $form['roles'] = $role->select();
    } else {
        $form['message'] = 'Utilisateur non précisé';
    }

    echo $twig->render('utilisateurModif.html.twig', ['form' => $form]);
}
