<?php

//atualizar_perfil: permite ao usuário alterar seus dados cadastrais (nome, email, senhas)

// A sessão é iniciada para podermos acessar os dados do usuário que estão logados no navegador.
session_start();

// Carrega os arquivos de conexão com o banco de dados, funções gerais do sistema e a validação de segurança que bloqueia usuários não logados.
require_once __DIR__ . '/../database/connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../login/verifica_user.php';

// Pega o ID do usuário que está guardado na sessão e armazena na variável $id_usuario. Por exemplo, se o ID do usuário logado é 2, a variável $id_usuario passará a valer 2.
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
            // Verifica se o formulário foi enviado através do método POST (quando o usuário clica em "Salvar").
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Guarda os dados enviados pelas caixas de texto do formulário em variáveis simples.
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                $senha_atual = $_POST['senha_atual'];
                $senha_nova = $_POST['senha_nova'];
                
                // Cria uma variável padrão vazia para a foto. Ela só mudará de valor se o upload da nova foto der totalmente certo.
                $nome_foto_final = null;

                // Verifica se o campo do arquivo existe (isset) e se o código de erro é diferente de 4. O erro 4 significa que o usuário não selecionou nenhuma foto nova, fazendo o PHP ignorar esse bloco se ele quiser apenas mudar o nome ou e-mail.
                if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== 4) {
                    $arquivo = $_FILES['foto_perfil'];

                    // Padronização em lista: se a propriedade já for uma lista (upload múltiplo), mantém. Se for um arquivo único, coloca entre colchetes [...] criando uma lista de um único item. Isso garante que o código trate qualquer cenário do mesmo jeito sem quebrar.
                    $nomes = is_array($arquivo['name']) ? $arquivo['name'] : [$arquivo['name']];
                    $tmp_names = is_array($arquivo['tmp_name']) ? $arquivo['tmp_name'] : [$arquivo['tmp_name']];
                    $erros = is_array($arquivo['error']) ? $arquivo['error'] : [$arquivo['error']];

                    // Verifica se o primeiro item da lista de erros é igual a 0, ou seja, se o upload para o servidor temporário foi feito com total sucesso.
                    if ($erros[0] === 0) {
                        // Extrai a extensão do arquivo (ex: .PNG ou .jpg), transforma todas as letras em minúsculas (strtolower) e confere se ela está na lista de extensões permitidas.
                        $extensao = strtolower(pathinfo($nomes[0], PATHINFO_EXTENSION));
                        $extensoes_permitidas = ['jpg', 'jpeg', 'png'];

                        if (in_array($extensao, $extensoes_permitidas)) {
                            // Cria um nome exclusivo para a foto usando o ID do usuário e o timestamp atual (time()). EXEMPLO: perfil_5_1717200000.png. Isso evita que uma foto nova sobrescreva a foto antiga ou a de outro usuário.
                            $nome_foto_final = "perfil_" . $id_usuario . "_" . time() . "." . $extensao;
                            
                            // Define o caminho físico definitivo onde a foto será salva dentro do servidor do site.
                            $destino = __DIR__ . "/../images/uploads/" . $nome_foto_final;

                            // Verifica se a pasta "uploads" não existe (!is_dir). Se não existir, a função mkdir cria a pasta automaticamente com as permissões corretas de escrita (0755).
                            if (!is_dir(__DIR__ . "/../images/uploads/")) {
                                mkdir(__DIR__ . "/../images/uploads/", 0755, true);
                            }

                            // Move o arquivo da pasta temporária secreta do PHP ($tmp_names[0]) para a pasta definitiva de uploads do site ($destino). Se falhar por falta de espaço ou permissão, a variável do nome volta a ser null para não quebrar o banco.
                            if (!move_uploaded_file($tmp_names[0], $destino)) {
                                $nome_foto_final = null;
                            }
                        }
                    }
                }

                // Cria o container visual da mensagem de sucesso ou erro na tela e executa a função atualizar_perfil passando todas as informações recolhidas para alterar os dados no Banco de Dados.
                echo "<div class='container-mensagem'>";
                atualizar_perfil($conexao, $id_usuario, $nome, $email, $senha_atual, $senha_nova, $nome_foto_final);
                echo "</div>";
            }

            // Vai ao banco de dados usando a função buscar_usuario() para recuperar as informações atualizadas do usuário logado e preencher o formulário abaixo.
            $usuario = buscar_usuario($conexao, $id_usuario);
            
            // Define qual foto de perfil exibir na tela. O operador ternário checa: se o campo 'foto' no banco não estiver vazio (!empty), usa o caminho da foto customizada do usuário. Se estiver vazio, exibe o caminho da imagem padrão cinza (foto_perfil.png).
            $foto_atual = !empty($usuario['foto']) ? "../images/uploads/" . $usuario['foto'] : "../images/foto_perfil.png";
            ?>

            <div class="card-perfil-container">
                <!-- O atributo enctype="multipart/form-data" é obrigatório aqui. Sem ele, o navegador envia apenas os textos dos inputs e ignora completamente o arquivo de foto anexado. -->
                <form action="" method="POST" enctype="multipart/form-data" class="form-perfil-campos">
                    
                    <!-- Seção visual que centraliza e agrupa o avatar redondo e o botão de seleção -->
                    <div class="avatar-secao" style="text-align: center; margin-bottom: 20px;">
                        <div class="avatar-circulo" style="margin: 0 auto 10px;">
                            <!-- Exibe dinamicamente a foto do usuário ou a imagem padrão carregada na variável $foto_atual -->
                            <img src="<?= $foto_atual; ?>" alt="Foto de Perfil" class="foto-perfil-redonda">
                        </div>
                        
                        <!-- A tag label está vinculada ao input através do atributo for="foto_perfil". Como o input original é feio, nós o escondemos com CSS e usamos este texto estilizado e clicável para abrir a janela de escolha de arquivos. -->
                        <label for="foto_perfil" class="lbl-upload-foto" style="cursor: pointer; color: #007bff; font-weight: bold;">
                            Alterar foto de perfil
                        </label>
                        
                        <!-- Input real do tipo arquivo. O accept restringe a janela de seleção apenas para arquivos de imagem comuns (png, jpeg, jpg). O style="display: none;" esconde o botão padrão feio do navegador. -->
                        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/png, image/jpeg, image/jpg" style="display: none;">
                    </div>

                    <!-- Nome de Usuário -->
                    <div class="campo-perfil-item">
                        <label for="nome">Insira o nome de usuário:</label>
                        <!-- O operador ?? (Null Coalesce) mostra o nome que o usuário acabou de digitar caso a página recarregue com erro ($_POST['nome']). Se for o primeiro acesso à página, ele puxa o nome salvo no banco de dados ($usuario['nome']). O htmlspecialchars() limpa o texto contra códigos maliciosos de invasão (XSS). -->
                        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($_POST['nome'] ?? $usuario['nome']); ?>" required class="input-perfil-pino">
                    </div>

                    <!-- Email -->
                    <div class="campo-perfil-item">
                        <label for="email">Insira o email:</label>
                        <!-- Segue a mesma lógica do campo nome: mantém o e-mail digitado no formulário se houver um recarregamento por erro, ou exibe o e-mail cadastrado atualmente no banco de dados. -->
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? $usuario['email']); ?>" required class="input-perfil-pino">
                    </div>

                    <!-- Senha Atual -->
                    <div class="campo-perfil-item">
                        <label for="senha_atual">Insira sua senha atual:</label>
                        <!-- Campo obrigatório (required) para confirmar a identidade do usuário antes de salvar qualquer modificação em seu perfil. -->
                        <input type="password" name="senha_atual" id="senha_atual" required class="input-perfil-pino">
                    </div>

                    <!-- Nova Senha -->
                    <div class="campo-perfil-item">
                        <label for="senha_nova">Insira uma nova senha (opcional):</label>
