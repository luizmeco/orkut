<?php
// include/conexao.php

$host = "localhost";
$user = "root";
$pass = "";
$db = "orkut";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conn->connect_error);
}
?>