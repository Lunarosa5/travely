
<?php
session_start();

require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';

$id_usuario = $_SESSION['id_usuario'];
?><!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Perfil - Travely</title>
    
    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-perfil">
            <h1 class="titulo-pagina">Atualizar Meu Perfil</h1>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                $senha_atual = $_POST['senha_atual'];
                $senha_nova = $_POST['senha_nova'];

                // Envolve o retorno da função no container-mensagem
                echo "<div class='container-mensagem'>";
                atualizar_perfil($conexao, $id_usuario, $nome, $email, $senha_atual, $senha_nova);
                echo "</div>";
            }

            // Busca os dados do usuário para preencher os campos do formulário
            $usuario = buscar_usuario($conexao, $id_usuario);
            ?>

            <div class="card-perfil-container">
                <!-- Foto do Perfil Redonda -->
                <div class="avatar-circulo">
                    <img src="../images/foto_perfil.png" alt="Foto de Perfil" class="foto-perfil-redonda">
                </div>

                <form action="" method="POST" class="form-perfil-campos">
                    <!-- Nome -->
                    <div class="campo-perfil-item">
                        <label for="nome">Insira o nome de usuário:</label>
                        <input type="text" name="nome" id="nome" value="<?= ($_POST['nome'] ?? $usuario['nome']); ?>" required class="input-perfil-pino">
                    </div>

                    <!-- Email -->
                    <div class="campo-perfil-item">
                        <label for="email">Insira o email:</label>
                        <input type="email" name="email" id="email" value="<?= ($_POST['email'] ?? $usuario['email']); ?>" required class="input-perfil-pino">
                    </div>

                    <!-- Senha Atual -->
                    <div class="campo-perfil-item">
                        <label for="senha_atual">Insira sua senha atual:</label>
                        <input type="password" name="senha_atual" id="senha_atual" required class="input-perfil-pino">
                    </div>

                    <!-- Nova Senha -->
                    <div class="campo-perfil-item">
                        <label for="senha_nova">Insira uma nova senha (opcional):</label>
                        <input type="password" name="senha_nova" id="senha_nova" class="input-perfil-pino">
                    </div>

                    <!-- Botões de Ação Centralizados Embaixo -->
                    <div class="container-botoes-perfil">
                        <a href="../pages/perfil.php" class="btn-perfil-acao">Voltar</a>
                        <input type="submit" value="Salvar" class="btn-perfil-acao">
                    </div>
                </form>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>

</html>
