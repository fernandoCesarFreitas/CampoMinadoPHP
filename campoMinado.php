<?php
session_start();

header('Content-Type: application/json');

if (isset($_POST['linha']) && isset($_POST['coluna'])) {
    $linha = $_POST['linha'];
    $coluna = $_POST['coluna'];
    processarJogada($linha, $coluna);
}

if (isset($_POST['reiniciar'])) {
    unset($_SESSION['matriz']);
    unset($_SESSION['jogo_iniciado']);
    iniciarJogo();
}

$estadoJogo = array(
    'matriz' => $_SESSION['matriz'],
    'jogo_iniciado' => $_SESSION['jogo_iniciado']
);

echo json_encode($estadoJogo, JSON_UNESCAPED_UNICODE);
exit;

function iniciarJogo()
{
    session_unset(); 
    session_destroy(); 
    session_start(); 
    $_SESSION['matriz'] = gerarMatrizAleatoria(8, 8, 9, "*");
    $_SESSION['jogo_iniciado'] = true;
    unset($_SESSION['jogo_encerrado']);
}

function processarJogada($linha, $coluna)
{
    if (!$_SESSION['jogo_iniciado']) {
        return;
    }

    if ($linha < 0 || $linha >= count($_SESSION['matriz']) || $coluna < 0 || $coluna >= count($_SESSION['matriz'][0])) {
        return;
    }

    if ($_SESSION['matriz'][$linha][$coluna] !== " " && $_SESSION['matriz'][$linha][$coluna] !== "*") {
        return;
    }

    if ($_SESSION['matriz'][$linha][$coluna] == "*") {
        $_SESSION['jogo_iniciado'] = false;
        $_SESSION['jogo_encerrado'] = true;
        revelarTodasCelulas($_SESSION['matriz']);
    } else {
        $bombasAoRedor = contarBombasAoRedor($_SESSION['matriz'], $linha, $coluna);
        $_SESSION['matriz'][$linha][$coluna] = (string)$bombasAoRedor;

        if ($bombasAoRedor == 0) {
            revelarAdjacencias($_SESSION['matriz'], $linha, $coluna);
        }
    }

    if ($_SESSION['jogo_encerrado']) {
        $estadoJogo = array(
            'jogo_encerrado' => true,
            'mensagem' => 'Você perdeu! Encontrou uma bomba.',
            'matriz' => $_SESSION['matriz'],
        );
        iniciarJogo();
    } else {
        $estadoJogo = array(
            'jogo_encerrado' => false,
            'matriz' => $_SESSION['matriz'],
        );
    }
    $estadoJogo['debug'] = 'Processado com sucesso';

    echo json_encode($estadoJogo, JSON_UNESCAPED_UNICODE);
    exit;
}
function revelarTodasCelulas(&$matriz)
{
    foreach ($matriz as &$linha) {
        foreach ($linha as &$celula) {
            $celula = $celula == '*' ? "\u{1F4A3}" : "\u{1F332}"; 
        }
    }
}


function gerarMatrizAleatoria($linhas, $colunas, $numBombas, $caracterBomba)
{
    // Cria uma matriz vazia
    $matriz = array_fill(0, $linhas, array_fill(0, $colunas, " "));

    // Obtém todas as posições possíveis na matriz
    $posicoes = [];
    foreach (range(0, $linhas - 1) as $i) {
        foreach (range(0, $colunas - 1) as $j) {
            $posicoes[] = [$i, $j];
        }
    }

    // Seleciona aleatoriamente as posições para as bombas
    $bombas = array_rand($posicoes, $numBombas);

    // Coloca as bombas nas posições selecionadas
    foreach ($bombas as $bomba) {
        list($linha, $coluna) = $posicoes[$bomba];
        $matriz[$linha][$coluna] = $caracterBomba;
    }

    return $matriz;
}




function contarBombasAoRedor($matriz, $linha, $coluna)
{
    $count = 0;
    for ($i = max(0, $linha - 1); $i <= min(count($matriz) - 1, $linha + 1); $i++) {
        for ($j = max(0, $coluna - 1); $j <= min(count($matriz[0]) - 1, $coluna + 1); $j++) {
            if ($matriz[$i][$j] == "*" && !($i == $linha && $j == $coluna)) {
                $count++;
            }
        }
    }
    return $count;
}
function revelarAdjacencias(&$matriz, $linha, $coluna)
{
    for ($i = max(0, $linha - 1); $i <= min(count($matriz) - 1, $linha + 1); $i++) {
        for ($j = max(0, $coluna - 1); $j <= min(count($matriz[0]) - 1, $coluna + 1); $j++) {
            if ($matriz[$i][$j] == " ") {
                $bombasAoRedor = contarBombasAoRedor($matriz, $i, $j);
                if ($bombasAoRedor == 0) {
                    $matriz[$i][$j] = "0";
                    revelarAdjacencias($matriz, $i, $j);
                } else {
                    $matriz[$i][$j] = (string)$bombasAoRedor;
                }
            }
        }
    }
}


