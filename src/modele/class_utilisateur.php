<?php
class Client {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function inscrireClient($prenom, $nom, $email, $password, $nuser, $role) {
        try {
            // Hashage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Préparation de la requête SQL
            $sql = "INSERT INTO utilisateur (prenom, nom, email, password, nuser, role) VALUES (:prenom, :nom, :email, :password, :nuser, :role)";
            $stmt = $this->db->prepare($sql);

            // Liaison des paramètres
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':nuser', $nuser);
            $stmt->bindParam(':role', $role, PDO::PARAM_INT);

            // Exécution de la requête
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            // Gestion des erreurs
            return "Erreur : " . $e->getMessage();
        }
    }
}