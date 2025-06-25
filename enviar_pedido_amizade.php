<?php
// Inclua seu arquivo de inicialização para sessão e conexão com o banco de dados
// Ele deve conter session_start() e a conexão $conn
include_once 'include/selectUsuario.php'; // Verifique o caminho correto do seu arquivo

// 1. Verifica se a requisição é um POST e se o 'destinatario_id' foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario_id'])) {

    $usuario_solicitante = $_SESSION['id'];
    $usuario_destinatario = (int)$_POST['destinatario_id']; // Garante que é um inteiro

    // --- (OPCIONAL: RECOLOQUE AS VALIDAÇÕES PARA MELHOR SEGURANÇA) ---
    // Validação básica: O usuário não pode enviar solicitação para si mesmo
    if ($usuario_solicitante == $usuario_destinatario) {
        header("Location: addamigo.php?erro=auto_amizade");
        exit();
    }

    // Validação: Verificar se a amizade já existe (pendente ou aceita)
    $stmt_check = $conn->prepare("
        SELECT id FROM amizades
        WHERE (usuario_solicitante = ? AND usuario_destinatario = ?)
           OR (usuario_solicitante = ? AND usuario_destinatario = ?)
    ");
    $stmt_check->bind_param("iiii", $usuario_solicitante, $usuario_destinatario, $usuario_destinatario, $usuario_solicitante);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header("Location: addamigo.php?erro=amizade_existente");
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();
    // --- (FIM DAS VALIDAÇÕES OPCIONAIS) ---

    // Se as validações (se existirem) passaram, insere a nova solicitação
    $status_amizade = 'pendente';

    // 2. Prepara e executa a inserção no banco de dados
    $stmt_insert = $conn->prepare("INSERT INTO amizades (usuario_solicitante, usuario_destinatario, status) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("iis", $usuario_solicitante, $usuario_destinatario, $status_amizade);

    if ($stmt_insert->execute()) {
        // 3. Sucesso: Redireciona para a página de adicionar amigo com mensagem de sucesso
        header("Location: addamigo.php?sucesso=pedido_enviado");
        exit(); // Importante: Garante que o script pare após o redirecionamento
    } else {
        // 4. Erro: Registra o erro e redireciona com mensagem de erro
        error_log("Erro ao inserir amizade: " . $stmt_insert->error); // Para depuração no log do servidor
        header("Location: addamigo.php?erro=erro_db");
        exit(); // Importante
    }

    $stmt_insert->close();

} else {
    // Se não for POST ou 'destinatario_id' não estiver definido, redireciona para a página de sugestões
    header("Location: addamigo.php");
    exit();
}

// Fecha a conexão com o banco de dados
$conn->close();
?>