<?php
class Commande {
    private $db;
    private $insert;
    private $selectById;

    public function __construct($db) {
        $this->db = $db;

        $this->insert = $db->prepare("
            INSERT INTO commande (montant, dateCommande, idEtat, idUtilisateur)
            VALUES (:montant, :dateCommande, :idEtat, :idUtilisateur)
        ");

        $this->selectById = $db->prepare("
            SELECT * FROM commande WHERE id = :id
        ");
    }

    public function insert($montant, $dateCommande, $idEtat, $idUtilisateur): ?int {
        $this->insert->execute([
            ':montant' => $montant,
            ':dateCommande' => $dateCommande,
            ':idEtat' => $idEtat,
            ':idUtilisateur' => $idUtilisateur
        ]);

        if ($this->insert->errorCode() === '00000') {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }

    public function selectById(int $id): ?array {
        $this->selectById->execute([':id' => $id]);
        $row = $this->selectById->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function selectAll(): array {
        $sql = "
            SELECT c.*, e.libelle AS etatLibelle, u.nom, u.prenom
            FROM commande c
            JOIN etat e ON c.idEtat = e.id
            JOIN utilisateur u ON c.idUtilisateur = u.id
            ORDER BY c.dateCommande DESC
        ";
        $req = $this->db->prepare($sql);
        $req->execute();

        if ($req->errorCode() != '00000') {
            print_r($req->errorInfo());
        }

        return $req->fetchAll();
    }

    public function updateEtat($idCommande, $idEtat): bool {
        $sql = "UPDATE commande SET idEtat = :idEtat WHERE id = :idCommande";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idEtat' => $idEtat,
            ':idCommande' => $idCommande
        ]);
    }

    public function delete($id): bool {
        $sql = "DELETE FROM commande WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function selectWithProduits($idCommande): array {
        $sql = "
        SELECT c.id AS idCommande, c.idUtilisateur, c.dateCommande, c.montant, c.idEtat,
       u.nom, u.prenom,
       p.nom AS produitNom, p.prix, p.img, cmp.quantite

            FROM commande c
            JOIN utilisateur u ON c.idUtilisateur = u.id
            JOIN composer cmp ON cmp.idCommande = c.id
            JOIN produits p ON p.id = cmp.idProduit
            WHERE c.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCommande]);

        if ($stmt->errorCode() !== '00000') {
            print_r($stmt->errorInfo());
        }

        return $stmt->fetchAll();
    }
}
