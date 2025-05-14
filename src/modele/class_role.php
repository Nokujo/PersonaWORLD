<?php
class Role {
    private $db;
    private $select;

    public function __construct($db) {
        $this->db     = $db;
        $this->select = $db->prepare("SELECT id, libelle FROM role");
    }

    public function select() {
        $this->select->execute();
        return $this->select->fetchAll();
    }
}
