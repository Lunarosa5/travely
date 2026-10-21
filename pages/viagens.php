<?php
// viagens.php: mostra as viagens cadastradas no sistema.

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
require_once __DIR__ . '/../database/connect.php';

// Chama a função relatorio() para buscar a lista de viagens no banco de dados e armazena o resultado na variável $viagens.
$viagens = relatorio($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas viagens - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">

        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-viagens">
            <h1 class="titulo-pagina">Minhas viagens</h1>

            <div class="viagens">
                <?php
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
                            <div class="imagem-viagem">
                                <img src="<?php echo ($caminho_imagem); ?>" alt="Foto da viagem">
                            </div>

                            <!-- Exibe as informações das viagens -->
                            <div class="info-viagem">

                                <!-- Exibe o titulo da viagem de acordo com o destino que esta armazenada no bcd -->
                                <p class="titulo-local"><strong><?php echo ($viagem['destino']); ?></strong></p>

                                <!-- Exibe a data de inicio da viagem de acordo com a data_inicio que esta armazenada no bcd -->
                                <p>Data: <?php echo date('d/m/Y', strtotime($viagem['data_inicio'])); ?></p>

                                <!-- Exibe a nota da viagem de acordo com a avaliacao que esta armazenada no bcd -->
                                <p>Nota: <?php echo ($viagem['avaliacao']); ?></p>

                                <!-- Botão Ver Mais que leva para a página da viagem com mais informações -->
                                <a href="detalhes_viagem.php?id=<?= $viagem['id']; ?>&origem=viagens" class="btn-ver-mais">Ver mais</a>
                            </div>
                        </div>
                <?php
                    }
                }
                // Se a lista de viagens estiver vazia, exibe uma mensagem informando que não há viagens cadastradas.
                else {
                    echo "<p class='sem-viagens'>Nenhuma viagem cadastrada ainda.</p>";
                }
                ?>

            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>

    </div>
</body>

</html>