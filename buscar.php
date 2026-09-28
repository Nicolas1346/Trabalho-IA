<?php

require_once __DIR__ . '/config.php';

$pedidoUsuario = $_POST['pedido'];
$pedidoUsuario = trim($pedidoUsuario);

if (empty($pedidoUsuario)) {
    die("Esse espaco não pode ficar em branco!");
}

$imoveis = json_decode(file_get_contents(__DIR__ . '/data/imoveis.json'), true);

$catalogoTexto = json_encode($imoveis);


$mensagemSistema = "Você é um assistente de uma imobiliária. Você recebe um catálogo de "
    . "imóveis em formato JSON e o pedido de um cliente em texto livre. Analise o catálogo "
    . "e recomende os imóveis que mais combinam com o pedido, explicando o motivo de cada "
    . "recomendação. Se nenhum imóvel combinar bem, diga isso claramente.\n\n"
    . "Catálogo de imóveis (JSON):\n" . $catalogoTexto;


$corpoRequisicao = [
    'model' => HF_MODELO,
    'messages' => [
        ['role' => 'system', 'content' => $mensagemSistema],
        ['role' => 'user', 'content' => $pedidoUsuario],
    ],
    'max_tokens' => 1200,
    'temperature' => 0.3,
];

$ch = curl_init(HF_ENDPOINT);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,          
    CURLOPT_POST => true,                     
    CURLOPT_HTTPHEADER => [                   
        'Content-Type: application/json',
        'Authorization: Bearer ' . HF_TOKEN,
    ],
    CURLOPT_POSTFIELDS => json_encode($corpoRequisicao, JSON_UNESCAPED_UNICODE), 
    CURLOPT_TIMEOUT => 30,
]);


$respostaBruta = curl_exec($ch);
$erroCurl = curl_error($ch);
$codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


if ($respostaBruta === false) {
    die("Erro ao conectar com a API do Hugging Face: " . $erroCurl);
}


$dadosResposta = json_decode($respostaBruta, true);


if ($codigoHttp !== 200) {
    $mensagemErro = $dadosResposta['error']['message'] ?? $dadosResposta['error'] ?? 'Erro desconhecido.';
    die("Erro da API (HTTP {$codigoHttp}): {$mensagemErro}");
}

// 9) Extrai o texto de resposta gerado pela IA
$respostaIA = $dadosResposta['choices'][0]['message']['content'] ?? null;

if ($respostaIA === null) {
    die("A API respondeu, mas não foi possível interpretar o conteúdo retornado.");
}

$respostaIA = preg_replace('/<think>.*?<\/think>/s', '', $respostaIA);
$respostaIA = trim($respostaIA);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="imoveis.css">
    <title>Resultado da busca - Rezze Imóveis</title>
</head>
<body class="pagina-imoveis">

<nav class="navbar">
    <div class="site-name"><small>REZZE IMÓVEIS</small></div>
    <div class="nav-links">
        <a href="index.php">Início</a>
        <a href="imoveis.php" class="ativo">Imóveis</a>
        <a href="#">Sobre nós</a>
        <a href="#">Contato</a>
    </div>
</nav>

<main class="imoveis-main">
    <h2 class="titulo-secao">Resultado da busca</h2>
    <p class="subtitulo-secao">Você procurou: "<?= htmlspecialchars($pedidoUsuario) ?>"</p>

    <div style="background:#fff; border-radius:14px; padding:24px; box-shadow:0 4px 18px rgba(0,0,0,0.06);">
        <?= nl2br(htmlspecialchars($respostaIA)) ?>
    </div>

    <p style="margin-top:24px;"><a href="imoveis.php">&larr; Voltar para todos os imóveis</a></p>
</main>

</body>
</html>
