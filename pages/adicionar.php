<?php

// A sessão é iniciada
session_start();

// Carrega os arquivos que serão usados para o funcionamento da página
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';
require_once __DIR__ . '/../database/connect.php';

// Guarda a data atual no formato ano-mês-dia (por exemplo 2026-10-21).
$hoje = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Nova Viagem - Travely</title>
    <link rel="stylesheet" href="/travely/css/style.css">
</head>

<body class="fundo-dashboard">
    <div class="dashboard-container">

        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="dashboard-conteudo-form">
            <h1 class="titulo-pagina">Descreva sua nova viagem</h1>

            <?php

            // Verifica se a requisição método POST foi enviada.
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                // Salva na variável $id_usuario o valor do id do usuário atual da sessão.
                $id_usuario = $_SESSION['id_usuario'];

                // Cria uma variável chamada $titulo que junta o texto "Viagem para " com o valor armazenado na coluna destino no bcd.
                $titulo = "Viagem para " . $_POST['destino'];

                // Começa a contagem de fotos enviadas do zero
                $total_fotos = 0;

                // Verifica se existe um arquivo ou arquivos (array) 
                if (isset($_FILES['imagem']['name']) && is_array($_FILES['imagem']['name'])) {

                    // Cria um loop que percorre cada arquivo enviado e a cada repetição que acontece, o nome do arquivo é guardado na variável $nome.
                    foreach ($_FILES['imagem']['name'] as $nome) {

                        // Se $nome não esta vazio, a cada arquivo somará +1 no contador.
                        if (!empty($nome)) {
                            $total_fotos++;
                        }
                    }
                }

                // Se passou de 10, nem tenta cadastrar a viagem e mostra o aviso direto
                if ($total_fotos > 10) {
                    echo "<div class='container-mensagem'><p class='caixa-mensagem erro'>Você só pode adicionar até 10 imagens por viagem!</p></div>";
                } else {

                    // Tenta cadastrar a viagem normalmente
                    $id_viagem_criada = cadastrar_viagem(
                        $conexao,
                        $id_usuario,
                        $titulo,
                        $_POST['destino'],
                        $_POST['data_inicio'],
                        $_POST['data_fim'],
                        $_POST['relato'],
                        $_POST['avaliacao']
                    );

                    if ($id_viagem_criada) {
                        // Verifica se algum arquivo foi enviado de fato antes de chamar a função
                        if (isset($_FILES['imagem']) && !empty($_FILES['imagem']['name'][0])) {
                            foto_viagem($conexao, $id_viagem_criada, $_FILES['imagem']);
                        }

                        echo "<div class='container-mensagem'><p class='caixa-mensagem sucesso'>Viagem cadastrada com sucesso!</p></div>";
                        $_POST = array();
                    }
                }
            }
            ?>

            <div class="card-formulario">
                <form action="" method="post" enctype="multipart/form-data" class="form-viagem">

                    <!-- Campo Destino -->
                    <div class="campo-form largura-total">
                        <label for="destino">Adicione o destino:</label>

                        <!-- Cria uma caixa de texto obrigatório (required) onde se algum erro acontecer e a página recarregar, o destino digitado não sumirá. -->
                        <input type="text" name="destino" id="destino" value="<?= ($_POST['destino'] ?? ''); ?>" required>

                        <!-- O operador ?? faz com que, quando o usuário abre a página pela primeira vez, o valor do input seja ' ', ou seja, nulo. Mas se a página recarregar, o que o usuário havia digitado será exibido novamente $_POST['destino']. -->

                    </div>

                    <!-- Datas -->
                    <div class="campo-form metade">
                        <label for="data_inicio">Data de inicio da viagem:</label>

                        <!-- Cria uma caixa de texto obrigatório (required) onde se algum erro acontecer e a página recarregar, o destino digitado não sumirá, e bloqueia datas futuras, permitindo o usuário escolher apenas até o dia atual -->
                        <input type="date" name="data_inicio" id="data_inicio" max="<?= $hoje; ?>" value="<?= ($_POST['data_inicio'] ?? ''); ?>" required>

                        <!-- O operador ?? faz com que, quando o usuário abre a página pela primeira vez, o valor do input seja ' ', ou seja, nulo. Mas se a página recarregar, o que o usuário havia digitado será exibido novamente com $_POST['data_inicio']. -->

                        <!-- O atributo max define a data limite que o calendário vai permitr que o usuário insira, assim, o usuário só poderá adicionar uma data igual ou menor que a do dia atual -->
                    </div>

                    <div class="campo-form metade">
                        <label for="data_fim">Data de fim da viagem:</label>

                        <!-- Cria uma caixa de texto obrigatório (required) onde se algum erro acontecer e a página recarregar, o destino digitado não sumirá, e bloqueia datas futuras, permitindo o usuário escolher apenas até o dia atual -->
                        <input type="date" name="data_fim" id="data_fim" max="<?= $hoje; ?>" value="<?= ($_POST['data_fim'] ?? ''); ?>" required>

                        <!-- O operador ?? faz com que, quando o usuário abre a página pela primeira vez, o valor do input seja ' ', ou seja, nulo. Mas se a página recarregar, o que o usuário havia digitado será exibido novamente  com $_POST['data_fim']. -->

                        <!-- O atributo max define a data limite que o calendário vai permitr que o usuário insira, assim, o usuário só poderá adicionar uma data igual ou menor que a do dia atual -->
                    </div>

                    <!-- Relato da viagem -->
                    <div class="campo-form largura-total">
                        <label for="relato">Descreva-a:</label>

                        <!-- Cria um área de texto, onde o usuário poderá escrever seu relato. Diferentemente do <input>, a tag <textarea> não usa o atributo value para mostrar um texto, ela possui uma tag de abertura e uma de fechamento, onde o texto ficará em seu meio. Por isso que o <?php echo ($_POST['relato'] ?? ''); ?> aparece no meio das tags, pois ele será o relato inserido pelo usuário. -->
                        <textarea name="relato" id="relato" rows="3" required><?php echo ($_POST['relato'] ?? ''); ?></textarea>

                        <!-- rows = linhas, ou seja, define a altura inicial da caixa de texto, mas o usuário pode digitar mais do que isso e a caixa criará automaticamente a rolagem. -->

                        <!-- name="relato" é o nome que o PHP vai usar para capturar o texto digitado após o envio (gerando o $_POST['relato']). -->

                        <!-- O operador ?? faz com que, quando o usuário abre a página pela primeira vez, o valor seja ' ', ou seja, nulo. Mas se a página recarregar, o que o usuário havia digitado será exibido novamente com $_POST['relato']. -->
                    </div>

                    <!-- Foto -->
                    <div class="campo-form metade">
                        <label for="imagem">Adicione uma foto da viagem:</label>

                        <!-- Cria um input do tipo file, arquivo, onde name="imagem[]" que "avisa" o PHP que esse campo não vai enviar apenas um arquivo simples, mas sim uma lista(array). Além do multiple permitir que o usuário selecione e envie mais de uma foto ao mesmo tempo. O accept="image/*" bloqueia o envio de outros arquivos (como PDFs) e aceita somente imagens. -->
                        <input type="file" name="imagem[]" id="imagem" accept="image/*" multiple>
                    </div>

                    <!-- Nota -->
                    <div class="campo-form metade">
                        <label for="avaliacao">De uma nota de 0 à 5:</label>

                        <!-- Cria um input do tipo número, onde o máximo a ser adicionado é 5 e o mínimo 0, já que a avaliação da viagem pode ser de 0 à 5. Além disso, se algum erro acontecer e a página recarregar, o destino digitado não sumir por conta do valor "<?php echo ($_POST['avaliacao'] ?? ''); ?>". -->
                        <input type="number" name="avaliacao" id="avaliacao" min="0" max="5" value="<?php echo ($_POST['avaliacao'] ?? ''); ?>" required>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="container-botoes">
                        <input type="reset" value="Limpar" class="btn-form btn-limpar">
                        <input type="submit" value="Salvar" class="btn-form btn-salvar">
                    </div>
                </form>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>

    </div>
</body>

</html>