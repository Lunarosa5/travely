<?php

// inicio.php: página inicial

// inicia a sessão do usuário
session_start();

// Carrega arquivo de conexão com o banco de dados e o arquivo de funções
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';

// Pega o ID do usuário que está logado
$id_usuario = $_SESSION['id_usuario'];

// E com base nele busca os dados da dashboard (Total, Média, Dias) pela função dados_dashboard que calcula esses dados
$dados = dados_dashboard($conexao, $id_usuario);

// Busca a lista das últimas viagens pela function que às procura
$ultimas_viagens = ultimas_viagens($conexao, $id_usuario);

// Se através dos dados a média foi calculada, ela é formatada para mostrar no máximo uma casa decimal (ex: 4.5). Caso não a média de notas é igual a 0.
if ($dados['media_notas']) {
    $media_notas = number_format($dados['media_notas'], 1);
} else {
    $media_notas = '0';
}
?><!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">

    <div class="dashboard-container">

        <!-- Inclui o header padrão das páginas-->
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard">

            <!-- Espaços com os dados da dashboard -->
            <div class="cards-topo">
                <div class="espaco-dashboard">

                    <!-- Retorna o total de viagens, mas se ele não existir, retorna o número 0. -->
                    <h3>Total de viagens:</h3>
                    <p class="valor-espaco"><?php echo $dados['total_viagens'] ?? 0; ?></p>
                </div>

                <!-- Exibe a média de notas formatada-->
                <div class="espaco-dashboard">
                    <h3>Média de avaliações:</h3>
                    <p class="valor-card"><?php echo $media_notas; ?></p>
                </div>

                <!-- Exibe os dados referentes ao total de dias que o usuário esteve em viagem. -->
                <div class="espaco-dashboard">
                    <h3>Dias em viagens:</h3>
                    <p class="valor-card"><?php echo $dados['dias_viagens'] ?? 0; ?></p>
                </div>
            </div>

            <hr class="divisor-dashboard">

            <!-- Seção das últimas viagens -->
            <section class="ultimas-viagens">
                <h2>Últimas viagens:</h2>

                <!-- Espaço para mostrar as últimas viagens relatadas pelo usuário -->
                <div class="viagens">

                    <!-- Se houver viagens, exibe o loop, caso contrário, mostra uma mensagem informativa. -->
                    <?php if (!empty($ultimas_viagens)): ?>

                    <!-- Percorre a lista de ultimas viagens uma por uma (viagem) -->
                        <?php foreach ($ultimas_viagens as $viagem):

                            // Busca as fotos salvas associadas ao ID da viagem
                            $fotos = buscar_fotos_viagem($conexao, $viagem['id']);

                            // Verifica se foram encontradas fotos
                            if (!empty($fotos)) {
                                
                            // Tenta acessar o primeiro item [0] da array de fotos enviado pelo usuário.
                                $caminho_imagem = '../uploads/' . $fotos[0]['nome_arquivo'];
                            } 
                            
                            // Se não houver fotos o caminho da imagem será uma imagem padrão salva na pasta imagens, que indica que a viagem não possui uma foto salva.
                            else {
                                $caminho_imagem = '../images/sem_imagem.png';
                            }
                        ?>
                            <div class="espaco-viagem">
                                <div class="imagem-viagem">

                        <!-- Mostra a imagem de acordo com seu caminho -->
                                    <img src="<?php echo ($caminho_imagem); ?>" alt="Foto da viagem">
                                </div>

                                <div class="info-viagem">
                        <!-- Pega o valor armazenado na coluna destino daquela viagem e mostra o título da viagem e o local de destino. -->
                                    <p class="titulo-local"><strong><?php echo ($viagem['destino']); ?></strong></p>

                        <!-- Pega o valor armazenado na coluna data_inicio daquela viagem e mostra a data formatada em dia/mês/ano e através do strtotime transforma a data de texto para um formato onde o PHP pode interpretar -->
                                    <p>Data: <?php echo date('d/m/Y', strtotime($viagem['data_inicio'])); ?></p>

                        <!-- Pega o valor armazenado na coluna avaliacao daquela viagem e mostra ela. -->
                                    <p>Nota: <?php echo ($viagem['avaliacao']); ?></p>

                        <!-- Link para abrir a página detalhes_viagem.php onde será mostrado mais detalhes da viagem selecionada. -->
                                    <a href="detalhes_viagem.php?id=<?= $viagem['id']; ?>&origem=inicio" class="btn-ver-mais">Ver mais</a>
                                </div>
                            </div>

                        <!-- Encerra o foreach, parando o ciclo de exibição das viagens. -->
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p class="sem-viagens">Nenhuma viagem cadastrada ainda.</p>
                    <?php endif; ?>

                </div>
            </section>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>

    </div>

</body>

</html>
