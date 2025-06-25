<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orkut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body class="bg-dark">
    <div class="container text-center">
        <div>
            <img style="width: 500px;" class="p-5" src="./img/orkutlogo.png" alt="#">
        </div>
        <form action="" method="POST">
            <div class="mb-5">
                <input type="text" name="usuario" class="form-control w-50 mx-auto" placeholder="login">
            </div>
            <div class="mb-5">
                <input type="password" name="senha" class="form-control w-50 mx-auto" placeholder="senha">
            </div>
            <div class="mb-5">
                <a href="registro.php" class="btn btn-secondary">Criar Login</a>
                <button class="btn btn-primary " type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "orkut";

// Estabelece a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // mostra os dados de todos os registros
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];

        if($usuario && $senha){
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['usuario'] == $usuario && $row['senha'] == $senha) {
                        session_start();
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['usuario'] = $usuario;
                        header("Location: principal.php");
                        exit();
                    }
                }
                echo "Usuário ou senha incorretos";
            } else {
                echo "Nenhum usuário encontrado";
            }
        }
    }
} else {
    echo "Sem resultados";
}
?>

</html>