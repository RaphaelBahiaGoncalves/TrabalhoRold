<?php
// ========================================
// CONFIGURAÇÃO DO ARQUIVO .TXT
// ========================================
define('ARQUIVO_CADASTROS', __DIR__ . '/cadastros.txt');

// Função: Ler cadastros do .txt
function lerCadastros(): array {
    $cadastros = [];
    if (!file_exists(ARQUIVO_CADASTROS)) {
        file_put_contents(ARQUIVO_CADASTROS, ""); // cria vazio
        return $cadastros;
    }

    $linhas = file(ARQUIVO_CADASTROS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        $dados = explode('|', trim($linha));
        if (count($dados) === 4) {
            $cadastros[] = [
                'id' => $dados[0],
                'NomeCliente' => $dados[1],
                'SobrenomeCliente' => $dados[2],
                'Sexo' => strtoupper($dados[3])
            ];
        }
    }
    return $cadastros;
}

// ========================================
// PROCESSAMENTO DA PESQUISA
// ========================================
$pesquisa = $_POST['Sexo'] ?? '';
$pesquisa = strtoupper(trim($pesquisa));
$resultados = [];
$encontrados = 0;

if ($pesquisa === 'M' || $pesquisa === 'F') {
    $todos = lerCadastros();
    foreach ($todos as $pessoa) {
        if ($pessoa['Sexo'] === $pesquisa) {
            $resultados[] = $pessoa;
            $encontrados++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pesquisa por Sexo</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e2);
            color: #333;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 { text-align: center; color: #5a67d8; margin-bottom: 10px; }
        h2 { text-align: center; color: #4c51bf; font-size: 1.3em; }
        form {
            text-align: center;
            margin: 25px 0;
        }
        select, button {
            padding: 12px 20px;
            font-size: 16px;
            margin: 8px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        select { width: 200px; }
        button {
            background: #667eea;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        button:hover { background: #5a67d8; transform: scale(1.05); }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f8f9ff;
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover { background: #e0e7ff; }
        .empty {
            text-align: center;
            color: #e53e3e;
            font-style: italic;
            padding: 20px;
        }
        .info {
            text-align: center;
            font-size: 0.9em;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Pesquisar Cadastros por Sexo</h1>
    <p style="text-align:center; color:#666;">Dados salvos em <code>cadastros.txt</code></p>

    <!-- FORMULÁRIO -->
    <form method="POST">
        <select name="Sexo" required>
            <option value="">-- Escolha o sexo --</option>
            <option value="M" <?php echo $pesquisa==='M'?'selected':''; ?>>Masculino</option>
            <option value="F" <?php echo $pesquisa==='F'?'selected':''; ?>>Feminino</option>
        </select>
        <button type="submit">Pesquisar</button>
    </form>

    <!-- RESULTADOS -->
    <?php if ($pesquisa && ($pesquisa === 'M' || $pesquisa === 'F')): ?>
        <h2>Resultados: <strong><?php echo $pesquisa === 'M' ? 'Masculino' : 'Feminino'; ?></strong></h2>

        <?php if ($encontrados > 0): ?>
            <table>
                <tr>
                    <th>NOME</th>
                    <th>SOBRENOME</th>
                    <th>SEXO</th>
                </tr>
                <?php foreach ($resultados as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['NomeCliente']); ?></td>
                        <td><?php echo htmlspecialchars($p['SobrenomeCliente']); ?></td>
                        <td><?php echo $p['Sexo'] === 'M' ? 'Masculino' : 'Feminino'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="empty">Nenhum cadastro encontrado para o sexo selecionado.</div>
        <?php endif; ?>

    <?php elseif ($pesquisa !== ''): ?>
        <div class="empty">Selecione uma opção válida.</div>
    <?php endif; ?>

    <div class="info">
        <strong>Dica:</strong> Edite <code>cadastros.txt</code> na mesma pasta para adicionar/remover pessoas.<br>
        Formato: <code>id|nome|sobrenome|M ou F</code>
    </div>
</div>

</body>
</html>
