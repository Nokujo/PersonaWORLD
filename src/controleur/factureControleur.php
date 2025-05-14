<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Charger l'autoloader de Composer AVANT tout
require_once '../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

function factureControleur($twig, $db) {
    // 🔐 Vérification admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
        header("Location: index.php?page=connexion");
        exit;
    }

    // 🆔 Vérification de l’ID
    $idCommande = $_GET['id'] ?? null;
    error_log('ID reçu dans factureControleur : ' . ($idCommande ?? 'non défini'));

    if (!is_numeric($idCommande)) {
        echo "ID de commande invalide";
        return;
    }

    // 📦 Récupération des détails
    $commandeModel = new Commande($db);
    $details = $commandeModel->selectWithProduits($idCommande);

    if (!$details || count($details) === 0) {
        echo "Commande introuvable.";
        return;
    }

    // 🧾 Génération du HTML de la facture
    ob_start();
    include '../src/vue/facture_template.php';
    $content = ob_get_clean();

    // 📄 Génération du PDF
    try {
        $pdf = new Html2Pdf('P', 'A4', 'fr');
        $pdf->writeHTML($content);
        $pdf->output('facture_' . $idCommande . '.pdf');
    } catch (Exception $e) {
        echo "Erreur de génération PDF : " . $e->getMessage();
    }
}
