<?php
class Commande {
    private $db;
    private $insert;
    private $selectByDateCli;

    public function __construct($db) {
        $this->db = $db;

        $this->insert = $db->prepare("
            INSERT INTO commande (montant, dateCommande, idEtat, idUtilisateur)
            VALUES (:montant, :dateCommande, :idEtat, :idUtilisateur)
        ");

        $this->selectByDateCli = $db->prepare("
            SELECT * FROM commande 
            WHERE dateCommande = :dateCommande 
              AND idUtilisateur = :idUtilisateur
        ");
    }

    public function insert($montant, $dateCommande, $idEtat, $idUtilisateur) {
        $this->insert->execute([
            ':montant' => $montant,
            ':dateCommande' => $dateCommande,
            ':idEtat' => $idEtat,
            ':idUtilisateur' => $idUtilisateur
        ]);
        return $this->insert->errorCode() === '00000';
    }

    public function selectByDateCli($dateCommande, $idUtilisateur) {
        $this->selectByDateCli->execute([
            ':dateCommande' => $dateCommande,
            ':idUtilisateur' => $idUtilisateur
        ]);
        return $this->selectByDateCli->fetch();
    }

    public function selectAll() {
        $sql = "
        SELECT c.*, e.libelle AS etatLibelle, u.nom, u.prenom
        FROM commande c
        JOIN etat e ON c.idEtat = e.id
        JOIN utilisateur u ON c.idUtilisateur = u.id
        ORDER BY c.dateCommande DESC
      ";

        $req = $this->db->prepare($sql);
        $req->execute();

        if ($req->errorCode() != 0) {
            print_r($req->errorInfo());
        }

        return $req->fetchAll();
    }

    public function updateEtat($idCommande, $idEtat) {
        $sql = "UPDATE commande SET idEtat = :idEtat WHERE id = :idCommande";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idEtat' => $idEtat,
            ':idCommande' => $idCommande
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM commande WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function selectWithProduits($idCommande) {
        $sql = "
            SELECT c.*, u.nom, u.prenom, p.nom AS produitNom, p.prix, cmp.quantite
            FROM commande c
            JOIN utilisateur u ON c.idUtilisateur = u.id
            JOIN composer cmp ON cmp.idCommande = c.id
            JOIN produits p ON p.id = cmp.idProduit
            WHERE c.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCommande]);
        return $stmt->fetchAll();
    }
    
    
}
