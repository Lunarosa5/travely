<?php

//detalhes_viagem: mostra uma viagem com todos suas informações

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
require_once __DIR__ . '/../database/connect.php';

// Pega o ID do usuário que está guardado na sessão e armazema na variável $id_usuario. Por exemplo, e o ID do usuário logado é 5, a variável $id_usuario passará a valer 5. Isso facilita o uso do ID do usuário nas próximas linhas, já que em vez de escrever $_SESSION['id_usuario'] toda vez, com ele, o valor é guardado uma vez e usado $id_usuario.
$id_usuario = $_SESSION['id_usuario'];

// Verifica se a página recebeu o ID de uma viagem pela URL pelo método GET, e caso não está definido (!isset) ou (||) esteja vazio (empty), redireciona o usuário para a página viagens.php. EXEMPLO: detalhes_viagem.php?id=3, o ?id=3 envia o valor 3 para o banco de dados que procura as infromações da viagem onde o ID=3, o bcd devolve e depois será exibido para o usuário.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: viagens.php");
    exit();
}

// Pega o id recebido pela URL e converte para um número inteiro e armazena isso em $id_viagem. Isso por que O $_GET['id'] recebe o valor da URL como string, mesmo que seja um número, e ai o (int) transforma o ID recebido como texto em um número inteiro que é o tipo esperado para o ID da viagem.
$id_viagem = (int)$_GET['id'];

// Define o para qual página o botão Voltar levará o usuário. O operador ?? escolhe entre dois valores: se $_GET['origem'] existir e não for null, usa esse valor. Caso contrário, usa 'viagens'. Por exemplo, se o usuário veio da página de pesquisa, o link pode ser: detalhes_viagem.php?id=3&origem=pesquisar. Nesse caso, $origem recebe "pesquisar". Se não houver o parâmetro origem, $origem recebe "viagens".
$origem = $_GET['origem'] ?? 'viagens';

// EXEMPLO: se $origem for "pesquisar", o resultado (voltar) será "pesquisar.php". Se for "viagens", será "viagens.php".
$link_voltar = $origem . '.php';

// Faz com que cada usuário acesse somente suas próprias viagens. Seleciona todas as colunas da tabela viagens onde o id da viagem (no banco de dados) é igual ao id da viagem (parametro) e o id do usuário (no banco de dados) é igual ao id do usuário (parametro)
$sql = "SELECT * FROM viagens WHERE id = :id AND id_usuario = :id_usuario";

// Prepara a consulta e guarda em $stmt ($stmt = consulta preparada e $sql é um "molde" da consulta)
$stmt = $conexao->prepare($sql);

// Associa um valor a um parâmetro da consulta e infroma que o valor deve ser int.
$stmt->bindValue(':id', $id_viagem, PDO::PARAM_INT);
$stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

// Executa a consulta preparada ($stmt), e pelo fetch ao resultado é pego e armazenado em um array associativo (chaves são os nomes das colunas $viagem['id']; $viagem['destino'];)).
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não achar a viagem ou não pertencer ao usuário logado, volta pra página de origem, local da onde o usuário veio.
if (!$viagem) {
    header("Location: " . $link_voltar);
    exit();
}

// Chama a função buscar_fotos-viagem passando a conexão com o banco de dados e o ID da viagem encontrada, armazenando o resultado em $fotos.
$fotos = buscar_fotos_viagem($conexao, $viagem['id']);

// Cria uma lista que guardará os caminhos das viagens que podem ser exibidas.
$lista_fotos = [];

// Se há fotos em $fotos, percorre cada foto como f (foto atual). Guarda na pasta uploads o caminho do nome do arquivo da foto atual em $listafotos. EXEMPLO: $lista_fotos = [ '../uploads/foto1.jpg', '../uploads/foto2.jpg' ];
if (!empty($fotos)) {
    foreach ($fotos as $f) {
        $lista_fotos[] = '../uploads/' . $f['nome_arquivo'];
    }

    // Senão, adiciona à $lista_fotos o caminho da imagem padrão que informa que não há fotos na viagem.
} else {
    $lista_fotos[] = '../images/sem_imagem.png';
}

