<?php
include_once 'include/selectUsuario.php';
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $id_destinatario = $_GET['id'];
    }
    
}
$amigo_id = 0;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $mensagem = $_POST['mensagem'];
    $id_destinatario = $_POST['amigo'];

    $sql = "INSERT INTO mensagens (remetente, destinatario, mensagem)
    VALUES (" . $_SESSION['id'] . ", '$id_destinatario', '$mensagem')";
    $conn->query($sql);

    $sql_excluir_mensagem_respondida = "DELETE FROM mensagens
    where remetente = ". $id_destinatario ." and destinatario =" . $_SESSION['id'] . ";";
    $conn->query($sql_excluir_mensagem_respondida);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

<head>
    <style>
        body {
            background-color: #ADD8E6;

        }

        .custom-box {
            background-color: #f0f0f0;
            /* Cor de fundo */
            border-radius: 15px;
            /* Bordas arredondadas */
            padding: 10px;
            /* Espaçamento interno */
            margin: 10px;
            /* Espaçamento externo entre as colunas */
        }
    </style>
    <meta charset="UTF-8">
    <title>Orkut</title>
</head>

<body>

    <div class="container">
        <div class="row text-center">

            <!-- Coluna 01 -->
            <div class="col-md-3">
                <div class="custom-box">
                    <a href="principal.php"><img style="width: 50%;" src="./img/orkutlogo.png" alt="#"></a>
                    <img src="uploads/<?php echo $foto; ?>" alt="Foto de perfil" style="width:150px;"><br>

                    <h3><?php echo $nome_completo; ?></h3><br>
                    <a href="x">Editar Perfil</a><br><br>
                    <a href="./addamigo.php">Adicionar Amigo</a><br><br>
                    <a href="">Enviar Mensagem</a>
                </div>
            </div>


            <!-- Coluna 02 -->
             <div class="col-md-9">
                <div class="custom-box">
                    <form action="" method="post">
                        <div class="mb-3">
                            <h1>Enviar Mensagem</h1>
                            <label for="amigo" class="form-label">Escolha um Amigo</label>
                            <select class="form-control" id="amigo" name="amigo" required>
                                <!-- Opções de amigos devem ser geradas dinamicamente -->
                                <?php
             $sql_amigos = "SELECT
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
             AND a.status = 'aceita'";
             $result = $conn->query($sql_amigos);
             
             while ($row = $result->fetch_assoc()) {
                 $amigo_id = $row['amigo_id'];
                 $amigo_username = $row['amigo_username'];
                 echo '<option value="' . $amigo_id . '" ' . ($amigo_id == $id_destinatario ? 'selected' : '') . '>' . $amigo_username . '</option>';
             }
             ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mensagem" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="mensagem" name="mensagem" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php

  
?>

</html>
