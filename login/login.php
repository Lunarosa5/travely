<?php 
// login.php: interface de login

// Inicia a sessão
session_start();

// Carrega arquivo de conexão com o banco de dados e o arquivo de funções, onde será utilizado para realizar o login.
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faça seu login - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="fundo-login">

    <!-- Lado Esquerdo - Formulário (Metade da Tela) -->
    <div class="login-lado-form">
    
        <a href="../index.php" class="login-botao-voltar">⬅ Voltar</a>

        <div class="login-conteudo-central">
            <h2>Faça o login</h2>
<!-- Processamento do formulário -->
<?php

            // Verifica se o usuário enviou o formulário (clicou no botão) antes de executar a função.
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // Executa a função que, no banco de dados, procura o usuário correspondente ao e-mail digitado e guarda o resultado em $usuario.
                $usuario = consulta_user($conexao, $_POST['email']);

                // Verifica se o usuário existe e se a senha (hash) digitada corresponde à senha (hash) armazenada.
                if ($usuario && password_verify($_POST['senha'], $usuario['senha'])) {
                    $_SESSION['id_usuario'] = $usuario['id'];
                    header("Location: ../pages/inicio.php");
                    exit(); // Após o login acontecer o usuário é redirecionado à página inicial (inicio.php).
                } else { // Em caso de erro, uma mensagem é exibida.
                    echo "<p class='alerta erro'>E-mail ou senha inválidos.</p>";
                }
            }
            ?>
            <form action="" method="POST">
                <div class="login-campo">

<!-- Cria um campo com tipo email para o usuário inserir seu e-mail, onde é exigido seu preenchimento. -->
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" placeholder="Insira seu email" required>
                </div>

                <div class="login-campo">
                    <!-- Cria um campo com tipo password para o usuário inserir sua senha, onde é exigido seu preenchimento. -->
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" placeholder="Insira sua senha" required>
                </div>

                <div class="login-container-submit">
                    <input type="submit" value="Entrar" class="login-botao-submit">
                </div>
            </form>

            <p class="login-texto-cadastrar">Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p> <!-- Link para a página de cadastro caso não tenha um ainda -->
        </div>

    </div>

    <!-- Lado Direito - Imagem -->
    <div class="login-lado-imagem"></div>

</body>
</html>