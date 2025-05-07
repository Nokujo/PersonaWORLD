<?php
function connexionControleur($twig) : void {
   

    $form = [];

    if (isset($_POST['btnConnexion'])) {
        $email = $_POST['inputEmail'];
        $password = $_POST['inputPassword'];

        // Connexion à la BDD
        try {
            $pdo = new PDO("mysql:host=10.51.7.100;dbname=site commerce;charset=utf8", "mustaphadmin", "mustapha");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT * FROM utilisateur WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $form['valide'] = true;
                $form['email'] = $user['email'];
                $form['role'] = $user['idRole'];
            } else {
                $form['valide'] = false;
                $form['message'] = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $form['valide'] = false;
            $form['message'] = "Erreur serveur : " . $e->getMessage();
        }
    }

    echo $twig->render('connexion.html.twig', ['form' => $form]);
}


