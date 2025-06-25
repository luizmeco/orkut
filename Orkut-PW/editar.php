<?php
include_once 'include/selectUsuario.php';
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
            background-color: #343A40;
            /* Cor de fundo */
            border-radius: 15px;
            /* Bordas arredondadas */
            padding: 10px;
            /* Espaçamento interno */
            margin: 10px;
            /* Espaçamento externo entre as colunas */
        }
        input{
            background-color: #6C757D !important;
            color: white !important;
        }
        </style>
    <meta charset="UTF-8">
    <title>Orkut</title>
</head>

<body class="bg-dark text-white">

    <div class="container">
        <div class="row text-center">

            <!-- Coluna 01 -->
            <div class="col-md-3">
                <div class="custom-box">
                    <a href="principal.php"><img style="width: 50%;" src="./img/orkutlogo.png" alt="#" class="mb-3"></a>
                    <img src="uploads/<?php echo $foto; ?>" alt="Foto de perfil" style="width:150px;"><br>

                    <h3><?php echo $nome_completo; ?></h3><br>
                    <a href="./editar.php" class="btn btn-info btn-sm">Editar Perfil</a><br><br>
                    <a href="./addamigo.php" class="btn btn-info btn-sm">Adicionar Amigo</a><br><br>
                    <a href="./enviarmsg.php" class="btn btn-info btn-sm">Enviar Mensagem</a>
                </div>
            </div>


            <!-- Coluna 02 -->
             <div class="col-md-9">
                <div class="custom-box">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $nome_completo; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os campos do formulário
    $novo_nome = $_POST['nome'] ?? '';
    $nova_senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Verificar se as senhas coincidem
    if ($nova_senha !== $confirmar_senha) {
        echo "<h3 style='color:red;'>Erro: As senhas não coincidem. <a href='javascript:history.back()'>Voltar</a></h3>";
        exit;
    }

    // Faz o upload da nova foto, se fornecida
    $novo_foto_destino = $_SESSION['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_nome = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $novo_foto_destino = 'uploads/' . basename($foto_nome);
        $nome_nova_foto = basename($foto_nome);

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        move_uploaded_file($foto_tmp, $novo_foto_destino);
    }

    // Atualiza a sessão com os novos dados
    $_SESSION['nome'] = $novo_nome;
    $_SESSION['senha'] = $nova_senha; // Certifique-se de que a senha seja armazenada de forma segura
    $_SESSION['foto'] = $novo_foto_destino;

    // Atualiza o banco de dados com os novos dados
    $sql = "UPDATE usuarios SET nome_completo = '$novo_nome', senha = '$nova_senha', foto = '$nome_nova_foto' WHERE id = " . $_SESSION['id'];
    $conn->query($sql);

    echo "<script>alert('Perfil atualizado com sucesso!');</script>";

    header('Location: principal.php');
    exit;
}
?>

</html>