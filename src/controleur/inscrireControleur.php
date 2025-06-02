<?php
require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function inscrireControleur($twig) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    require_once '../config/parametres.php';
    require_once '../config/connexion.php';

    $form = [];

    if (isset($_POST['btnInscrire'])) {
        $email     = filter_input(INPUT_POST, 'inputEmail', FILTER_VALIDATE_EMAIL);
        $password  = $_POST['inputPassword'] ?? '';
        $password2 = $_POST['inputPassword2'] ?? '';
        $nom       = htmlspecialchars($_POST['nom']);
        $prenom    = htmlspecialchars($_POST['prenom']);
        $nuser     = $_POST['nuser'];
        $role      = 2;

        if (!$email) {
            $form['valide'] = false;
            $form['message'] = 'Email invalide';
        } elseif ($password !== $password2) {
            $form['valide'] = false;
            $form['message'] = 'Les mots de passe sont différents';
        } elseif (strlen($password) < 8) {
            $form['valide'] = false;
            $form['message'] = 'Le mot de passe doit contenir au moins 8 caractères';
        } else {
            try {
                $db = connect();
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $check = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
                $check->execute([$email]);

                if ($check->fetchColumn() > 0) {
                    $form['valide'] = false;
                    $form['message'] = 'Cet email est déjà utilisé';
                } else {
                    $idgenere = uniqid();
                    $stmt = $db->prepare("
                        INSERT INTO utilisateur (prenom, nom, nuser, email, password, idRole, valider, idgenere)
                        VALUES (?, ?, ?, ?, ?, ?, 0, ?)
                    ");
                    $stmt->execute([
                        $prenom, $nom, $nuser, $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        $role,
                        $idgenere
                    ]);

                    // ENVOI EMAIL CLIENT
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'mustaphachouhani2@gmail.com';
                    $mail->Password   = 'iydn cewu pbzq fsic'; // ✅ mot de passe app qui marche
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('mustaphachouhani2@gmail.com', 'Persona World');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Inscription en attente de validation';
                    $mail->Body    = "Bonjour $prenom,<br>Merci pour votre inscription. Un administrateur va bientôt valider votre compte.<br>Vous recevrez un email de confirmation.";

                    $mail->send();

                    // ENVOI EMAIL ADMIN
                    $adminMail = new PHPMailer(true);
                    $adminMail->isSMTP();
                    $adminMail->Host       = 'smtp.gmail.com';
                    $adminMail->SMTPAuth   = true;
                    $adminMail->Username   = 'mustaphachouhani2@gmail.com';
                    $adminMail->Password   = 'iydn cewu pbzq fsic';
                    $adminMail->SMTPSecure = 'tls';
                    $adminMail->Port       = 587;
                    $adminMail->setFrom('mustaphachouhani2@gmail.com', 'Persona World');
                    $adminMail->addAddress('mustaphachouhani2@gmail.com');
                    $adminMail->isHTML(true);
                    $adminMail->CharSet = 'UTF-8';
                    $adminMail->Subject = 'Nouvelle inscription à valider';
                    $lien = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=validation&email=$email&idgenere=$idgenere";
                    $adminMail->Body = "Un nouveau compte client a été créé.<br><a href=\"$lien\">Cliquez ici pour le valider</a>";

                    $adminMail->send();

                    $form['valide'] = true;
                    $form['message'] = 'Inscription réussie. Un mail a été envoyé.';
                }

            } catch (Exception $e) {
                $form['valide'] = false;
               error_log("Erreur PHPMailer : " . $e->getMessage());
               $form['message'] = "Une erreur est survenue lors de l’envoi du mail.";

            } catch (PDOException $e) {
                $form['valide'] = false;
                error_log("Erreur BDD : " . $e->getMessage());
                $form['message'] = "Une erreur est survenue. Veuillez réessayer plus tard.";

            }
        }
    }

    echo $twig->render('inscrire.html.twig', ['form' => $form]);
}
