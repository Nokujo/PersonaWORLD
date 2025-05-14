<?php
function connexionControleur($twig) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once(__DIR__ . '/../../config/connexion.php'); // ✅


    $form = [];

    if (isset($_POST['btnConnexion'])) {
        $email = $_POST['inputEmail'];
        $password = $_POST['inputPassword'];

        $db = connect(); // ✅ autochargé

        if ($db !== null) {
            $sql = "SELECT * FROM utilisateur WHERE email = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['login'] = $user['email'];
                $_SESSION['role'] = $user['idRole'];

                if ($_SESSION['role'] == 3) {
                    header("Location: index.php?page=produit");
                } else {
                    header("Location: index.php?page=accueil");
                }
                exit();
            } else {
                $form['valide'] = false;
                $form['message'] = "Identifiants incorrects.";
            }
        } else {
            $form['valide'] = false;
            $form['message'] = "Connexion à la base de données impossible.";
        }
    }

    echo $twig->render('connexion.html.twig', ['form' => $form]);
}
