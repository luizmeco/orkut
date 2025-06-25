<?php
session_start();
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

$sql = "SELECT * FROM usuarios where id = " . $_SESSION['id'];
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $nome_completo = $row['nome_completo'];
    $username = $row['usuario'];
    $foto = $row['foto'];
}
?>