<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../vendor/autoload.php';
require_once '../config/connexion.php';
require_once '../src/modele/class_commande.php';

use Spipu\Html2Pdf\Html2Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getImageBase64($url) {
    $data = @file_get_contents($url);
    if ($data === false) return '';
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($data);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
        return '';
    }
    return 'data:' . $mime . ';base64,' . base64_encode($data);
}

function factureControleur($twig, $db) {
    if (!isset($_SESSION['role'], $_SESSION['id'])) {
        echo "⛔ Non autorisé.";
        return;
    }

    $idCommande = $_GET['id'] ?? null;
    if (!is_numeric($idCommande)) {
        echo "❌ ID commande invalide.";
        return;
    }

    $commandeModel = new Commande($db);
    $details = $commandeModel->selectWithProduits($idCommande);
if (!is_array($details) || count($details) === 0) {
    echo "❌ Commande introuvable.";
    return;
}
$detailsCopy = $details; // pour le template



    if ($_SESSION['role'] == 2 && $details[0]['idUtilisateur'] != $_SESSION['id']) {
        echo "⛔ Accès interdit à cette facture.";
        return;
    }

    foreach ($details as &$ligne) {
    if (!empty($ligne['img'])) {
        $ligne['img'] = getImageBase64($ligne['img']); 
    }
}
$detailsCopy = $details;


    $nomFichier = 'facture_' . $idCommande . '.pdf';
    $cheminFichier = __DIR__ . '/../../factures/' . $nomFichier;



    ob_start();
include '../src/vue/facture_template.php';
$content = ob_get_clean();

try {
    // Génère le PDF en mémoire
    $pdf = new Html2Pdf('P', 'A4', 'fr');
    $pdf->writeHTML($content);

    // Enregistre temporairement pour le mail
    $cheminFichier = __DIR__ . '/../facture_temp.pdf';
    $pdf->output($cheminFichier, 'F'); // pour pièce jointe

} catch (Exception $e) {
    echo "Erreur PDF : " . $e->getMessage();
    return;
}

// Envoi mail (AVANT affichage)
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mustaphachouhani2@gmail.com';
    $mail->Password = 'xtitlcvydzqpppwl';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('mustaphachouhani2@gmail.com', 'Persona World');
    $mail->addAddress($_SESSION['login']);
    $mail->addAttachment($cheminFichier); // en pièce jointe

   $mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Subject = '📎 Votre facture Persona World';
$mail->Body    = 'Merci pour votre commande ! Vous trouverez ci-joint votre facture au format PDF.';

    $mail->send();

    unlink($cheminFichier); // nettoyage
} catch (Exception $e) {
    error_log("Erreur mail : " . $mail->ErrorInfo);
}

// Affiche ensuite le PDF dans le navigateur
$pdf->output('facture_' . $idCommande . '.pdf', 'D');


}
