<?php
// cadastro.php: interface de cadastro

// A sessão é iniciada
session_start();

// Carrega os arquivos necessários para conectar ao bcd e executar a função do cadastro.
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faça seu cadastro - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-cadastro">

    <!-- Lado Esquerdo - Formulário (Metade da Tela) -->
    <div class="cadastro-lado-form">

        <a href="../index.php" class="cadastro-botao-voltar">⬅ Voltar</a>

        <div class="cadastro-conteudo-central">
            <h2>Faça seu cadastro</h2>

            <form action="" method="POST">

                <div class="cadastro-campo">

                    <!-- Nome de usuário, preenchimento necessário -->
                    <label for="nome">Nome de usuario:</label>
                    <input type="text" name="nome" id="nome" placeholder="Insira seu nome" required>
                </div>

                <div class="cadastro-campo">

                    <!-- E-mail do usuário, preenchimento necessário -->
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" placeholder="Insira seu email" required>
                </div>

                <div class="cadastro-campo">

                    <!-- Senha do usuário, preenchimento necessário-->
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" placeholder="Crie uma senha" required>
                </div>

                <div class="cadastro-container-submit">
                    <input type="submit" value="Cadastrar" class="cadastro-botao-submit">
                </div>

            </form>

            <?php

            // verifica se o formulário foi enviado (usuário clicou no botão) antes de tentar cadastrar o usuário.
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                // Se sim, a função recebe os argumentos e executa a função cadastrar_user
                if (cadastrar_user($conexao, $_POST['nome'], $_POST['email'], $_POST['senha'])) {

                    // Em caso de sucesso, o usuário é redirecionado à página inicial (inicio.php)
                    header("Location: ../pages/inicio.php");
                    exit();
                }
            }
            ?>
        </div>
    </div>

    <!-- Lado Direito - Imagem -->
    <div class="cadastro-lado-imagem"></div>

</body>

</html>
