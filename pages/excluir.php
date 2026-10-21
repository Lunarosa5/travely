<?php
// excluir.php: Permite que o usuário pesquise a viagem que deseja excluir e exibe as informações sobre as viagens do local pesquisado, além de possibilitar o usuário de escolher qual viagem ele irá excluir.

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Viagem - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-pesquisar">

            <!-- Título -->
            <h1 class="titulo-pagina">Apague o relato de uma viagem</h1>

            <div class="container-mensagem">
                <?php  // Se a página recebeu a requisição POST e (&&) o campo ação foi enviado (isset($_POST['acao'])) com o valor excluir ($_POST['acao'] == 'excluir'))
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'excluir') {

                    // A função excluir, responsável por excluir a viagem é chamada, informando qual viagem deve ser excluida, pelo ID da viagem, e qual usuário realiza a ação, pelo Id do usuário.
                    excluir($conexao, $_POST['id_viagem'], $_SESSION['id_usuario']);
                } ?>
            </div>

            <form action="" method="POST" class="form-pesquisa">
                <div class="campo-pesquisa-grupo">

                    <!-- Cria a barra de pesquisa para que após o usuário digitar o local e buscar, o local não suma, ou seja, em ($_POST['destino_busca'] ?? '') se o usuário digitou o destino, com o operador ?? ela retorará o destino, se não, deixará em branco a barra de pesquisa. -->
                    <input type="text" name="destino" id="destino" placeholder="Insira o local da viagem que deseja apagar" value="<?= ($_POST['destino'] ?? ''); ?>" required class="input-pesquisa">

                    <!-- Botão de buscar -->
                    <input type="submit" value="Buscar" class="btn-buscar">
                </div>
            </form>

            <div class="resultado-pesquisa-container">
                <?php

                // Se a requisição POST foi recebida e o campo destino não esta vazio
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['destino'])) {

                    // Procura no banco de dados as viagens correspondentes ao destino buscado através da função pesquisar_funcoes, e o resultado é armazenado em $viagens
                    $viagens = pesquisar_opcoes($conexao, $_POST['destino']);

                    // Se a variável $viagens não estiver vazia o sistema continua.
                    if (!empty($viagens)) {

                        // Para cada viagem como uma única viagem, o sistema buscará as fotos relacionadas ao seu ID.
                        foreach ($viagens as $viagem) {

                            // A função buscar_fotos_viagem busca as fotos de uma viagem pelo seu id e armazena em $fotos.
                            $fotos = buscar_fotos_viagem($conexao, $viagem['id']);

                            // Se a lista de $fotos não está vazia 
                            if (!empty($fotos)) {

                                // o caminho da imagem que será exibida será ../uploads/+ o nome (['nome_arquivo']) da primeira foto da lista ($fotos[0])
                                $caminho_imagem = '../uploads/' . $fotos[0]['nome_arquivo'];
                            } else {
                                // Se não tem fotos, o caminho da imagem exibida será ../images/sem_imagem.png, que é uma imagem padrão para viagens sem fotos salvas.
                                $caminho_imagem = '../images/sem_imagem.png';
                            }
                ?>
                            <div class="espaco-viagem">

                                <!-- Exibe a imagem de acordo com o caminho definido anteriormente -->
                                <div class="caixa-imagem">
                                    <img src="<?= ($caminho_imagem); ?>" alt="Foto de <?= ($viagem['destino']); ?>">
                                </div>

                                <!-- Exibe as informações das viagens -->
                                <div class="info-viagem">

                                    <!-- Exibe o titulo da viagem de acordo com o destino que esta armazenada no bcd -->
                                    <p class="titulo-local"><strong><?php echo ($viagem['destino']); ?></strong></p>

                                    <!-- Exibe a data de inicio da viagem de acordo com a data_inicio que esta armazenada no bcd -->
                                    <p>Data: <?php echo date('d/m/Y', strtotime($viagem['data_inicio'])); ?></p>

                                    <!-- Exibe a nota da viagem de acordo com a avaliacao que esta armazenada no bcd -->
                                    <p>Nota: <?php echo $viagem['avaliacao']; ?></p>

                                    <!-- Campo oculto que envia o ID da viagem selecionada para identificar qual registro deverá ser excluído. -->
                                    <form action="" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar este relato?');">

                                        <!-- Por ser uma função do JavaScript, a parte de confirmação da exclusão (onsubmit), campos escondidos são criados que enviam informações essenciais para o PHP para realizar a exclusão -->

                                        <!-- Campo invisível que envia o ID da viagem para o PHP saber exatamente qual registro apagar, por exemplo, ID = 12, ou seja, apagar viagem para São Paulo -->
                                        <input type="hidden" name="id_viagem" value="<?= $viagem['id']; ?>">

                                        <!-- Campo invisível que avisa o PHP qual ação deve ser tomada (neste caso, "excluir") -->
                                        <input type="hidden" name="acao" value="excluir">

                                        <!-- Botão visível na tela que o usuário clica para enviar o formulário e excluir a viagem escolhida -->

                                        <input type="submit" value="Excluir" class="btn-ver-mais">
                                    </form>
                                </div>
                            </div>
                <?php
                        }
                    }

                    // Se a variável $viagens estiver vazia o sistema exibe uma mensagem.
                    else {
                        echo "<p class='sem-viagens'>Nenhuma viagem encontrada para este destino.</p>";
                    }
                }
                ?>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>

</html>