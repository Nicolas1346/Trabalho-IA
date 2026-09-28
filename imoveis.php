<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="imoveis.css">
    <title>Imóveis - Rezze Imóveis</title>
</head>
<body class="pagina-imoveis">

<nav class="navbar">

    <div class="site-name">
        <small>REZZE IMÓVEIS</small>
    </div>

    <div class="nav-links">
        <a href="index.php">Início</a>
        <a href="imoveis.php">Imóveis</a>
        <a href="sobre.php">Sobre nós</a>
    </div>

</nav>

<main class="imoveis-main">

    <h2 class="titulo-secao">Nossos Imóveis</h2>
    <p class="subtitulo-secao">Encontre o imóvel que combina com o seu próximo capítulo.</p>

<div class="form-busca">
    <form action="buscar.php" method="POST">
        <label for="pedido">O que você procura?</label>
        <input type="text" id="pedido" name="pedido">
        <button type="submit" class="botao-buscar">Buscar</button>
    </form>
</div>




    <section class="grade-imoveis">
        <?php
        $imoveis = json_decode(file_get_contents(__DIR__ . '/data/imoveis.json'), true);

        foreach ($imoveis as $imovel):
        ?>
            <article class="card-imovel">
                <div class="card-imovel-topo">
                    <span class="tag-tipo"><?= htmlspecialchars($imovel['tipo']) ?></span>
                    <span class="card-valor">R$ <?= number_format($imovel['valor'], 2, ',', '.') ?></span>
                </div>

                <h3 class="card-titulo"><?= htmlspecialchars($imovel['titulo']) ?></h3>
                <p class="card-local"><?= htmlspecialchars($imovel['bairro']) ?> · <?= htmlspecialchars($imovel['cidade']) ?></p>

                <ul class="card-detalhes">
                    <li><?= (int)$imovel['area_m2'] ?> m²</li>
                    <li><?= (int)$imovel['quartos'] ?> quarto(s)</li>
                    <li><?= (int)$imovel['vagas_garagem'] ?> vaga(s)</li>
                </ul>

                <p class="card-descricao"><?= htmlspecialchars($imovel['descricao']) ?></p>

    <a href="detalhe.php?id=<?= $imovel['id'] ?>" class="botao-card">Ver mais</a>
            </article>
        <?php endforeach; ?>
    </section>

</main>

</body>
</html>
