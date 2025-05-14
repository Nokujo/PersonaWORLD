<?php
function panierControleur($twig, $db){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form  = [];
    $liste = [];

    // — suppression d’un article
    if (isset($_GET['remove'])) {
        unset($_SESSION['panier'][$_GET['remove']]);
    }

    // — mise à jour des quantités
    if (isset($_POST['update'])) {
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'q-') === 0) {
                $id = (int)substr($k, 2);
                $_SESSION['panier'][$id] = $v;
            }
        }
        $form['message'] = 'Panier mis à jour.';
        $form['valide']  = true;
    }

    // — passer la commande
    if (isset($_POST['placerCommande'])) {
        $montant = $_POST['montant'];
        $date    = (new DateTime('now', new DateTimeZone('Europe/Paris')))
                     ->format('Y-m-d H:i:s');

        // récupérer l’id du client
        $util      = new Utilisateur($db);
        $unUtil    = $util->selectByEmail($_SESSION['login']);
        $idUser    = $unUtil['id'];

        // insérer la commande
        $commande  = new Commande($db);
        $ok        = $commande->insert($montant, $date, 1, $idUser);

        if ($ok) {
            $maCommande = $commande->selectByDateCli($date, $idUser);
            $composer   = new Composer($db);
            foreach ($_SESSION['panier'] as $pid => $qty) {
                if (!$composer->insert($maCommande['id'], $pid, $qty)) {
                    $form['erreur'] = 'Au moins un produit n’a pas été validé.';
                }
            }
            $form['valide']   = true;
            $form['message']  = 'Votre commande a été passée.';
            unset($_SESSION['panier']);
        } else {
            $form['valide']  = false;
            $form['message'] = 'Erreur lors de l’enregistrement de la commande.';
        }
    }

    // — récupérer les produits du panier
    if (!empty($_SESSION['panier'])) {
        $ids  = array_keys($_SESSION['panier']);
        $prod = new Produit($db);
        $liste = $prod->selectIn($ids);
    }

    echo $twig->render('panier.html.twig', [
        'form'  => $form,
        'liste' => $liste
    ]);
}

function ajoutPanierControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int)$_GET['id'];

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $_SESSION['panier'][$id] = ($_SESSION['panier'][$id] ?? 0) + 1;

        // Redirection fluide sans remonter
        echo "<script>window.history.back();</script>";
        exit;
    } else {
        echo $twig->render("accueil.html.twig", [
            "erreur" => "ID de produit invalide",
        ]);
    }
}
