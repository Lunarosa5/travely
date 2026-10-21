<?php
// functions.php: guarda todas as funções do sistema que serão utilizadas nas páginas

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../database/connect.php';


// FUNÇÕES PARA LOGIN E CADASTRO DE USUÁRIOS:


// cadastrar_user: Cadastra um novo usuário no sistema com senha criptografada

// cria a função cadastrar_user que recebe a conexão com o banco, nome, e-mail e senha do usuário que será cadastrado.
function cadastrar_user($conexao, $nome, $email, $senha)
{
    // Gera um hash da senha com BCRYPT (ferramenta para criar hashes de senhas), para que a senha original não seja armazenada no banco de dados, aumentando a segurança do sistema.
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    // Cria a consulta que será preenchida com o novo usuário no banco de dados. Também foi usado o RETURNING id para obter o ID gerado no INSERT
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha) RETURNING id";

    // Tenta executar a query e trata possíveis erros
    try {

    // O prepare($sql) prepara a consulta e guarda o resultado em $stmt.
        $stmt = $conexao->prepare($sql);

        // Pelo bindParam conecta o e-mail recebido ($nome) pela função ao parâmetro da consulta SQL (":nome"), faz isso nas próximas duas linhas
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha_hash);
        // bindParam é usado para associar as variáveis do PHP aos parâmetros da consulta SQL.

        // Se a inserção for bem-sucedida, recupera o ID gerado pelo banco em um array associativo pelo fetch
        if ($stmt->execute()) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Guarda informações dentro da sessão do usuário, para que o sistema saiba quem está logado
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome_usuario'] = $nome;

            // Retorna true para indicar que o cadastro foi bem-sucedido
            return true;
        }
        // Se o cadastro falhar, retorna false
        return false;
    } catch (PDOException $e) {
        if ($e->getCode() == '23505') {
            // Código 23505 no PostgreSQL indica uma violação de chave única, no caso, que o e-mail já existe (UNIQUE)

            // Então, se for erro por conta do e-mail, exibe uma mensagem correspondente
            echo "<p class='alerta erro'>Este e-mail já está cadastrado no sistema!</p>";
        } else {
            //  Se não, exibe uma mensagem correspondente ao erro que aconteceu
            echo "<p class='alerta erro'>Erro ao cadastrar: " . $e->getMessage() . "</p>";
        }
        return false; // Retorna false para indicar que o cadastro falhou
    }
}

// consulta_user: Busca os dados de um usuário pelo e-mail e retornar suas informações.

// cria a função consulta_user que recebe a conexão com o banco e o e-mail do usuário que queremos encontrar.
function consulta_user($conexao, $email)
{

// Prepara a consulta ao bcd para buscar o ID, nome, e-mail e senha do usuário que está na tabela usuarios pelo e-mail informado
    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = :email";

    // Tenta executar a consulta
    try {

    // prepara a consulta para que ela possa receber o e-mail de maneira segura, armazenando isso em $stmt
        $stmt = $conexao->prepare($sql);

        // Pelo bindParam conecta o e-mail recebido ($email) pela função ao parâmetro da consulta SQL (":email")
        $stmt->bindParam(":email", $email);

        // Executa a busca do usuário no banco de dados
        $stmt->execute();

        // recupera o usuário encontrado pelo fetch, organiza os dados em um array associativo (estrutura que armazena os dados em chave e valor, ou seja, $usuario['nome'] -> $usuario é a array, 'name' é a chave e o resultado disso seria o valor, como Luna) pelo PDO::FETCH_ASSOC e armazena o resultado em $usuario
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Devolve o resultado em $usuario para o local onde a função foi chamada.
        return $usuario;

        // Se ocorrer algum erro, o código captura o erro e exibe a mensagem correspondente
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}

// dados_dashboard: Função para buscar os dados dos 3 espaços do dashboard
function dados_dashboard($conexao, $id_usuario)
{
    // Calcula o total de viagens, faz a média das notas e soma a diferença de dias
    $sql = "SELECT 
                COUNT(*) as total_viagens,
                AVG(avaliacao) as media_notas,
                SUM(data_fim - data_inicio + 1) as dias_viagens
            FROM viagens 
            WHERE id_usuario = :id_usuario";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['total_viagens' => 0, 'media_notas' => 0, 'dias_viagens' => 0];
    }
}

