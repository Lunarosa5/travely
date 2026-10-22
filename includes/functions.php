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
    // Calcula o total de viagens(count(*) conta quantas linhas de viagens estão registradas para aquele usuário), faz a média das notas (AVG(avaliacao) calcula a média aritmética de um conjunto de valores em uma coluna de uma tabela) e soma a diferença de dias (SUM(data_fim - data_inicio + 1) calcula e retorna a soma total dos valores de uma coluna || além disso O + 1 é adicionado para garantir que uma viagem que comece e termine no mesmo dia conte como 1 dia de viagem. )
    $sql = "SELECT 
                COUNT(*) as total_viagens,
                AVG(avaliacao) as media_notas,
                SUM(data_fim - data_inicio + 1) as dias_viagens
            FROM viagens 
            WHERE id_usuario = :id_usuario";

    // O bloco tenta (try) rodar o código, o método prepare() valida a query e o bindParam() vincula o código :id_usuario ao ID do usuário que está na sessão. Por fim, o execute() roda a linha e o fetch(PDO::FETCH_ASSOC) puxa o resultado como um array com os nomes definidos no SELECT (ex: ['total_viagens' => 12, ...]).
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

    // Estrutura a consulta SQL para selecionar apenas as duas viagens mais recentes cadastradas pelo usuário específico.
    $sql = "SELECT * FROM viagens 
            WHERE id_usuario = :id_usuario 
            ORDER BY id DESC 
            LIMIT 2";

    // Executa a consulta e o fetchAll() cria uma lista contendo todas as linhas encontradas (neste caso, até 2 viagens), permitindo que no HTML um loop como o foreach seja usado para listá-las.
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Se houver um erro, ele é retornado
    } catch (PDOException $e) {
        return [];
    }
}

// FUNÇÕES DE GESTÃO DE VIAGENS (CRUD QUE FICARÁ NA PASTA PAGES)

// cadastrar_viagem: Função para cadastrar uma nova viagem

// Cria função cadastrar_viagem com os parâmetros. Primeiramente, pega o dia atual e faz três checagens: a data de início não pode ser no futuro, a data de término também não pode ser no futuro, e o usuário não pode dizer que a viagem terminou antes de ter começado. Se qualquer uma dessas condições falhar, a função exibe um aviso de erro e cancela a operação retornando false.

function cadastrar_viagem($conexao, $id_usuario, $titulo, $destino, $data_inicio, $data_fim, $relato, $avaliacao)
{
    $hoje = date('Y-m-d');

    // Valida se as datas são válidas: a data de início e fim não podem ser maiores que a data atual, e a data de início não pode ser maior que a data de fim.
    if ($data_inicio > $hoje || $data_fim > $hoje || $data_inicio > $data_fim) {
        echo "<div class='container-mensagem'><p class='caixa-mensagem erro'>Data inválida! Tente novamente.</p></div>";
        return false;
    }

    // Monta a estrutura de inserção com RETURNING id que faz com que o banco de dados, logo após criar a linha, devolva imediatamente o número do ID que acabou de ser gerado para aquela viagem.
    $sql = "INSERT INTO viagens (id_usuario, titulo, destino, data_inicio, data_fim, relato, avaliacao) 
            VALUES (:id_usuario, :titulo, :destino, :data_inicio, :data_fim, :relato, :avaliacao) RETURNING id";

    // O bloco associa todas as variáveis recebidas como parâmetros às variáveis com o bindParam. Ao executar ($stmt->execute()), o código faz um fetch() para capturar o ID que o banco de dados devolveu por causa do comando RETURNING e o envia de volta para quem chamou a função.
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

        // Caso um erro aconteça, exibe a mensagem correspondente.
    } catch (PDOException $e) {
        echo "<p>Erro ao cadastrar viagem: " . $e->getMessage() . "</p>";
        return false;
    }
}

