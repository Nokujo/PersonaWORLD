<?php
class Composer {
    private $db;
    private $insert;

    public function __construct($db) {
        $this->db = $db;

        $this->insert = $db->prepare("
            INSERT INTO composer (idCommande, idProduit, quantite)
            VALUES (:idCommande, :idProduit, :quantite)
        ");
    }

    public function insert($idCommande, $idProduit, $quantite): bool {
        $this->insert->execute([
            ':idCommande' => $idCommande,
            ':idProduit' => $idProduit,
            ':quantite' => $quantite
        ]);
        return $this->insert->errorCode() === '00000';
    }
    public function selectByCommandeId($idCommande) {
        $sql = "SELECT c.idProduit, c.quantite, p.nom, p.prix, p.img
                FROM composer c
                JOIN produits p ON c.idProduit = p.id
                WHERE c.idCommande = :idCommande";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idCommande' => $idCommande]);
    
        if ($stmt->errorCode() != '00000') {
            print_r($stmt->errorInfo());
        }
    
        return $stmt->fetchAll();
    }
    
}
