<?php
// ... (Seu código PHP existente no principal.php, incluindo 'include/selectUsuario.php') ...
include_once 'include/selectUsuario.php';
// Lógica para exibir mensagens de sucesso/erro (se o redirecionamento tiver passado um parâmetro GET)
if (isset($_GET['sucesso'])) {
    if ($_GET['sucesso'] == 'amizade_aceita') {
        echo '<div class="alert alert-success" role="alert">Amizade aceita com sucesso!</div>';
    }
} elseif (isset($_GET['erro'])) {
    if ($_GET['erro'] == 'amizade_nao_encontrada_ou_ja_aceita') {
        echo '<div class="alert alert-warning" role="alert">A solicitação de amizade não foi encontrada ou já foi aceita.</div>';
    } elseif ($_GET['erro'] == 'erro_db_aceitar_amizade') {
        echo '<div class="alert alert-danger" role="alert">Ocorreu um erro ao aceitar a amizade. Tente novamente.</div>';
    }
}
// ... (restante do seu código PHP, como as consultas de mensagens, etc.) ...
?>

<!DOCTYPE html>
<html lang="pt-BR">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

<head>
    <style>
        body { background-color: #ADD8E6; }
        .custom-box {
            background-color: #f0f0f0;
            border-radius: 15px;
            padding: 10px;
            margin: 10px;
        }
        .friend-card, .pending-friend-card {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            text-align: left;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px;
        }
        .friend-card img, .pending-friend-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .pending-friend-card button {
            margin-left: auto; /* Alinha o botão à direita */
        }
    </style>
    <meta charset="UTF-8">
    <title>Orkut</title>
</head>

<body>

    <div class="container">
        <div class="row text-center">

            <div class="col-md-3">
                <div class="custom-box">
                    <a href="principal.php"><img style="width: 50%;" src="./img/orkutlogo.png" alt="#"></a>
                    <img src="uploads/<?php echo $foto;?>" alt="Foto de perfil" style="width:150px; height:150px; border-radius: 50%; object-fit: cover;"><br>

                    <h3><?php echo $nome_completo; ?></h3><br>
                    <a href="./editar.php">Editar Perfil</a><br><br>
                    <a href="./addamigo.php">Adicionar Amigo</a><br><br>
                    <a href="enviarmsg.php">Enviar Mensagem</a><br><br>
                    
                    <a href="logout.php" class="btn btn-danger btn-sm mt-2">Sair / Deslogar</a>
                </div>
            </div>


            <div class="col-md-6">
                <div class="custom-box">
                    <p>Nome: <?php echo $nome_completo; ?></p>
                    <p>Username: <?php echo $username; ?></p>
                </div>

                <?php
$sql_mensagens_recebidas = "SELECT
m.id AS mensagem_id,
m.mensagem AS mensagem_texto,
u_remetente.id AS remetente_id,
u_remetente.usuario AS remetente_username,
u_remetente.foto AS remetente_foto
FROM
mensagens m
INNER JOIN
usuarios u_remetente ON m.remetente = u_remetente.id
WHERE
m.destinatario = " . $_SESSION['id'] . "
ORDER BY
m.id DESC;"; 
                 
$result_mensagens = $conn->query($sql_mensagens_recebidas);
                 
if ($result_mensagens->num_rows > 0) {
    echo "<h3>Mensagens Recebidas</h3>";
    while ($row_msg = $result_mensagens->fetch_assoc()) {
        $mensagem_id = $row_msg['mensagem_id'];
        $mensagem_texto = $row_msg['mensagem_texto'];
        $remetente_id = $row_msg['remetente_id'];
        $remetente_username = $row_msg['remetente_username'];
        $remetente_foto = "uploads/" . $row_msg['remetente_foto'];
        ?>
        <div class="custom-box">
            <img src="<?php echo $remetente_foto; ?>" alt="Foto de perfil do remetente" style="width:50px; height:50px; border-radius:50%; object-fit:cover;"><br>
            <p><strong><?php echo $remetente_username; ?>:</strong> <?php echo $mensagem_texto; ?></p>
            <a href="enviarmsg.php?id=<?php echo $remetente_id; ?>" class="btn btn-info btn-sm">Responder</a>
        </div>
        <?php
    }
} else {
    echo "<p>Nenhuma mensagem recebida.</p>";
}
?>
            </div>


            <div class="col-md-3">
                <div class="custom-box">
                    <h4>Solicitações de Amizade</h4>
                    <?php
                    $sql_solicitacoes_pendentes = "SELECT
                        a.id AS amizade_id,
                        u_solicitante.id AS solicitante_id,
                        u_solicitante.usuario AS solicitante_username,
                        u_solicitante.foto AS solicitante_foto
                    FROM
                        amizades a
                    INNER JOIN
                        usuarios u_solicitante ON a.usuario_solicitante = u_solicitante.id
                    WHERE
                        a.usuario_destinatario = " . $_SESSION['id'] . "
                        AND a.status = 'pendente'
                    ORDER BY
                        u_solicitante.usuario;";
                    
                    $result_pendentes = $conn->query($sql_solicitacoes_pendentes);
                    
                    if ($result_pendentes->num_rows > 0) {
                        while ($row_pendente = $result_pendentes->fetch_assoc()) {
                            $amizade_id = $row_pendente['amizade_id']; // ID da linha da amizade
                            $solicitante_id = $row_pendente['solicitante_id'];
                            $solicitante_username = $row_pendente['solicitante_username'];
                            $solicitante_foto = "uploads/" . $row_pendente['solicitante_foto'];
                            ?>
                            <div class="pending-friend-card">
                                <img src="<?php echo $solicitante_foto; ?>" alt="Foto de <?php echo $solicitante_username; ?>">
                                <span><?php echo $solicitante_username; ?></span>
                                <form action="processa_aceite_amizade.php" method="post">
                                    <input type="hidden" name="amizade_id" value="<?php echo $amizade_id; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Aceitar</button>
                                </form>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Nenhuma solicitação pendente.</p>";
                    }
                    ?>

                    <hr> <h4>Meus Amigos</h4>
                    <?php
                    $sql_amigos_aceitos = "SELECT
                        u.id AS amigo_id,
                        u.usuario AS amigo_username,
                        u.foto AS amigo_foto
                    FROM
                        amizades a
                    INNER JOIN
                        usuarios u ON (
                            CASE
                                WHEN a.usuario_solicitante = " . $_SESSION['id'] . " THEN a.usuario_destinatario
                                ELSE a.usuario_solicitante
                            END = u.id
                        )
                    WHERE
                        (a.usuario_solicitante = " . $_SESSION['id'] . " OR a.usuario_destinatario = " . $_SESSION['id'] . ")
                        AND a.status = 'aceita'
                    ORDER BY
                        u.usuario;";
                    $result_amigos_aceitos = $conn->query($sql_amigos_aceitos);
                    
                    if ($result_amigos_aceitos->num_rows > 0) {
                        while ($row_amigo = $result_amigos_aceitos->fetch_assoc()) {
                            $amigo_id = $row_amigo['amigo_id'];
                            $amigo_username = $row_amigo['amigo_username'];
                            $amigo_foto = "uploads/" . $row_amigo['amigo_foto'];
                            ?>
                            <div class="friend-card">
                                <img src="<?php echo $amigo_foto; ?>" alt="<?php echo $amigo_username; ?>'s photo">
                                <span><?php echo $amigo_username; ?></span>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Você ainda não tem amigos aceitos.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>