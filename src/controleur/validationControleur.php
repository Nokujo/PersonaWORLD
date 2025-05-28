<?php
require_once '../config/connexion.php';
require_once '../vendor/autoload.php'; // utilise l'autoload de Composer


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function validationControleur($twig, $db) {
    $form = [];

    $email = $_GET['email'] ?? '';
    $idgenere = $_GET['idgenere'] ?? '';

    if ($email && $idgenere) {
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = :email AND idgenere = :idgenere AND valider = 0");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':idgenere', $idgenere);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            // ✅ Mise à jour de l’état de validation
            $update = $db->prepare("UPDATE utilisateur SET valider = 1 WHERE email = :email");
            $update->bindValue(':email', $email);
            $update->execute();

            // 📧 Envoi mail au client
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mustaphachouhani2@gmail.com';
                $mail->Password = 'xtitlcvydzqpppwl'; // mot de passe d'application
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('mustaphachouhani2@gmail.com', 'Persona World');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Votre compte a été validé !';
                $mail->Body    = "
                    Bonjour <strong>{$user['prenom']} {$user['nom']}</strong>,<br><br>
                    Votre compte sur <strong>Persona World</strong> a bien été validé par l’administrateur.<br>
                    Vous pouvez dès à présent vous connecter, remplir votre panier, et découvrir notre sélection !<br><br>
                    <a href='http://{$_SERVER['HTTP_HOST']}/index.php?page=connexion' 
                       style='display:inline-block;padding:10px 20px;background:#5D4AFF;color:#fff;border-radius:5px;text-decoration:none;'>
                       Se connecter
                    </a><br><br>
                    Merci de votre confiance ✨
                ";
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = 'html';

                $mail->send();
                $form['valide'] = true;
                $form['message'] = "✔️ Le compte a été activé et l'utilisateur a été notifié par e-mail.";
            } catch (Exception $e) {
                $form['valide'] = true;
                $form['message'] = "✔️ Le compte a été activé, mais l’e-mail de confirmation n’a pas pu être envoyé.";
                error_log("Erreur mail validation : " . $mail->ErrorInfo);
            }

        } else {
            $form['valide'] = false;
            $form['message'] = "Utilisateur introuvable, ou déjà validé.";
        }
    } else {
        $form['valide'] = false;
        $form['message'] = "Paramètres manquants.";
    }

    echo $twig->render("validation.html.twig", ['form' => $form]);
}
