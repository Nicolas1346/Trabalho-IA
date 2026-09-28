<?php

function carregarEnv(string $caminhoArquivo): void
{
    if (!file_exists($caminhoArquivo)) {
        return;
    }

    $linhas = file($caminhoArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        if ($linha === '' || str_starts_with($linha, '#')) {
            continue;
        }

        if (str_contains($linha, '=')) {
            [$chave, $valor] = explode('=', $linha, 2);
            putenv(trim($chave) . '=' . trim($valor));
        }
    }
}

carregarEnv(__DIR__ . '/.env');

define('HF_TOKEN', getenv('HF_TOKEN') ?: '');
define('HF_MODELO', 'Qwen/Qwen3-32B');
define('HF_ENDPOINT', 'https://router.huggingface.co/v1/chat/completions');
