<?php

$idImovel = (int) $_GET['id'];

$imoveis = json_decode(file_get_contents(__DIR__ . '/data/imoveis.json'), true);


$imovelEncontrado = null;

foreach ($imoveis as $imovel) {
    if ($imovel['id'] === $idImovel) {
        $imovelEncontrado = $imovel;
    }
}

    if($imovelEncontrado === null) {
        die("imovel não encontrado");
    }

$numeroLimpo = preg_replace('/[^0-9]/', '', $imovelEncontrado['contato']);

$numeroWhatsapp = '55' . $numeroLimpo;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="imoveis.css">
    <title><?= htmlspecialchars($imovelEncontrado['titulo']) ?> - Rezze Imóveis</title>
</head>
<body class="pagina-imoveis">

<nav class="navbar">
    <div class="site-name"><small>REZZE IMÓVEIS</small></div>
    <div class="nav-links">
        <a href="index.php">Início</a>
        <a href="imoveis.php" class="ativo">Imóveis</a>
        <a href="sobre.php">Sobre nós</a>
        <a href="#">Contato</a>
    </div>
</nav>

<main class="imoveis-main">

    <div class="card-imovel" style="max-width:500px; margin:0 auto;">
        <h2 class="card-titulo"><?= htmlspecialchars($imovelEncontrado['titulo']) ?></h2>
        <p class="card-local">
            <?= htmlspecialchars($imovelEncontrado['bairro']) ?> · <?= htmlspecialchars($imovelEncontrado['cidade']) ?>
        </p>
        <p class="card-valor">R$ <?= htmlspecialchars($imovelEncontrado['valor']) ?></p>
        <p class="card-descricao"><?= htmlspecialchars($imovelEncontrado['descricao']) ?></p>

        <a href="https://wa.me/<?= $numeroWhatsapp ?>" target="_blank" class="botao-card">
            Falar no WhatsApp
        </a>
    </div>

    <p style="text-align:center; margin-top:24px;">
        <a href="imoveis.php">&larr; Voltar para todos os imóveis</a>
    </p>

</main>

</body>
</html>
