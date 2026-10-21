<?php
// editar_viagem: responsável por editar os dados de uma viagem já cadastrada no sistema.

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
require_once __DIR__ . '/../database/connect.php';

// Guarda o ID do usuário que está conectado
$id_usuario = $_SESSION['id_usuario'];

// Pega a data atual no formato ano-mês-dia e armazena em $hoje
$hoje = date('Y-m-d');

// Variável para guardar a mensagem de sucesso/erro
$mensagem = '';

// Pega o ID da viagem via URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: atualizar.php");
    exit();
}

$id_viagem = (int)$_GET['id'];

// Processa a atualização quando envia o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura a saída da função para não dar echo antes do HTML
    ob_start();
    atualizar(
        $conexao,
        $id_viagem,
        $_POST['destino'],
        $_POST['data_inicio'],
        $_POST['data_fim'],
        $_POST['avaliacao'],
        $_POST['relato'],
        $id_usuario
    );
    $mensagem = ob_get_clean(); // Guarda a mensagem gerada

    // Se selecionou foto nova, cadastra no banco
    if (isset($_FILES['imagem']) && !empty($_FILES['imagem']['name'][0])) {
        foto_viagem($conexao, $id_viagem, $_FILES['imagem']);
    }
}

// Busca os dados atualizados da viagem no banco
$sql = "SELECT * FROM viagens WHERE id = :id AND id_usuario = :id_usuario";
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':id', $id_viagem, PDO::PARAM_INT);
$stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viagem) {
    header("Location: atualizar.php");
    exit();
}
?><!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Viagem - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">

        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-form">
            <a href="atualizar.php" class="btn-voltar-link">⬅ Voltar</a>

            <h1 class="titulo-pagina">Editar dados da viagem</h1>

            <!-- Exibe a mensagem de feedback no lugar certo -->
            <?php if (!empty($mensagem)): ?>
                <div class="container-mensagem">
                    <p class="caixa-mensagem sucesso"><?= strip_tags($mensagem); ?></p>
                </div>
            <?php endif; ?>

            <div class="card-formulario">
                <form action="" method="post" enctype="multipart/form-data" class="form-viagem">

                    <div class="campo-form largura-total">
                        <label for="destino">Destino:</label>
                        <input type="text" name="destino" id="destino" value="<?= ($viagem['destino']); ?>" required>
                    </div>

                    <div class="campo-form metade">
                        <label for="data_inicio">Data de início:</label>
                        <input type="date" name="data_inicio" id="data_inicio" max="<?= $hoje; ?>" value="<?= $viagem['data_inicio']; ?>" required>
                    </div>

                    <div class="campo-form metade">
                        <label for="data_fim">Data de fim:</label>
                        <input type="date" name="data_fim" id="data_fim" max="<?= $hoje; ?>" value="<?= $viagem['data_fim']; ?>" required>
                    </div>

                    <div class="campo-form largura-total">
                        <label for="relato">Relato:</label>
                        <textarea name="relato" id="relato" rows="4" required><?= ($viagem['relato']); ?></textarea>
                    </div>

                    <div class="campo-form metade">
                        <label for="imagem">Adicionar foto extra (opcional):</label>
                        <input type="file" name="imagem[]" id="imagem" accept="image/*" multiple>
                    </div>

                    <div class="campo-form metade">
                        <label for="avaliacao">Nota (0 a 5):</label>
                        <input type="number" name="avaliacao" id="avaliacao" min="0" max="5" value="<?= $viagem['avaliacao']; ?>" required>
                    </div>

                    <div class="container-botoes">
                        <a href="atualizar.php" class="btn-form btn-limpar">Cancelar</a>
                        <input type="submit" value="Salvar Alterações" class="btn-form btn-salvar">
                    </div>
                </form>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>

    </div>
</body>

</html>