// Função para buscar as 2 últimas viagens cadastradas pelo usuário
function ultimas_viagens($conexao, $id_usuario)
{
    $sql = "SELECT * FROM viagens 
            WHERE id_usuario = :id_usuario 
            ORDER BY id DESC 
            LIMIT 2";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// FUNÇÕES DE GESTÃO DE VIAGENS (CRUD QUE FICARÁ NA PASTA PAGES)

// cadastrar_viagem: Função para cadastrar uma nova viagem
function cadastrar_viagem($conexao, $id_usuario, $titulo, $destino, $data_inicio, $data_fim, $relato, $avaliacao)
{
    $hoje = date('Y-m-d');

    // Valida se as datas são válidas: a data de início e fim não podem ser maiores que a data atual, e a data de início não pode ser maior que a data de fim.
    if ($data_inicio > $hoje || $data_fim > $hoje || $data_inicio > $data_fim) {
        echo "<div class='container-mensagem'><p class='caixa-mensagem erro'>Data inválida! Tente novamente.</p></div>";
        return false;
    }

    $sql = "INSERT INTO viagens (id_usuario, titulo, destino, data_inicio, data_fim, relato, avaliacao) 
            VALUES (:id_usuario, :titulo, :destino, :data_inicio, :data_fim, :relato, :avaliacao) RETURNING id";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->bindParam(':relato', $relato);
        $stmt->bindParam(':avaliacao', $avaliacao);

        if ($stmt->execute()) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['id'];
        }
        return false;
    } catch (PDOException $e) {
        echo "<p>Erro ao cadastrar viagem: " . $e->getMessage() . "</p>";
        return false;
    }
}

