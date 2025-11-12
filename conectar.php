<?php
// ==============================
// CONFIGURAÇÃO DO BANCO
// ==============================
$Servidor = 'localhost';
$nomeBanco = 'banco_teste';
$Usuario = 'fabio';
$Senha = '123';

$conn = new mysqli($Servidor, $Usuario, $Senha, $nomeBanco);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// ==============================
// CRIAÇÃO DA TABELA (se não existir)
// ==============================
$conn->query("
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100),
        email VARCHAR(100)
    )
");

// ==============================
// FUNÇÕES (Service + Model)
// ==============================

// Função para adicionar usuário
function adicionarUsuario($conn, $nome, $email) {
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome, $email);
    return $stmt->execute();
}

// Função para listar todos os usuários
function listarUsuarios($conn) {
    $sql = "SELECT * FROM usuarios";
    $resultado = $conn->query($sql);

    $usuarios = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    return $usuarios;
}

// ==============================
// LÓGICA PRINCIPAL (main)
// ==============================

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!empty($nome) && !empty($email)) {
        if (adicionarUsuario($conn, $nome, $email)) {
            $msg = "✅ Usuário adicionado com sucesso!";
        } else {
            $msg = "❌ Erro ao adicionar usuário.";
        }
    } else {
        $msg = "⚠️ Preencha todos os campos!";
    }
}

$usuarios = listarUsuarios($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Usuários</title>
    <style>
        body { font-family: Arial; background: #f3f3f3; padding: 20px; }
        h2 { color: #333; }
        form { background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px #ccc; width: 300px; }
        input, button { width: 100%; padding: 8px; margin: 5px 0; }
        ul { background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px #ccc; width: 300px; list-style: none; }
        li { margin-bottom: 8px; }
        .msg { margin: 10px 0; font-weight: bold; color: #007700; }
    </style>
</head>
<body>

<h2>Cadastro de Usuários</h2>

<form method="POST">
    <label>Nome:</label>
    <input type="text" name="nome" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Salvar</button>
</form>

<?php if ($msg): ?>
    <div class="msg"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>Lista de Usuários</h2>
<ul>
<?php foreach ($usuarios as $u): ?>
    <li><?= htmlspecialchars($u['nome']) ?> - <?= htmlspecialchars($u['email']) ?></li>
<?php endforeach; ?>
</ul>

</body>
</html>
