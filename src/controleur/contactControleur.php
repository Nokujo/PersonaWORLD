<?php
require_once '../vendor/autoload.php';
require_once '../config/connexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function contactControleur($twig, $db) {
    $form = [];

    if (isset($_POST['btEnvoyer'])) {
        $nom     = htmlspecialchars($_POST['nom'] ?? '');
        $prenom  = htmlspecialchars($_POST['prenom'] ?? '');
        $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = htmlspecialchars($_POST['message'] ?? '');

        if (!$email || empty($message) || empty($nom) || empty($prenom)) {
            $form['valide'] = false;
            $form['message'] = "Tous les champs sont requis.";
        } else {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mustaphachouhani2@gmail.com';
                $mail->Password = 'iydn cewu pbzq fsic'; // mot de passe d'application
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('mustaphachouhani2@gmail.com', 'Persona World');
                $mail->addReplyTo($email, "$prenom $nom");
                $mail->addAddress('mustaphachouhani2@gmail.com', 'Mustapha Admin');
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = "📩 Nouveau message du site Persona World";
                $mail->Body = "<strong>De :</strong> $prenom $nom<br><strong>Email :</strong> $email<br><br><strong>Message :</strong><br>$message";

                $mail->send();
                $form['valide'] = true;
                $form['message'] = "Votre message a été envoyé avec succès. Nous vous répondrons rapidement.";
            } catch (Exception $e) {
                $form['valide'] = false;
                $form['message'] = "Erreur lors de l'envoi : " . $mail->ErrorInfo;
            }
        }
    }

    echo $twig->render("contact.html.twig", [
        'form' => $form
    ]);
}