// relatorio: Função para possibilitar o usuário de ver todas suas viagens relatadas (da cadastrada mais recentemente para a mais antiga)
function relatorio($conexao)
{
    $id_usuario = $_SESSION['id_usuario'];
    // Ordena pelo ID em ordem decrescente (a última cadastrada fica em primeiro lugar)
    $sql = "SELECT * FROM viagens WHERE id_usuario = :id_usuario ORDER BY id DESC";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // Retorna o array associativo com todas as viagens encontradas
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// pesquisar_opcoes: Busca e retorna as viagens encontradas pelo destino
function pesquisar_opcoes($conexao, $destino)
{
    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT * FROM viagens WHERE id_usuario = :id_usuario AND destino ILIKE :destino ORDER BY data_inicio DESC";

    try {
        $stmt = $conexao->prepare($sql);
        $termo = '%' . $destino . '%';
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':destino', $termo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// excluir: Função para excluir um relato da viagem pelo ID, confirmando o usuário logado
function excluir($conexao, $id, $id_usuario)
{
    // Primeiro exclui as imagens associadas (se existirem na tabela fotos_viagem)
    $sql_fotos = "DELETE FROM fotos_viagem WHERE id_viagem = :id";
    $stmt_fotos = $conexao->prepare($sql_fotos);
    $stmt_fotos->bindParam(':id', $id);
    $stmt_fotos->execute();

    // Depois exclui a viagem
    $sql = "DELETE FROM viagens WHERE id = :id AND id_usuario = :id_usuario";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_usuario", $id_usuario);

        if ($stmt->execute()) {
            echo "<p class='caixa-mensagem'>Viagem apagada com sucesso!</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='caixa-mensagem erro'>Erro ao apagar viagem: " . $e->getMessage() . "</p>";
    }
}

// atualizar: Função para atualizar os dados de uma viagem
function atualizar($conexao, $id, $destino, $data_inicio, $data_fim, $avaliacao, $relato, $id_usuario)
{
    // Prepara a query para atualizar os dados da viagem, garantindo que apenas o usuário logado possa atualizar suas próprias viagens.
    $sql = "UPDATE viagens 
            SET destino = :destino, data_inicio = :data_inicio, data_fim = :data_fim, avaliacao = :avaliacao, relato = :relato 
            WHERE id = :id AND id_usuario = :id_usuario";

    // Tenta executar a query (try) e trata possíveis erros (catch). Se a execução for bem-sucedida, exibe uma mensagem de sucesso; caso contrário, exibe uma mensagem de erro.
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', (int)$id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->bindParam(':avaliacao', $avaliacao);
        $stmt->bindParam(':relato', $relato);

        if ($stmt->execute()) {
            echo "<p>A viagem para " . ($destino) . " foi atualizada!</p>";
        } else {
            echo "<p>Erro ao atualizar viagem.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erro: " . $e->getMessage() . "</p>";
    }
}



// GERENCIAMENTO DE PERFIL DE USUÁRIO (CRUD DE USUÁRIO)

// Função para buscar os dados do usuário logado
function buscar_usuario($conexao, $id_usuario)
{
    $sql = "SELECT id, nome, email FROM usuarios WHERE id = :id";
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Função para atualizar os dados do perfil (nome, email e senha)
function atualizar_perfil($conexao, $id_usuario, $nome, $email, $senha_atual, $senha_nova)
{
    $sql = "SELECT senha FROM usuarios WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($senha_atual, $user['senha'])) {
        echo "<p>Senha atual incorreta! As alterações não foram salvas.</p>";
        return false;
    }

    if (!empty($senha_nova)) {
        $nova_senha_hash = password_hash($senha_nova, PASSWORD_BCRYPT);
        $sql_update = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bindParam(':senha', $nova_senha_hash);
    } else {
        $sql_update = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
        $stmt_update = $conexao->prepare($sql_update);
    }

    try {
        $stmt_update->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt_update->bindParam(':nome', $nome);
        $stmt_update->bindParam(':email', $email);

        if ($stmt_update->execute()) {
            $_SESSION['nome_usuario'] = $nome;
            echo "<p>Perfil atualizado com sucesso!</p>";
            return true;
        }
        return false;
    } catch (PDOException $e) {
        echo "<p>Erro ao atualizar perfil: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Função para excluir a conta do usuário e todas as suas viagens
function excluir_conta($conexao, $id_usuario)
{
    try {
        // Apaga todas as viagens do usuário
        $sql_viagens = "DELETE FROM viagens WHERE id_usuario = :id";
        $stmt_viagens = $conexao->prepare($sql_viagens);
        $stmt_viagens->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt_viagens->execute();

        // Apaga o usuário
        $sql_user = "DELETE FROM usuarios WHERE id = :id";
        $stmt_user = $conexao->prepare($sql_user);
        $stmt_user->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt_user->execute();

        // Encerra a sessão e redireciona para o login
        session_destroy();
        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro ao excluir conta: " . $e->getMessage() . "</p>";
    }
}

// FUNÇÕES DE UPLOAD DE IMAGENS:


// foto_viagem: função para mover até 10 fotos para a pasta uploads e salvar no banco de dados
function foto_viagem($conexao, $id_viagem, $arquivo)
{
    // Se não houver arquivo enviado, encerra
    if (empty($arquivo['name'])) {
        return true;
    }

    // Se for múltiplos arquivos (array)
    if (is_array($arquivo['name'])) {
        // Conta quantos arquivos realmente foram selecionados (ignorando os vazios)
        $total_enviados = 0;
        foreach ($arquivo['name'] as $nome) {
            if (!empty($nome)) {
                $total_enviados++;
            }
        }

        // Se o usuário selecionou mais de 10 imagens, exibe a mensagem e interrompe
        if ($total_enviados > 10) {
            echo "<div class='container-mensagem'><p class='caixa-mensagem erro'>Você só pode adicionar até 10 imagens por viagem!</p></div>";
            return false;
        }

        for ($i = 0; $i < count($arquivo['name']); $i++) {
            if (!empty($arquivo['name'][$i]) && isset($arquivo['error'][$i]) && $arquivo['error'][$i] === UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($arquivo['name'][$i], PATHINFO_EXTENSION));
                $novo_nome = "foto_" . uniqid() . "_" . $i . "." . $extensao;
                $destino = __DIR__ . '/../uploads/' . $novo_nome;

                if (move_uploaded_file($arquivo['tmp_name'][$i], $destino)) {
                    $sql = "INSERT INTO fotos_viagem (id_viagem, nome_arquivo) VALUES (:id_viagem, :nome_arquivo)";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindValue(':id_viagem', (int)$id_viagem, PDO::PARAM_INT);
                    $stmt->bindParam(':nome_arquivo', $novo_nome);
                    $stmt->execute();
                }
            }
        }
    } else {
        // Se $arquivo['name'] for uma string simples (apenas 1 foto)
        if (!empty($arquivo['name']) && isset($arquivo['error']) && $arquivo['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $novo_nome = "foto_" . uniqid() . "." . $extensao;
            $destino = __DIR__ . '/../uploads/' . $novo_nome;

            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $sql = "INSERT INTO fotos_viagem (id_viagem, nome_arquivo) VALUES (:id_viagem, :nome_arquivo)";
                $stmt = $conexao->prepare($sql);
                $stmt->bindValue(':id_viagem', (int)$id_viagem, PDO::PARAM_INT);
                $stmt->bindParam(':nome_arquivo', $novo_nome);
                $stmt->execute();
            }
        }
    }

    return true;
}

// Função para buscar as fotos de uma viagem
function buscar_fotos_viagem($conexao, $id_viagem)
{
    $sql = "SELECT nome_arquivo FROM fotos_viagem WHERE id_viagem = :id_viagem ORDER BY id ASC";
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':id_viagem', (int)$id_viagem, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>