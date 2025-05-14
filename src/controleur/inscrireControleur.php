<?php

function inscrireControleur($twig) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    require_once '../config/parametres.php';  // ✅ ajoute ça AVANT connexion
    require_once '../config/connexion.php';

    $form = [];

    if (isset($_POST['btnInscrire'])) {
        $email        = filter_input(INPUT_POST, 'inputEmail', FILTER_VALIDATE_EMAIL);
        $password     = $_POST['inputPassword'] ?? '';
        $password2    = $_POST['inputPassword2'] ?? '';
        $nom          = htmlspecialchars($_POST['nom']);
        $prenom       = htmlspecialchars($_POST['prenom']);
        $nuser        = $_POST['nuser'];
        $role         = 2;

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
                $db = connect();  // ✅ ici config est bien défini
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Vérifie si email existe déjà
                $check = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
                $check->execute([$email]);

                if ($check->fetchColumn() > 0) {
                    $form['valide'] = false;
                    $form['message'] = 'Cet email est déjà utilisé';
                } else {
                    $stmt = $db->prepare("
                        INSERT INTO utilisateur (prenom, nom, nuser, email, password, idRole)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $prenom, $nom, $nuser, $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        $role
                    ]);

                    $_SESSION['login'] = $email;
                    $_SESSION['role'] = $role;

                    header('Location: index.php?page=accueil');
                    exit;
                }

            } catch (PDOException $e) {
                $form['valide'] = false;
                $form['message'] = "Erreur : " . $e->getMessage();
            }
        }
    }

    echo $twig->render('inscrire.html.twig', ['form' => $form]);
}
