<?php
// Inclua seu arquivo de inicialização. Ele deve conter session_start() e a conexão $conn.
include_once 'include/selectUsuario.php'; // Verifique o caminho correto do seu arquivo

// Lógica para exibir mensagens de sucesso/erro (se o redirecionamento tiver passado um parâmetro GET)
if (isset($_GET['sucesso'])) {
    if ($_GET['sucesso'] == 'pedido_enviado') {
        echo '<div class="alert alert-success" role="alert">Solicitação de amizade enviada com sucesso!</div>';
    }
} elseif (isset($_GET['erro'])) {
    if ($_GET['erro'] == 'amizade_existente') {
        echo '<div class="alert alert-warning" role="alert">Vocês já são amigos ou já existe um pedido pendente!</div>';
    } elseif ($_GET['erro'] == 'auto_amizade') {
        echo '<div class="alert alert-danger" role="alert">Você não pode adicionar a si mesmo como amigo.</div>';
    } elseif ($_GET['erro'] == 'erro_db') {
        echo '<div class="alert alert-danger" role="alert">Ocorreu um erro ao processar sua solicitação. Tente novamente.</div>';
    }
}

// 1. Capturar o termo de busca (se existir)
$termo_busca = ''; // Variável para armazenar o termo de busca
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $termo_busca = $conn->real_escape_string($_GET['search']); // Sanitiza o input
}

// 2. Modificar a consulta SQL para incluir o filtro de busca
$sql_sugestoes_amizade = "SELECT
    u.id,
    u.usuario,
    u.nome_completo,
    u.foto
FROM
    usuarios u
WHERE
    u.id != " . $_SESSION['id'] . "
    AND u.id NOT IN (
        SELECT
            CASE
                WHEN a.usuario_solicitante = " . $_SESSION['id'] . " THEN a.usuario_destinatario
                ELSE a.usuario_solicitante
            END
        FROM
            amizades a
        WHERE
            a.usuario_solicitante = " . $_SESSION['id'] . " OR a.usuario_destinatario = " . $_SESSION['id'] . "
    )";

// Adiciona a condição de busca SE um termo foi fornecido
if (!empty($termo_busca)) {
    // Usamos LIKE '%termo%' para buscar o termo em qualquer parte do nome de usuário ou nome completo
    $sql_sugestoes_amizade .= " AND (u.usuario LIKE '%" . $termo_busca . "%' OR u.nome_completo LIKE '%" . $termo_busca . "%')";
}

$sql_sugestoes_amizade .= " ORDER BY u.usuario"; // Mantém a ordenação

$result_sugestoes = $conn->query($sql_sugestoes_amizade);

// ... (restante do seu HTML) ...
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        body { background-color: #ADD8E6; }
        .custom-box {
            background-color: #343A40;
            border-radius: 15px;
            padding: 10px;
            margin: 10px;
        }
        .sugestao-amigo-card {
            display: flex;
            align-items: center;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .sugestao-amigo-card img {
            margin-right: 15px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        .sugestao-amigo-card{
            background-color: #343A40;
        }
    </style>
    <meta charset="UTF-8">
    <title>Orkut</title>
</head>

<body class="bg-dark text-white">

    <div class="container">
        <div class="row text-center">

            <div class="col-md-3">
                <div class="custom-box">
                    <a href="principal.php"><img style="width: 50%;" src="./img/orkutlogo.png" alt="#" class="mb-3"></a>
                    <img src="uploads/<?php echo $foto; ?>" alt="Foto de perfil" style="width:150px; height:150px; border-radius: 50%; object-fit: cover;"><br>

                    <h3><?php echo $nome_completo; ?></h3><br>
                    <a href="./editar.php" class="btn btn-info btn-sm">Editar Perfil</a><br><br>
                    <a href="./addamigo.php" class="btn btn-info btn-sm">Adicionar Amigo</a><br><br>
                    <a href="./enviarmsg.php" class="btn btn-info btn-sm">Enviar Mensagem</a>
                </div>
            </div>


            <div class="col-md-9">
                <div class="custom-box">
                    <form action="" method="get" class="d-flex">
                        <input class="form-control me-2" type="search" name="search" placeholder="Pesquisar" aria-label="Search" value="<?php echo htmlspecialchars($termo_busca); ?>">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>
                </div>
                <?php
if ($result_sugestoes->num_rows > 0) {
    echo "<h3>Sugestões de Amizade</h3>";
    while ($row_sugestao = $result_sugestoes->fetch_assoc()) {
        $sugestao_id = $row_sugestao['id'];
        $sugestao_username = $row_sugestao['usuario'];
        $sugestao_nome_completo = $row_sugestao['nome_completo'];
        $sugestao_foto = "uploads/" . $row_sugestao['foto'];
        ?>
        <div class="sugestao-amigo-card">
            <img src="<?php echo $sugestao_foto; ?>" alt="Foto de <?php echo $sugestao_username; ?>" >
            <div>
                <strong><?php echo $sugestao_username; ?></strong><br>
                <span><?php echo $sugestao_nome_completo; ?></span>
                <form action="enviar_pedido_amizade.php" method="post">
                    <input type="hidden" name="destinatario_id" value="<?php echo $sugestao_id; ?>">
                    <button type="submit" class="btn btn-primary btn-sm mt-2">Adicionar Amigo</button>
                </form>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>Não há novas sugestões de amizade no momento.</p>";
}
?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// É uma boa prática fechar a conexão aqui, depois que tudo foi processado e exibido
$conn->close();
?>