// relatorio: Função para possibilitar o usuário de ver todas suas viagens relatadas (da cadastrada mais recentemente para a mais antiga)
function relatorio($conexao)
{

    // A função pega o ID do usuário conectado e guarda em $id_usuario e a consulta SQL busca todas as colunas da tabela de viagens, filtrando para que apenas os registros do usuário conectado sejam exibidos. O comando ORDER BY id DESC garante que o histórico seja listado em uma ordem onde as viagens inseridas mais recentemente aparecem no topo da página.

    $id_usuario = $_SESSION['id_usuario'];

    $sql = "SELECT * FROM viagens WHERE id_usuario = :id_usuario ORDER BY id DESC";

    // Realiza a consulta e após a execução, o método fetchAll(PDO::FETCH_ASSOC) extrai todas as linhas encontradas do banco e as armazenada em uma array associativa. Caso alguem erro aconteça o catch pega o erro e retorna uma lista vazia [], ou seja, não retorna nada.
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

    // A função pega o ID do usuário conectado e guarda em $id_usuario, a query SQL utiliza o operador ILIKE (nativo do PostgreSQL), que realiza uma busca por texto sem diferenciar letras maiúsculas de minúsculas (fazendo com que "Bahia", "bahia" ou "BAHIA" tragam o mesmo resultado). O comando ORDER BY data_inicio DESC organiza a lista colocando as viagens com as datas de início mais recentes primeiro.
    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT * FROM viagens WHERE id_usuario = :id_usuario AND destino ILIKE :destino ORDER BY data_inicio DESC";

    // Dentro de try, o código concatena o símbolo % antes e depois da palavra pesquisada (transformando "São" em %São%). No banco de dados, isso funciona como um caractere curinga, permitindo encontrar o termo em qualquer parte da frase (encontrando tanto "São Paulo" quanto "São Carlos"). Além do mais o uso do fetchAll(PDO::FETCH_ASSOC) captura todas as linhas encontradas do banco e as armazenada em uma array associativa.
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
    //  Antes de apagar a viagem em si, o código roda um comando DELETE na tabela secundária fotos_viagem, isso porque o banco possui chave estrangeira (Foreign Keys) e tentar apagar uma viagem que ainda tem fotos cadastradas gera um erro fatal.
    $sql_fotos = "DELETE FROM fotos_viagem WHERE id_viagem = :id";
    $stmt_fotos = $conexao->prepare($sql_fotos);
    $stmt_fotos->bindParam(':id', $id);
    $stmt_fotos->execute();

    // Aqui é feita a exclusão da viagem inserida. Mas para garantir que o sistema só irá apagar o registro se o ID da viagem bater com o ID do usuário que está logado é usado o WHERE id = :id AND id_usuario = :id_usuario. Isso impede que um usuário apague as viagens de outra pessoa mudando o ID manualmente.
    $sql = "DELETE FROM viagens WHERE id = :id AND id_usuario = :id_usuario";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_usuario", $id_usuario);

        // Mensagem de sucesso
        if ($stmt->execute()) {
            echo "<p class='caixa-mensagem'>Viagem apagada com sucesso!</p>";
        }

        // Mensagem de erro
    } catch (PDOException $e) {
        echo "<p class='caixa-mensagem erro'>Erro ao apagar viagem: " . $e->getMessage() . "</p>";
    }
}


