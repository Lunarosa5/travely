<?php
// perfil.php: Mostra as informações da conta do usuário (Nome de usuário, email, senha escondida), permite acessar a tela de atualização do perfil e a opçao de excluir a conta.

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';

// Se a página recebeu a requisição POST e (&&) o campo acao foi enviado e (&&) o valor do campo é igual a (==) excluir_conta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'excluir_conta') {

    // a função de excluir_conta é chamada identificando qual usuário esta solicitando a exclusão.
    excluir_conta($conexao, $_SESSION['id_usuario']);
}

// a função buscar_usuario é chamada fornecendo acesso ao banco de dados ($conexao) e informando qual usuário deve ser procurado, onde o resultado é armazenado em $usuario.
$usuario = buscar_usuario($conexao, $_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-perfil">
            <h1 class="titulo-pagina">Meu Perfil</h1>

            <div class="card-perfil-container">

                <!-- Foto do Perfil -->
                <div class="avatar-circulo">

                    <!-- Exibe uma imagem de perfil padrão do sistema -->
                    <img src="../images/foto_perfil.png" alt="Foto de Perfil" class="foto-perfil-redonda">
                </div>

                <div class="form-perfil-campos">

                    <!-- Nome de usuário -->
                    <div class="campo-perfil-item">
                        <label>Nome:</label>

                        <!-- Se o nome de usuário existe exibe o nome dele, caso não exista deixe em branco. readonly impede a edição desse campo. -->
                        <input type="text" value="<?php if (isset($usuario['nome'])) {
                                                        echo $usuario['nome'];
                                                    } else {
                                                        echo '';
                                                    } ?>" readonly class="input-perfil-pino">

                    </div>

                    <!-- Email do usuário -->
                    <div class="campo-perfil-item">
                        <label>Email:</label>

                        <!-- Se o email do usuário existe exibe o email dele, caso não exista deixe em branco. readonly impede a edição desse campo. -->
                        <input type="email" value="<?php if (isset($usuario['email'])) {
                                                        echo $usuario['email'];
                                                    } else {
                                                        echo '';
                                                    } ?>" readonly class="input-perfil-pino">

                    </div>

                    <!-- Senha do usuário -->
                    <div class="campo-perfil-item">
                        <label>Senha:</label>

                        <!-- Mostra o conteúdo como fixo exibindo asteriscos (*) para "esconder" a senha. readonly impede a edição desse campo. -->
                        <input type="password" value="********" readonly class="input-perfil-pino">
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="container-botoes-perfil">

                    <!-- Cria o formulário com o método POST. Executa uma função do JavaScript quando o formulário é enviado e mostra uma caixa de confirmação na tela com as opções de cancelar ou confirmar. -->
                    <form action="" method="POST"
                        onsubmit="return confirm('Tem certeza de que deseja excluir sua conta? Esta ação apagará todos os seus relatos e não poderá ser desfeita!');" class="form-excluir-inline">

                        <!-- Campo invisível que envia o a acao para o PHP saber exatamente o que fazer, ou seja, excluir a conta -->
                        <input type="hidden" name="acao" value="excluir_conta">
                        <input type="submit" value="Excluir" class="btn-perfil-acao btn-perfil-excluir">
                    </form>

                    <!-- Cria um link para a tela onde o usuário poderá atualizar suas informações -->
                    <a href="../login/atualizar_perfil.php" class="btn-perfil-acao btn-perfil-atualizar">Atualizar</a>
                </div>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>

</html>