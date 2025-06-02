<?php
class Produit {
    private $db;
    private $select;
    private $selectById;
    private $selectIn;

    private $insert;
    private $update;
    private $delete;
    private $search;        

    public function __construct(PDO $db) {
        $this->db = $db;

        
        $this->select = $db->prepare("
          SELECT 
            p.id, p.nom, p.description, p.img, p.prix, p.quantite,
            p.idType, t.libelle AS typeLibelle
          FROM produits p
          LEFT JOIN type t ON p.idType = t.id
          ORDER BY p.nom
        ");
        $this->selectById = $db->prepare("
          SELECT 
            p.id, p.nom, p.description, p.img, p.prix, p.quantite,
            p.idType, t.libelle AS typeLibelle
          FROM produits p
          LEFT JOIN type t ON p.idType = t.id
          WHERE p.id = :id
        ");
        $this->selectIn = $db->prepare("
            SELECT 
                p.id, p.nom, p.description, p.img, p.prix, p.quantite,
                p.idType, t.libelle AS typeLibelle
            FROM produits p
            LEFT JOIN type t ON p.idType = t.id
            WHERE FIND_IN_SET(p.id, :ids)
            ");

        $this->search = $db->prepare("
          SELECT 
            p.id, p.nom, p.description, p.img, p.prix, p.quantite,
            p.idType, t.libelle AS typeLibelle
          FROM produits p
          LEFT JOIN type t ON p.idType = t.id
          WHERE p.nom LIKE :mot
             OR p.description LIKE :mot
          ORDER BY p.nom
        ");
        $this->insert = $db->prepare("
          INSERT INTO produits (nom, description, img, idType, prix, quantite)
          VALUES (:nom, :description, :img, :idType, :prix, :quantite)
        ");
        $this->update = $db->prepare("
          UPDATE produits
          SET nom        = :nom,
              description= :description,
              img        = :img,
              idType     = :idType,
              prix       = :prix,
              quantite   = :quantite
          WHERE id = :id
        ");
        $this->delete = $db->prepare("DELETE FROM produits WHERE id = :id");
    }

    public function select() {
        $sql = "
            SELECT p.*, t.libelle AS typeLibelle
            FROM produits p
            LEFT JOIN type t ON p.idType = t.id
            ORDER BY p.nom
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    

    public function selectById(int $id): ?array {
        $this->selectById->execute([':id' => $id]);
        $row = $this->selectById->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public function selectIn(array $ids): array {
        if (count($ids) === 0) return [];
    
        $idString = implode(',', $ids);
        $this->selectIn->execute([':ids' => $idString]);
        return $this->selectIn->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function recherche(string $q): array {
        $mot = '%' . $q . '%';
        $this->search->execute([':mot' => $mot]);
        return $this->search->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($nom, $description, $img, $idType, $prix, $quantite): bool {
        $this->insert->execute([
          ':nom'         => $nom,
          ':description' => $description,
          ':img'         => $img,
          ':idType'      => $idType ?: null,
          ':prix'        => $prix,
          ':quantite'    => $quantite
        ]);
        return $this->insert->errorCode() === '00000';
    }

    public function update($id, $nom, $description, $img, $idType, $prix, $quantite): bool {
        $this->update->execute([
          ':id'          => $id,
          ':nom'         => $nom,
          ':description' => $description,
          ':img'         => $img,
          ':idType'      => $idType ?: null,
          ':prix'        => $prix,
          ':quantite'    => $quantite
        ]);
        return $this->update->errorCode() === '00000';
    }

    public function delete(int $id): bool {
        $this->delete->execute([':id' => $id]);
        return $this->delete->errorCode() === '00000';
    }

    public function selectByTypeNom($libelle) {
        $sql = "SELECT p.* FROM produits p
                JOIN type t ON p.idType = t.id
                WHERE LOWER(t.libelle) = LOWER(:libelle)";
        $req = $this->db->prepare($sql);
        $req->execute([':libelle' => $libelle]);
        return $req->fetchAll();
    }
    
    
    
}
