<?php
class Type {
    private $db;
    private $select;
    private $selectById;
    private $update;
    private $insert;
    private $delete;


    public function __construct($db) {
    $this->db = $db;
    $this->select = $db->prepare("SELECT id, libelle FROM type");
    $this->selectById = $db->prepare("SELECT * FROM type WHERE id = :id");
    $this->update = $db->prepare("UPDATE type SET libelle = :libelle WHERE id = :id");
    $this->delete = $db->prepare("DELETE FROM type WHERE id = :id");
    $this->insert = $db->prepare("INSERT INTO type(libelle) VALUES (:libelle)");
}

    public function select() {
        $this->select->execute();
        return $this->select->fetchAll();
    }

    public function selectById($id) {
        $this->selectById->execute([':id' => $id]);
        return $this->selectById->fetch();
    }

    public function update($id, $libelle) {
        $this->update->execute([
            ':id'      => $id,
            ':libelle' => $libelle
        ]);
        return $this->update->errorCode() === '00000';
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

    public function insert($libelle) {
    $this->insert->execute([':libelle' => $libelle]);
    return $this->insert->errorCode() === '00000';
}
    
}
