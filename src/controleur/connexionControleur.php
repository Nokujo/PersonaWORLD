<?php
function connexionControleur($twig) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form = [];

    if (isset($_POST['btnConnexion'])) {
        $email = $_POST['inputEmail'];
        $password = $_POST['inputPassword'];

        $pdo = new PDO("mysql:host=10.51.7.100;dbname=site commerce;charset=utf8", "mustaphadmin", "mustapha");
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['login'] = $user['email'];
            $_SESSION['role'] = $user['idRole'];

            header("Location: index.php?page=accueil");
            exit(); 
        } else {
            $form['valide'] = false;
            $form['message'] = "Identifiants incorrects.";
        }
    }

    echo $twig->render('connexion.html.twig', ['form' => $form]);
}
