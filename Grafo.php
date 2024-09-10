<?php

define('MAX_NODES', 100);

require_once 'Aresta.php';
require_once 'No.php';

class Grafo {
    public $nos = [];
    public $direcional;
    public $ponderado;
    public $numeroNos;
    public $visitado = [];
    public $descoberta = [];
    public $finalizacao = [];
    public $tempo;

    public function __construct($direcional, $ponderado, $numeroNos) {
        $this->direcional = $direcional;
        $this->ponderado = $ponderado;
        $this->numeroNos = $numeroNos;
        $this->tempo = 0;

        for ($i = 0; $i < $numeroNos; $i++) {
            $this->nos[$i] = new No(chr(ord('A') + $i));
            $this->visitado[$i] = 0;
            $this->descoberta[$i] = 0;
            $this->finalizacao[$i] = 0;
        }
    }

    public function adicionarAresta($origem, $destino, $peso) {
        $novaAresta = new Aresta($destino, $peso);
        $novaAresta->proxima = $this->nos[$origem]->arestas;
        $this->nos[$origem]->arestas = $novaAresta;

        if (!$this->direcional) {
            $novaArestaReversa = new Aresta($origem, $peso);
            $novaArestaReversa->proxima = $this->nos[$destino]->arestas;
            $this->nos[$destino]->arestas = $novaArestaReversa;
        }
    }

