<?php

class Aresta {
    public $destino;
    public $peso;
    public $proxima;

    public function __construct($destino, $peso) {
        $this->destino = $destino;
        $this->peso = $peso;
        $this->proxima = null;
    }
}

?>
