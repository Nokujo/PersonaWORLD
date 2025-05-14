<?php
class Upload {
    private array  $exts;
    private string $dir;
    private int    $maxSize;

    public function __construct(array $extensions, string $directory, int $maxSize) {
        $this->exts    = $extensions;
        $this->dir     = $directory;
        $this->maxSize = $maxSize;
    }

    /**
     * @return array{nom: string|null, erreur: string|null}
     */
    public function enregistrer(string $field): array {
        $res = ['nom'=>null,'erreur'=>null];
        if (empty($_FILES[$field]['name'])) {
            return $res;
        }

        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->exts, true)) {
            $res['erreur'] = 'Extensions autorisées : '.implode(', ', $this->exts);
            return $res;
        }
        if ($_FILES[$field]['size'] > $this->maxSize) {
            $res['erreur'] = 'Fichier trop volumineux (max '.$this->maxSize.' octets).';
            return $res;
        }

        $name = uniqid('', true).'.'.$ext;
        $dst  = rtrim($this->dir, '/').'/'.$name;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dst)) {
            $res['erreur'] = 'Erreur de transfert.';
            return $res;
        }
        chmod($dst, 0644);
        $res['nom'] = $name;
        return $res;
    }
}