// atualizar: Função para atualizar os dados de uma viagem
function atualizar($conexao, $id, $destino, $data_inicio, $data_fim, $avaliacao, $relato, $id_usuario)
{
    // O comando UPDATE modifica a linha da tabela viagens. Ele mapeia todos os campos alteráveis (destino, datas, nota e relato) usando parâmetros nomeados (como :destino). E com o WHERE id = :id AND id_usuario = :id_usuario um usuário é impedido de alterar os dados de viagens de outros usuários.
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

    // Seleciona apenas o id, nome e email da tabela usuarios onde o id do banco de dados é o mesmo que o id do usuário conectado.
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

    // Antes de alterar qualquer dado, a função faz uma checagem para confirmar a identidade do usuário. Ela busca o hash da senha salva no banco e usa a função segura password_verify() para comparar com a senha digitada no formulário.
    $sql = "SELECT senha FROM usuarios WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se o usuário errar a senha atual, o sistema bloqueia o processo imediatamente e cancela a alteração.
    if (!$user || !password_verify($senha_atual, $user['senha'])) {
        echo "<p>Senha atual incorreta! As alterações não foram salvas.</p>";
        return false;
    }

    // Avalia se o usuário digitou algo no campo de "Nova Senha". Se digitou o código gera um hash seguro usando criptografia PASSWORD_BCRYPT e monta uma query que atualiza o nome, e-mail e a nova senha. Se foi deixado em branco o sistema entende que o usuário quer apenas mudar o nome ou e-mail, gerando uma query menor que mantem a senha antiga.

    if (!empty($senha_nova)) {
        $nova_senha_hash = password_hash($senha_nova, PASSWORD_BCRYPT);
        $sql_update = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bindParam(':senha', $nova_senha_hash);
    } else {
        $sql_update = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
        $stmt_update = $conexao->prepare($sql_update);
    }

    // Os parâmetros comuns (ID, nome e e-mail) são vinculadose ao executar a query com sucesso, a função atualiza a variável global $_SESSION['nome_usuario']. Isso garante que, se o usuário mudou o nome de "Luna" para "Luana", o campo onde exibe o nome do usuário no site mude o texto na hora para o nome novo sem precisar deslogar e logar de novo.
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

        // Se houver um erro (como tentar cadastrar um e-mail que já existe), o bloco catch pega o erro e exibe a mensagem na tela para ajudar no ajuste.
    } catch (PDOException $e) {
        echo "<p>Erro ao atualizar perfil: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Função para excluir a conta do usuário e todas as suas viagens
function excluir_conta($conexao, $id_usuario)
{

    // O sistema inicia o processo de exclusão de dados em cascata. Como a tabela viagens possui chave estrangeira (Foreign Keys) ligadas à tabela usuarios, tentar deletar o usuário diretamente faria o banco rejeitar a operação e um erro fatal aconteceria. Ao deletar todas as viagens vinculadas àquele ID de usuário primeiro, depois erros não acontecerão.

    // Primeiramente as viagens são deletadas.
    try {
        $sql_viagens = "DELETE FROM viagens WHERE id_usuario = :id";
        $stmt_viagens = $conexao->prepare($sql_viagens);
        $stmt_viagens->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt_viagens->execute();

        // Após isso apaga o usuário.
        $sql_user = "DELETE FROM usuarios WHERE id = :id";
        $stmt_user = $conexao->prepare($sql_user);
        $stmt_user->bindValue(':id', (int)$id_usuario, PDO::PARAM_INT);
        $stmt_user->execute();

        // Encerra a sessão e redireciona para o login.
        session_destroy();
        header("Location: ../index.php");
        exit();

        // Se um erro acontecer, exibe uma mensagem correspondente à ele.
    } catch (PDOException $e) {
        echo "Erro ao excluir conta: " . $e->getMessage() . "</p>";
    }
}

// FUNÇÕES DE UPLOAD DE IMAGENS:

// foto_viagem: função para mover até 10 fotos para a pasta uploads e salvar no banco de dados
function foto_viagem($conexao, $id_viagem, $arquivo)
{
// A função começa checando se há algum arquivo de fato sendo enviado. Se o campo de upload veio vazio, ela interrompe a execução retornando true, pois o envio de fotos é opcional.
    if (empty($arquivo['name'])) {
        return true;
    }

    // Em seguida, ela define o caminho absoluto da pasta onde as fotos ficarão salvas.
    $pasta_destino = __DIR__ . '/../uploads/';

    // Garante que o PHO consiga lidar da mesma forma tanto com o envio de uma única foto quanto com o envio de várias fotos de uma vez.

    // "$nomes = is_array($arquivo['name'])" => os nomes dos arquivos já estão em uma lista? "? $arquivo['name']" => Se SIM, repassa as informações de "$arquivo['name']" para "$nomes" nomes das fotos). ": [$arquivo['name']]" Se NÃO, ou seja, ele é uma única foto, o PHP pega esse nome único e coloca dentro de colchetes [], transformando-o em uma lista de um único item. Isso permite o uso do foreach, para as fotos serem processadas corretamente.
    $nomes = is_array($arquivo['name']) ? $arquivo['name'] : [$arquivo['name']];

    // "tmp_name" => é uma pasta temporária onde as imagens enviadas são armazenadas temporáriamente antes de serem enviadas para a pasta uploads.
// "is_array($arquivo['tmp_name'])" => Confirma se os caminhos temporários das imagens já estão em uma lista. "? $arquivo['tmp_name']" => Se SIM, repassa essa lista para a variável $tmp_names. "[$arquivo['tmp_name']]" => Se NÃO, ou seja, somente uma foto foi enviada, o PHP pega o único caminho temporário e coloca dentro de uma lista [].
    $tmp_names = is_array($arquivo['tmp_name']) ? $arquivo['tmp_name'] : [$arquivo['tmp_name']];

// Caso erros acontecam, o PHP também cria uma lista para eles. ENtão "is_array($arquivo['error'])" => verifica se os códigos de erro já estão em uma lista, sendo vários árquivos. "? $arquivo['error']" => Se SIM ele repassa esssa lista de números de erro para a variável $erros. Se NÃO "[$arquivo['error']];" => é um único erro, uma foto foi enviada, então ele pega o número de erro único e coloca dentro de uma lista.
    $erros = is_array($arquivo['error']) ? $arquivo['error'] : [$arquivo['error']];

    // O laço foreach passa por cada arquivo enviado. Primeiro, ele confere se o nome não está em branco e se o arquivo chegou sem erros de upload (UPLOAD_ERR_OK). Depois, extrai a extensão do arquivo (ex: .jpg ou .png) em letras minúsculas (strtolower). Para evitar erros, o código gera um nome completamente novo e aleatório usando a função uniqid(), combinada com o índice $i (ex: foto_65d2f1a3b4c5e_0.jpg). Isso impede que usuários que enviam arquivos com o mesmo nome (como foto.jpg) apaguem arquivos antigos uns dos outros. A função move_uploaded_file retira a foto da área temporária do servidor e a joga definitivamente na pasta de uploads.
    foreach ($nomes as $i => $nome) {
        if (!empty($nome) && $erros[$i] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
            $novo_nome = "foto_" . uniqid() . "_" . $i . "." . $extensao;

            // Assim que o arquivo é movido com sucesso para a pasta, o sistema precisa guardar essa referência. Ele executa uma query de INSERT na tabela fotos_viagem, salvando apenas o número do ID da viagem correspondente e o novo nome do arquivo textual gerado. Dessa forma, é possível recuperar todas as fotos fazendo um SELECT depois.
            if (move_uploaded_file($tmp_names[$i], $pasta_destino . $novo_nome)) {
                $sql = "INSERT INTO fotos_viagem (id_viagem, nome_arquivo) VALUES (:id_viagem, :nome_arquivo)";
                $stmt = $conexao->prepare($sql);
                $stmt->bindValue(':id_viagem', (int)$id_viagem, PDO::PARAM_INT);
                $stmt->bindValue(':nome_arquivo', $novo_nome);
                $stmt->execute();
            }
        }
    }

    return true;
}

// Função para buscar as fotos de uma viagem
function buscar_fotos_viagem($conexao, $id_viagem)
{

// Seleciona a coluna nome_arquivo da tabela fotos_viagem sendo filtrada para trazer apenas registros que pertecem aquela viagem específica (id_viagem = :id_viagem). Também, o trecho ORDER BY id ASC organiza os resultados da primeira foto adicionada para a mais recente.
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
