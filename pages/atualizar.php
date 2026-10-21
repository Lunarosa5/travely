<?php
// atualizar.php: Permite que o usuário pesquise a viagem que deseja atualizar e exibe as informações sobre as viagens do local pesquisado, além de possibilitar o usuário de escolher qual viagem ele irá atualizar.

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
require_once __DIR__ . '/../database/connect.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Relato - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-pesquisar">
            <h1 class="titulo-pagina">Atualize suas anotações</h1>

            <form action="" method="POST" class="form-pesquisa">
                <div class="campo-pesquisa-grupo">

                    <!-- Cria a barra de pesquisa para que após o usuário digitar o local e buscar, o local não suma, ou seja, em ($_POST['destino_busca'] ?? '') se o usuário digitou o destino, com o operador ?? ela retorará o destino, se não, deixará em branco a barra de pesquisa. -->
                    <input type="text" name="destino_busca" id="destino_busca" placeholder="Insira o local da sua viagem" value="<?= ($_POST['destino_busca'] ?? ''); ?>" required class="input-pesquisa">

                    <!-- Botão de buscar -->
                    <input type="submit" value="Buscar" class="btn-buscar">
                </div>
            </form>

            <div class="resultado-pesquisa-container">
                <?php

                // Verifica se o formulário foi enviado e se destino_busca não esta vazio, ou seja, se o usuário informou o local da pesquisa.
                if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['destino_busca'])) {

                    // Chama a função que pesquisa no banco de dados as viagens que correspondem ao destino informado pelo usuário e armazena na variável $resultados
                    $resultados = pesquisar_opcoes($conexao, $_POST['destino_busca']);

                    // Se a variável $resultados não estiver vazia o sistema continua.
                    if (!empty($resultados)) {

                        // Para cada resultado como uma viagem, o sistema buscará as fotos relacionadas ao seu ID.
                        foreach ($resultados as $viagem) {
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
                                    <img src="<?= ($caminho_imagem); ?>" alt="Foto de <?php echo $viagem['destino']; ?>">
                                </div>

                                <!-- Exibe as informações das viagens -->
                                <div class="info-viagem">

                                    <!-- Exibe o titulo da viagem de acordo com o destino que esta armazenada no bcd -->
                                    <p class="titulo-local"><strong><?php echo $viagem['destino']; ?></strong></p>

                                    <!-- Exibe a data de inicio da viagem de acordo com a data_inicio que esta armazenada no bcd -->
                                    <p>Data: <?php echo date('d/m/Y', strtotime($viagem['data_inicio'])); ?></p>

                                    <!-- Exibe a nota da viagem de acordo com a avaliacao que esta armazenada no bcd -->
                                    <p>Nota: <?php echo $viagem['avaliacao']; ?></p>


                                    <!-- Botão Atualizar que leva para a página de edição -->
                                    <a href="editar_viagem.php?id=<?= $viagem['id']; ?>" class="btn-ver-mais">Atualizar</a>
                                </div>
                            </div>
                <?php
                        }
                        // Se a variável $resultados estiver vazia o sistema exibe uma mensagem.
                    } else {
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