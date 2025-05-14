<?php
class Utilisateur {
    private $db;
    private $select;
    private $selectById;
    private $update;
    private $updateMdp;
    private $delete;
    private $selectByEmail;


    public function __construct($db) {
        $this->db = $db;

        $this->select = $this->db->prepare("
            SELECT u.id, u.email, u.nom, u.prenom, u.idRole, r.libelle AS libellerole
            FROM utilisateur u
            JOIN role r ON u.idRole = r.id
            ORDER BY u.nom
        ");

        $this->selectById = $this->db->prepare("
            SELECT * FROM utilisateur WHERE id = :id
        ");

        $this->update = $this->db->prepare("
            UPDATE utilisateur
            SET nom = :nom, prenom = :prenom, idRole = :role, email = :email
            WHERE id = :id
        ");

        $this->updateMdp = $this->db->prepare("
            UPDATE utilisateur
            SET password = :mdp
            WHERE id = :id
        ");

        $this->selectByEmail = $this->db->prepare("
        SELECT * FROM utilisateur WHERE email = :email
        ");

        $this->delete = $db->prepare("DELETE FROM utilisateur WHERE id = :id");

    }

    public function select() {
        $this->select->execute();
        if ($this->select->errorCode() != 0) {
            print_r($this->select->errorInfo());
        }
        return $this->select->fetchAll();
    }

    public function selectById($id) {
        $this->selectById->execute([':id' => $id]);
        if ($this->selectById->errorCode() != 0) {
            print_r($this->selectById->errorInfo());
        }
        return $this->selectById->fetch();
    }

    public function update($id, $role, $nom, $prenom, $email) {
        $this->update->execute([
            ':id' => $id,
            ':role' => $role,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email
        ]);
        return $this->update->errorCode() == 0;
    }

    public function updateMdp($id, $mdp) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $this->updateMdp->execute([
            ':id' => $id,
            ':mdp' => $hash
        ]);
        return $this->updateMdp->errorCode() == 0;
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

    public function delete($id) {
        $r = true;
        $this->delete->execute([':id' => $id]);
        if ($this->delete->errorCode() != '00000') {
            print_r($this->delete->errorInfo());
            $r = false;
        }
        return $r;
    }
    
    public function selectByEmail($email) {
        $this->selectByEmail->execute([':email' => $email]);
        if ($this->selectByEmail->errorCode() != '00000') {
            print_r($this->selectByEmail->errorInfo());
        }
        return $this->selectByEmail->fetch();
    }
}