    public function imprimirGrafo() {
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo $this->nos[$i]->nome . ' ';
            $aresta = $this->nos[$i]->arestas;
            while ($aresta != null) {
                if ($this->ponderado) {
                    echo '(' . $this->nos[$aresta->destino]->nome . ': ' . $aresta->peso . ') ';
                } else {
                    echo '(-> ' . $this->nos[$aresta->destino]->nome . ') ';
                }
                $aresta = $aresta->proxima;
            }
            echo "\n";
        }
    }

    public function dfs($no, &$ordem, &$indexOrdem) {
        $this->visitado[$no] = 1;
        $this->tempo++;
        $this->descoberta[$no] = $this->tempo;

        $aresta = $this->nos[$no]->arestas;
        while ($aresta != null) {
            if (!$this->visitado[$aresta->destino]) {
                $this->dfs($aresta->destino, $ordem, $indexOrdem);
            }
            $aresta = $aresta->proxima;
        }

        $this->tempo++;
        $this->finalizacao[$no] = $this->tempo;
        $ordem[$indexOrdem--] = $this->nos[$no]->nome;
    }

    public function ordenacaoTopologica() {
        $ordem = array_fill(0, MAX_NODES, null);
        $indexOrdem = $this->numeroNos - 1;

        for ($i = 0; $i < $this->numeroNos; $i++) {
            if (!$this->visitado[$i]) {
                $this->dfs($i, $ordem, $indexOrdem);
            }
        }

        echo "\nOrdenacao Topologica: ";
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo $ordem[$i] . ' ';
        }
        echo "\n";
    }

    public function imprimirDescobertaFinalizacao() {
        echo "\nOrdem de Descoberta/Finalizacao:\n";
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo $this->nos[$i]->nome . ' (' . $this->descoberta[$i] . '/' . $this->finalizacao[$i] . ")\n";
        }
    }

    public function transporGrafo() {
        $grafoT = new Grafo($this->direcional, $this->ponderado, $this->numeroNos);
        for ($i = 0; $i < $this->numeroNos; $i++) {
            $aresta = $this->nos[$i]->arestas;
            while ($aresta != null) {
                $grafoT->adicionarAresta($aresta->destino, $i, $aresta->peso);
                $aresta = $aresta->proxima;
            }
        }
        return $grafoT;
    }

    public function encontrarComponentesFortementeConectados() {
        $ordem = array_fill(0, MAX_NODES, null);
        $indexOrdem = $this->numeroNos - 1;
    
        // DFS para ordem de finalização
        for ($i = 0; $i < $this->numeroNos; $i++) {
            $this->visitado[$i] = 0;
        }
    
        for ($i = 0; $i < $this->numeroNos; $i++) {
            if (!$this->visitado[$i]) {
                $this->dfs($i, $ordem, $indexOrdem);
            }
        }
        $grafoT = $this->transporGrafo();
    
        // DFS no grafo transposto
        echo "\nComponentes Fortemente Conectados:\n";
        for ($i = 0; $i < $grafoT->numeroNos; $i++) {
            $grafoT->visitado[$i] = 0; // Reseta visitação no grafo transposto
        }
    
        // Reversão
        for ($i = $this->numeroNos - 1; $i >= 0; $i--) {
            $no = ord($ordem[$i]) - ord('A');
            if (!$grafoT->visitado[$no]) {
                echo "{ ";
                // Re-inicializar indexOrdem
                $indexOrdem = $grafoT->numeroNos - 1;
                $this->dfsComponentes($grafoT, $no);
                echo "}\n";
            }
        }
    }
    private function dfsComponentes(Grafo $grafo, $no) {
        $grafo->visitado[$no] = 1;
        echo $grafo->nos[$no]->nome . ' ';
    
        $aresta = $grafo->nos[$no]->arestas;
        while ($aresta != null) {
            if (!$grafo->visitado[$aresta->destino]) {
                $this->dfsComponentes($grafo, $aresta->destino);
            }
            $aresta = $aresta->proxima;
        }
    }
    public function dijkstra($inicio, $fim) {
        $distancias = array_fill(0, $this->numeroNos, INF);
        $predecessores = array_fill(0, $this->numeroNos, null);
        $visitado = array_fill(0, $this->numeroNos, false);
    
        $distancias[$inicio] = 0;
        $fila = [];
        for ($i = 0; $i < $this->numeroNos; $i++) {
            $fila[$i] = $distancias[$i];
        }
    
        while (!empty($fila)) {
            $u = array_search(min($fila), $fila);
            unset($fila[$u]);
    
            if ($u === $fim) {
                break;
            }
    
            $visitado[$u] = true;
            $aresta = $this->nos[$u]->arestas;
            while ($aresta != null) {
                $v = $aresta->destino;
                if (!$visitado[$v]) {
                    $novaDistancia = $distancias[$u] + $aresta->peso;
                    if ($novaDistancia < $distancias[$v]) {
                        $distancias[$v] = $novaDistancia;
                        $predecessores[$v] = $u;
                        $fila[$v] = $novaDistancia;
                    }
                }
                $aresta = $aresta->proxima;
            }
    
            $this->imprimirTabelaControle($distancias, $predecessores, $u);
        }
    
        $this->imprimirCaminhoMin($predecessores, $inicio, $fim);
    }
    
    private function imprimirTabelaControle($distancias, $predecessores, $u) {
        echo "\nTabela após visitação do nó $u:\n";
        echo "u ";
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo $i . " ";
        }
        echo "\n";
        echo "dist ";
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo ($distancias[$i] == INF ? "INF" : $distancias[$i]) . " "; //infinito
        }
        echo "\n";
        echo "predecessor ";
        for ($i = 0; $i < $this->numeroNos; $i++) {
            echo ($predecessores[$i] === null ? "-" : $predecessores[$i]) . " "; // adota valor
        }
        echo "\n";
    }
    
    private function imprimirCaminhoMinimo($predecessores, $inicio, $fim) {
        $caminho = [];
        for ($atual = $fim; $atual !== null; $atual = $predecessores[$atual]) {
            array_unshift($caminho, $atual);
        }
    
        if ($caminho[0] == $inicio) {
            echo "\nCaminho mínimo de $inicio a $fim: ";
            foreach ($caminho as $no) {
                echo $no . " ";
            }
            echo "\n";
        } else {
            echo "\nNão há caminho de $inicio a $fim.\n";
        }
    }
    private function imprimirCaminhoMin($predecessores, $inicio, $fim) {
        // Tratamento para caso não houver caminho
        if (!isset($predecessores[$fim]) || $predecessores[$fim] === null) {
            echo "Nenhum caminho encontrado do nó $inicio ao nó $fim.\n";
            return;
        }        
    
        // Reconstrói o caminho do fim até o início
        $caminho = [];
        $atual = $fim;
        while ($atual !== null) {
            array_unshift($caminho, $atual); 
            $atual = $predecessores[$atual]; 
        }
    
        // Exibe o caminho mínimo encontrado
        echo "Caminho mínimo do nó $inicio ao nó $fim: " . implode(" -> ", $caminho) . "\n";
    }
    
    private function distanciaEuclidiana($x1, $y1, $x2, $y2) {
        return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    }
    
    public function aEstrela($inicio, $fim, $coordenadas) {
        // Inicialização das listas aberta e fechada, e os arrays de custos e predecessores
        $aberta = [];
        $fechada = [];
        $g = array_fill(0, $this->numeroNos, INF); // Custo do caminho já percorrido (g)
        $f = array_fill(0, $this->numeroNos, INF); // f = g + h (h = heurística)
        $predecessores = array_fill(0, $this->numeroNos, null);

        $g[$inicio] = 0;
        $f[$inicio] = $this->distanciaEuclidiana($coordenadas[$inicio][0], $coordenadas[$inicio][1], 
                                                 $coordenadas[$fim][0], $coordenadas[$fim][1]);
        $aberta[$inicio] = $f[$inicio];
    
        while (!empty($aberta)) {
            $u = array_search(min($aberta), $aberta);
            unset($aberta[$u]);
    
            // Adiciona o nó à lista fechada
            $fechada[$u] = true;
    
            // Verifica se o nó atual é o destino
            if ($u === $fim) {
                break; // Encerra se caminho encontrado
            }

            $aresta = $this->nos[$u]->arestas;
            while ($aresta != null) {
                $v = $aresta->destino;
    
                // If !Nó [lista], fazer tratamento para pegar o peso arestas
                if (!isset($fechada[$v])) {
                    $gNovo = $g[$u] + $aresta->peso;
                    $h = $this->distanciaEuclidiana($coordenadas[$v][0], $coordenadas[$v][1], 
                                                    $coordenadas[$fim][0], $coordenadas[$fim][1]); // Heurística
                    $fNovo = $gNovo + $h;
    
                    if (!isset($aberta[$v]) || $fNovo < $f[$v]) {
                        $g[$v] = $gNovo;
                        $f[$v] = $fNovo;
                        $predecessores[$v] = $u;
                        $aberta[$v] = $fNovo; 
                    }
                }
    
                $aresta = $aresta->proxima;
            }
    
            // resultado
            $this->imprimirListasAbertasFechadas($aberta, $fechada, $u);
        }
    
        // resultado caminho minimo para esse A*
        $this->imprimirCaminhoMin($predecessores, $inicio, $fim);
    }
    
    private function imprimirListasAbertasFechadas($aberta, $fechada, $atual) {
        echo "Inserindo nó {$atual} na lista fechada\n";
    
        echo "Lista Aberta: ";
        foreach ($aberta as $no => $f) {
            echo "($no: f=$f) ";
        }
        echo "\n";

        echo "Lista Fechada: ";
        foreach ($fechada as $no => $valor) {
            if ($valor) {
                echo "$no ";
            }
        }
        echo "\n";
    }    
}

?>