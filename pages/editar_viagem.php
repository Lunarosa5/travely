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

// O código verifica se um ID de viagem válido foi enviado através da URL (ex: editar_viagem.php?id=12). Se o ID não existir ou estiver vazio, o código redireciona o usuário imediatamente de volta para a página atualizar.php e interrompe a execução com o exit(). Se estiver tudo certo, o ID é convertido em um número inteiro por segurança.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: atualizar.php");
    exit();
}

$id_viagem = (int)$_GET['id'];

// Antes mesmo de carregar o visual da página, o código vai ao banco de dados verificar se essa viagem realmente pertence ao usuário logado. Se a viagem for de outra pessoa ou não existir, o sistema barra o acesso imediatamente.
$sql = "SELECT * FROM viagens WHERE id = :id AND id_usuario = :id_usuario";
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':id', $id_viagem, PDO::PARAM_INT);
$stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

// Se a viagem não for encontrada ou não pertencer ao usuário logado, redireciona o usuário.
if (!$viagem) {
    header("Location: atualizar.php");
    exit();
}
?>
<!DOCTYPE html>
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

            <!-- Bloco responsável por processar a alteração e exibir a mensagem de resposta -->
            <div class="container-mensagem">
                <?php
                //  Quando o usuário clica em salvar, os dados novos são enviados. O código chama a função atualizar(), processa os novos dados (se houverem) e, logo em seguida, roda a consulta do banco de novo ($stmt->execute()). Isso garante que os campos do formulário atualizem na tela com os novos textos assim que a página recarregar.
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    // Chama a função atualizar para gravar as alterações no banco de dados
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

                    // Se selecionou uma ou mais fotos novas, salva no banco de dados
                    if (isset($_FILES['imagem']) && !empty($_FILES['imagem']['name'][0])) {
                        foto_viagem($conexao, $id_viagem, $_FILES['imagem']);
                    }

                    // Recarrega os dados da viagem no banco para atualizar os campos do formulário na tela
                    $stmt->execute();
                    $viagem = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                ?>
            </div>

            <div class="card-formulario">

<!-- O formulário exibe os dados que vieram do banco dentro de cada value="...". O parâmetro max="<?php // echo $hoje; ?>" nos campos de data impede que o usuário selecione um dia no futuro, forçando-o a escolher apenas datas passadas ou o dia de hoje. O atributo enctype possibilita o envio de arquivos de foto. -->
                <form action="" method="post" enctype="multipart/form-data" class="form-viagem">

                <!-- Campo de destino -->
                    <div class="campo-form largura-total">
                        <label for="destino">Destino:</label>
                        <input type="text" name="destino" id="destino" value="<?php echo ($viagem['destino']); ?>" required>
                    </div>

<!-- Campos de datas -->
                    <div class="campo-form metade">
                        <label for="data_inicio">Data de início:</label>
                        <input type="date" name="data_inicio" id="data_inicio" max="<?= $hoje; ?>" value="<?= $viagem['data_inicio']; ?>" required>
                    </div>

                    <div class="campo-form metade">
                        <label for="data_fim">Data de fim:</label>
                        <input type="date" name="data_fim" id="data_fim" max="<?= $hoje; ?>" value="<?= $viagem['data_fim']; ?>" required>
                    </div>

                    <!-- Campo de relato -->
                    <div class="campo-form largura-total">
                        <label for="relato">Relato:</label>
                        <textarea name="relato" id="relato" rows="4" required><?= ($viagem['relato']); ?></textarea>
                    </div>

                    <!-- Campo de foto extra -->
                    <div class="campo-form metade">
                        <label for="imagem">Adicionar foto extra (opcional):</label>
                        <input type="file" name="imagem[]" id="imagem" accept="image/*" multiple>
                    </div>

                    <!-- Campo de avaliação -->
                    <div class="campo-form metade">
                        <label for="avaliacao">Nota (0 a 5):</label>
                        <input type="number" name="avaliacao" id="avaliacao" min="0" max="5" value="<?= $viagem['avaliacao']; ?>" required>
                    </div>

                    <!-- Botões -->
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