// Conta quantas fotos há na $lista_fotos com o count() e armazena o resultado em total_fotos. Isso serve depois para definir quantas opções e setas o carrossel de imagems precisa gerar.
$total_fotos = count($lista_fotos);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Mostra o título da aba do navegador de acordo com a viagem que o usuário esta vendo -->
    <title><?php echo ($viagem['titulo']); ?> - Travely</title>

    <link rel="icon" type="image/png" href="/travely/images/favicon.png">
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-detalhes">

            <!-- Botão Voltar, que volta para a página anterior que o usuário estava. -->
            <a href="<?php echo ($link_voltar); ?>" class="btn-voltar-link">⬅ Voltar</a>

            <!-- Título da viagem. Ex.: Viagem para Paris -->
            <h1 class="titulo-detalhe-viagem"><?php echo ($viagem['titulo']); ?></h1>

            <div class="card-detalhe-container">

                <!-- Carrossel de fotos -->
                <div class="carrossel-css">
                    <!-- Laço de repetição que começa em 0 ($i = 0;) e roda enquanto $i é menor que a quantidade total de fotos ($i < $total_fotos) sempre adicionando a cada repetição +1 a $i. -->
                    <?php for ($i = 0; $i < $total_fotos; $i++): ?>

                        <!-- Cria um botão de opçao redondo, que no CSS serão estilizados para virarem setas. -->
                        <input type="radio" name="carrossel_foto" id="foto-<?php echo $i; ?>" <?php if ($i === 0) {
                                                                                                    echo 'checked';
                                                                                                } ?>>
                        <!-- O id="foto-<?php // echo $i; 
                                        ?>" funciona do seguinte modo: ele cria um nome único para o controle de cada foto baseado no número de $i. Se o carrossel tem 3 fotos, o PHP vai criar três nomes: foto-0, foto-1 e foto-2. E as setas usam esses nomes para saber qual foto exibir.
                    O <?php // if ($i === 0) { echo 'checked'; } 
                        ?> funciona assim: se $i é EXETAMENTE a primeira foto (0) ele marca um checked e depois mostrará a foto-01 e as setas, se não, nao marca nada. -->

                    <?php endfor; ?>

                    <div class="foto-detalhe-caixa">

                        <!-- Para cada foto em $lista_fotos, o número da posição (0, 1, 2...) é guardado em $numero e o caminho do arquivo é armazenado em $caminho -->
                        <?php foreach ($lista_fotos as $numero => $caminho): ?>

                            <!-- Exibe a imagem de acordo com seu caminho e cria uma classe para cada imagem usando a variável $numero, isso por que o CSS faz asa outras iamgens aparecerem de acordo com sua classe.  -->
                            <img src="<?php echo $caminho; ?>" alt="Foto de viagem" class="slide-foto foto-<?php echo $numero; ?>">

                        <?php endforeach; ?>
                    </div>

                    <!-- Confere se a viagem possui mais de uma foto cadastrada, se sim, as setas serão mostaradas, senão, não terá setas. -->
                    <?php if ($total_fotos > 1): ?>
                        <div class="controles-setas">

                            <!-- Cria um loop onde i começa sendo igual a 0 e se repete enquanto é menor que $total_fotos, enquanto adiciona +1 em $i a cada loop. -->
                            <?php for ($i = 0; $i < $total_fotos; $i++):

                                // O contador $i menos 1 mais o total de fotos dividido por um número onde o resto da divisão é o total de fotos é igual a foto anterior. EXEMPLO: se $i = 0 (primeira foto) e total de fotos = 3, a conta fica: (0 - 1 + 3) % 3 = 2 % 3. Como 2 dividido por 3 dá 0 e sobra 2, o resultado de $anterior é 2 (a última foto). Assim, a seta de voltar levará o usuário até ela.
                                $anterior = ($i - 1 + $total_fotos) % $total_fotos;

                                // O contador $i mais 1, onde o resto da divisãoé igual ao total de fotos resulta na próxima foto. EXEMPLO: se estiver na última foto ($i = 2) e o total de fotos = 3, a conta fica: (2 + 1) % 3 = 3 % 3. Como 3 dividido por 3 dá 1 e sobra 0, o resultado de $proxima é 0 (a primeira foto). Então, a seta de avançar levará o usuário de volta ao início.
                                $proxima = ($i + 1) % $total_fotos;
                            ?>
                                <!-- Cria as setas de acordo com o número de $i, por exemplo, se o loop estiver na primeira foto, a class será seta-grupo-0 e o CSS exibe as setas de acordo com o grupo -->
                                <div class="grupo-setas seta-grupo-<?php echo $i; ?>">

                                    <!-- o atributo for na tag label diz quem ela controlará, ou seja, se você estiver na "foto 0", a variável $anterior vale 2. O PHP vai escrever for="foto-2" e quando o usuário clicar no seta, o navegador entende a ordem e marca o botão invisível da última foto (foto-2) como ativo.  -->
                                    <label for="foto-<?php echo $anterior; ?>" class="seta-carrossel seta-esquerda">❬</label>


                                    <!-- O atributo for na tag label diz quem ela controlará, ou seja, se você estiver na "foto 0", a variável $proxima vale 1. O PHP vai escrever for="foto-1" e quando o usuário clicar na seta, o navegador entende a ordem e marca o botão invisível da próxima foto (foto-1) como ativo. -->
                                    <label for="foto-<?php echo $proxima; ?>" class="seta-carrossel seta-direita">❭</label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>


                            <!-- Exibe o período da viagem. O banco de dados armazena as datas no formato do sistema (AAAA-MM-DD). O código usa a função strtotime() para converter esse texto em uma linha do tempo e a função date('d/m/Y') para reorganizar os números no formato brasileiro (DD/MM/AAAA). -->
                <div class="info-detalhe-conteudo">
                    <div class="campo-info">
                        <h3>Data:</h3>
                        <div class="pilula-info">
                            <?php echo date('d/m/Y', strtotime($viagem['data_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($viagem['data_fim'])); ?>
                        </div>
                    </div>


                            <!-- Exibe a nota que a viagem recebeu. O PHP faz uma busca na variável $viagem['avaliacao'] e o comando echo exibe esse valor na tela, que pode ser um número de 1 a 5. -->
                    <div class="campo-info">
                        <h3>Avaliação:</h3>
                        <div class="pilula-info pilula-curta">
                            <?php echo ($viagem['avaliacao']); ?>
                        </div>
                    </div>

                            <!-- Exibe o relato escrito pelo usuário. Foi usada a tag <textarea>, que cria uma caixa própria para textos longos de vários parágrafos. O atributo rows="5" define a altura inicial da caixa, e o atributo readonly garante que o usuário que está visitando a página consiga apenas ler o texto, sem poder alterá-lo. -->
                    <div class="campo-info">
                        <h3>Relato:</h3>
                        <textarea class="caixa-relato-info" rows="5" readonly><?php echo ($viagem['relato']); ?></textarea>
                    </div>
                </div>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>

</html>