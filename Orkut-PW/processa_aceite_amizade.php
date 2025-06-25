<?php
// processa_aceite_amizade.php

include_once 'include/selectUsuario.php'; // Inclua seu arquivo de inicialização

// Redireciona se o usuário não estiver logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se a requisição é um POST e se o ID da amizade (ou do solicitante) foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['amizade_id'])) {

    $id_amizade_a_aceitar = (int)$_POST['amizade_id'];
    $usuario_logado_id = $_SESSION['id'];

    // Prepara a consulta para atualizar o status da amizade
    // É CRUCIAL verificar se o 'usuario_destinatario' da amizade é o usuário logado
    // Isso impede que um usuário aceite uma amizade que não é destinada a ele.
    $stmt = $conn->prepare("UPDATE amizades SET status = 'aceita' WHERE id = ? AND usuario_destinatario = ? AND status = 'pendente'");
    $stmt->bind_param("ii", $id_amizade_a_aceitar, $usuario_logado_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Sucesso: a amizade foi aceita
            header("Location: principal.php?sucesso=amizade_aceita"); // Redireciona para a página principal ou onde quiser
            exit();
        } else {
            // Nenhuma linha afetada: a amizade pode já ter sido aceita, não encontrada, ou não era para este usuário
            header("Location: principal.php?erro=amizade_nao_encontrada_ou_ja_aceita");
            exit();
        }
    } else {
        // Erro no banco de dados
        error_log("Erro ao aceitar amizade: " . $stmt->error);
        header("Location: principal.php?erro=erro_db_aceitar_amizade");
        exit();
    }
    $stmt->close();

} else {
    // Se não for POST ou 'amizade_id' não foi enviado
    header("Location: principal.php"); // Redireciona para evitar acesso direto
    exit();
}

$conn->close();
?>