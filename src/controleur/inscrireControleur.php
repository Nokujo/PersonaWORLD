<?php
function inscrireControleur($twig) : void {
    $form = array();

    if (isset($_POST['btnInscrire'])) {
        $inputEmail     = filter_input(INPUT_POST, 'inputEmail', FILTER_VALIDATE_EMAIL);
        $inputPassword  = $_POST['inputPassword'];
        $inputPassword2 = $_POST['inputPassword2'];
        $nom            = htmlspecialchars($_POST['nom']);
        $prenom         = htmlspecialchars($_POST['prenom']);
        $nuser          = htmlspecialchars($_POST['nuser']);
        $role           = $_POST['role'];

        
        if (!$inputEmail) {
            $form['valide'] = false;
            $form['message'] = 'Email invalide';
        }
        
        else if ($inputPassword !== $inputPassword2) {
            $form['valide'] = false;
            $form['message'] = 'Les mots de passe sont différents';
        } 
        
        else if (strlen($inputPassword) < 8) {
            $form['valide'] = false;
            $form['message'] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        else {
            try {
                $pdo = new PDO("mysql:host=10.51.7.100;dbname=site commerce;charset=utf8", "mustaphadmin", "mustapha");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
              
                $checkSql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([$inputEmail]);
                
                if ($checkStmt->fetchColumn() > 0) {
                    $form['valide'] = false;
                    $form['message'] = 'Cet email est déjà utilisé';
                } else {
                    $sql = "INSERT INTO utilisateur (prenom, nom, nuser, email, password, idRole) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $prenom,
                        $nom,
                        $nuser,
                        $inputEmail,
                        password_hash($inputPassword, PASSWORD_DEFAULT),
                        $role
                    ]);
                    $form['valide'] = true;
                    $form['email'] = $inputEmail;
                    $form['nuser'] = $nuser;
                    $form['role'] = $role;
                }
            } catch (PDOException $e) {
                $form['valide'] = false;
                $form['message'] = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    }

    echo $twig->render('inscrire.html.twig', array('form' => $form));
}
