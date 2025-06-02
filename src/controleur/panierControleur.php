<?php
function panierControleur($twig, $db){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form  = [];
    $liste = [];

    // Initialisation panier si absent
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Suppression d’un article
    if (isset($_GET['remove'])) {
        unset($_SESSION['panier'][$_GET['remove']]);
    }

    // Mise à jour des quantités
    if (isset($_POST['update'])) {
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'q-') === 0) {
                $id = (int)substr($k, 2);
                $_SESSION['panier'][$id] = max(1, (int)$v); // min 1
            }
        }
        $form['message'] = 'Panier mis à jour.';
        $form['valide']  = true;
    }

    // Passage de commande
    if (isset($_POST['placerCommande']) && !empty($_SESSION['panier'])) {
        $montant = $_POST['montant'];
        $date    = (new DateTime('now', new DateTimeZone('Europe/Paris')))
                    ->format('Y-m-d H:i:s');

        // Récupération du client
        $util    = new Utilisateur($db);
       if (!isset($_SESSION['login'])) {
         $form['valide'] = false;
            $form['message'] = "Vous devez être connecté pour passer une commande.";
        } else {
            $unUtil  = $util->selectByEmail($_SESSION['login']);
            $idUser  = $unUtil['id'] ?? null;
        }

        $idUser  = $unUtil['id'] ?? null;

        if ($idUser) {
            $commande     = new Commande($db);
            $idCommande   = $commande->insert($montant, $date, 1, $idUser);

            if ($idCommande !== null) {
                $composer = new Composer($db);

                foreach ($_SESSION['panier'] as $pid => $qty) {
                    if (!$composer->insert($idCommande, $pid, $qty)) {
                        $form['erreur'] = '⚠️ Un produit n’a pas pu être ajouté.';
                    }
                }

                $form['valide']     = true;
                $form['message']    = '✅ Commande passée avec succès.';
                $form['commandeOK'] = true;
                $form['idCommande'] = $idCommande;
                
                foreach ($_SESSION['panier'] as $idProduit => $qteAchetee) {
                    // Récupérer la quantité actuelle
                    $stmt = $db->prepare("SELECT quantite FROM produits WHERE id = :id");
                    $stmt->bindValue(':id', $idProduit, PDO::PARAM_INT);
                    $stmt->execute();
                    $produit = $stmt->fetch();
                
                    if ($produit) {
                        $nouvelleQuantite = max(0, $produit['quantite'] - $qteAchetee);
                        $update = $db->prepare("UPDATE produits SET quantite = :qte WHERE id = :id");
                        $update->bindValue(':qte', $nouvelleQuantite, PDO::PARAM_INT);
                        $update->bindValue(':id', $idProduit, PDO::PARAM_INT);
                        $update->execute();
                    }
                }
                
                unset($_SESSION['panier']);
            } else {
                $form['valide']  = false;
                $form['message'] = 'Erreur lors de la création de la commande.';
            }
        } else {
            $form['valide']  = false;
            $form['message'] = 'Veuillez vous connectez ou créer un compte.';
        }
    }
    // Récupération des produits si panier non vide
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

        echo "<script>window.history.back();</script>";
        exit;
    } else {
        echo $twig->render("accueil.html.twig", [
            "erreur" => "ID de produit invalide",
        ]);
    }
}
