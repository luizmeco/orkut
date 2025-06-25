<?php
// registro.php (ou criarconta.php)

// Inclua APENAS o arquivo de conexão, pois o usuário ainda não está logado
// Certifique-se de que include/conexao.php NÃO inicia a sessão.
include_once 'conexao.php'; // Caminho para o seu arquivo de conexão

// Mensagens de feedback para o usuário
$feedback_message = '';
$feedback_class = ''; // Para estilos de sucesso/erro (Bootstrap)

// Inicializa variáveis para o formulário (para manter valores após erro)
$username = '';
$nome_completo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Captura e sanitiza os dados do formulário
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $nome_completo = $conn->real_escape_string($_POST['nome'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 2. Validações
    if (empty($username) || empty($nome_completo) || empty($password) || empty($confirm_password)) {
        $feedback_message = "Por favor, preencha todos os campos.";
        $feedback_class = "alert-danger";
    } elseif ($password !== $confirm_password) {
        $feedback_message = "As senhas não coincidem.";
        $feedback_class = "alert-danger";
    } else {
        // 3. Verifica se o username já existe
        $stmt_check_username = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt_check_username->bind_param("s", $username);
        $stmt_check_username->execute();
        $result_check_username = $stmt_check_username->get_result();

        if ($result_check_username->num_rows > 0) {
            $feedback_message = "Este nome de usuário já está em uso. Por favor, escolha outro.";
            $feedback_class = "alert-warning";
        } else {
            // 4. Processa o upload da foto
            $foto_destino = 'default.jpg'; // Valor padrão caso não haja upload ou falhe
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $target_dir = "uploads/";
                // Gera um nome único para o arquivo para evitar sobrescrever
                $imageFileType = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $new_file_name = uniqid() . "." . $imageFileType;
                $foto_destino_completo = $target_dir . $new_file_name;

                // Validações adicionais para a imagem (tipo, tamanho, etc.)
                $check = getimagesize($_FILES['foto']['tmp_name']);
                if($check === false) {
                    $feedback_message = "O arquivo enviado não é uma imagem.";
                    $feedback_class = "alert-danger";
                } elseif ($_FILES['foto']['size'] > 500000) { // Limite de 500KB
                    $feedback_message = "A imagem é muito grande. Máximo de 500KB.";
                    $feedback_class = "alert-danger";
                } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $feedback_message = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
                    $feedback_class = "alert-danger";
                } else {
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_destino_completo)) {
                        $foto_destino = $new_file_name; // Apenas o nome do arquivo, não o caminho completo
                    } else {
                        $feedback_message = "Erro ao fazer upload da imagem. Tente novamente.";
                        $feedback_class = "alert-danger";
                    }
                }
            }

            // Se não houve erro até agora (ou se o upload foi bem-sucedido/usou default)
            if (empty($feedback_message)) {
                // 5. REMOVIDA: Hash da senha para segurança
                // A senha será armazenada em texto puro (NÃO RECOMENDADO)
                $plain_password = $password;

                // 6. Insere os dados no banco de dados usando Prepared Statement
                // O tipo do bind_param para a senha mudará de 's' para 's' (ainda string, mas sem hash)
                $stmt_insert = $conn->prepare("INSERT INTO usuarios (usuario, nome_completo, senha, foto) VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param("ssss", $username, $nome_completo, $plain_password, $foto_destino);

                if ($stmt_insert->execute()) {
                    $feedback_message = "Conta criada com sucesso! Você já pode fazer login.";
                    $feedback_class = "alert-success";
                    // Redireciona para a página de login após o sucesso
                    header('Location: login.php?cadastro=sucesso');
                    exit;
                } else {
                    $feedback_message = "Erro ao criar conta: " . $stmt_insert->error;
                    $feedback_class = "alert-danger";
                }
                $stmt_insert->close();
            }
        }
        $stmt_check_username->close();
    }
}

// Inicializa $username e $nome_completo para evitar "Undefined variable" no atributo value
$username = $username ?? '';
$nome_completo = $nome_completo ?? '';

// Feche a conexão se $conn for global e não será mais usado após este script.
// $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        body { background-color: #ADD8E6; }
        .form-control.w-50 {
            max-width: 400px; /* Limita a largura dos campos para melhor visualização */
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container text-center">
        <div>
            <img style="width: 500px;" class="p-5" src="./img/orkutlogo.png" alt="Orkut Logo">
        </div>
        
        <?php if (!empty($feedback_message)): ?>
            <div class="alert <?php echo $feedback_class; ?> w-50 mx-auto" role="alert">
                <?php echo $feedback_message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" name="username" class="form-control w-50 mx-auto" placeholder="Usuário" required value="<?php echo htmlspecialchars($username); ?>">
            </div>
            <div class="mb-3">
                <input type="text" name="nome" class="form-control w-50 mx-auto" placeholder="Nome completo" required value="<?php echo htmlspecialchars($nome_completo); ?>">
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control w-50 mx-auto" placeholder="Senha" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control w-50 mx-auto"
                    placeholder="Confirmar senha" required>
            </div>
            <div class="mb-3">
                <label for="foto_upload" class="form-label">Foto de Perfil</label>
                <input type="file" name="foto" id="foto_upload" class="form-control w-50 mx-auto" accept="image/*" required>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Criar Nova Conta</button>
            </div>
        </form>
        <p class="mt-3">Já tem uma conta? <a href="login.php">Faça login aqui!</a></p>
    </div>
</body>
</html>