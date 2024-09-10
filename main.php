<?php

require_once 'Grafo.php';

function main() {
    $tipoGrafo = (int)readline("Escolha o tipo de grafo:\n1 - Direcional nao ponderado\n2 - Nao direcional ponderado\n");
    $numeroNos = (int)readline("Digite o numero de nos do grafo: ");

    $grafo = new Grafo($tipoGrafo == 1, $tipoGrafo == 2, $numeroNos);

    while (true) {
        $origem = (int)readline("Digite o no de origem (0 para sair): ");
        if ($origem == 0) break;

        $destino = (int)readline("Digite o no de destino: ");

        if ($grafo->ponderado) {
            $peso = (int)readline("Digite o peso da aresta: ");
        } else {
            $peso = 1;
        }

        $grafo->adicionarAresta($origem - 1, $destino - 1, $peso);
    }

    echo "\nGrafo criado:\n";
    $grafo->imprimirGrafo();

    if ($grafo->direcional && !$grafo->ponderado) {
        $grafo->ordenacaoTopologica();
        $grafo->imprimirDescobertaFinalizacao();
        $grafo->encontrarComponentesFortementeConectados();
    }

    // Coletar dados para Dijkstra
    $inicioDijkstra = (int)readline("Digite o nó de início para Dijkstra (1 a $numeroNos): ") - 1;
    $fimDijkstra = (int)readline("Digite o nó de fim para Dijkstra (1 a $numeroNos): ") - 1;

    echo "\nExecutando o Algoritmo de Dijkstra:\n";
    $grafo->dijkstra($inicioDijkstra, $fimDijkstra);

    // Coletar dados para A*
    $inicioAEstrela = (int)readline("Digite o nó de início para A* (1 a $numeroNos): ") - 1;
    $fimAEstrela = (int)readline("Digite o nó de fim para A* (1 a $numeroNos): ") - 1;

    $coordenadas = [];
    echo "\nDigite as coordenadas dos nós:\n";
    for ($i = 0; $i < $numeroNos; $i++) {
        $x = (float)readline("Coordenada x do nó " . ($i + 1) . ": ");
        $y = (float)readline("Coordenada y do nó " . ($i + 1) . ": ");
        $coordenadas[$i] = [$x, $y];
    }

    echo "\nExecutando o Algoritmo A*:\n";
    $grafo->aEstrela($inicioAEstrela, $fimAEstrela, $coordenadas);
}

main();

?>
