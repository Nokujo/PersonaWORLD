<?php
class Utilisateur {
    private $db;
    private $select;

    public function __construct($db) {
        $this->db = $db;

        // Préparation de la requête de sélection
        $this->select = $this->db->prepare("
            SELECT u.id, u.email, u.nom, u.prenom, u.idRole, r.libelle AS libellerole
            FROM utilisateur u
            JOIN role r ON u.idRole = r.id
            ORDER BY u.nom
        ");
    }

    public function select() {
        $this->select->execute();
        if ($this->select->errorCode() != 0) {
            print_r($this->select->errorInfo());
        }
        return $this->select->fetchAll();
    }

    public function inscrireClient($prenom, $nom, $email, $password, $nuser, $role) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO utilisateur (prenom, nom, email, password, nuser, idRole) 
                    VALUES (:prenom, :nom, :email, :password, :nuser, :role)";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':nuser', $nuser);
            $stmt->bindParam(':role', $role, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}
