<?php
require_once 'conexao.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao == 'salvar_contagem') {
    $codigo = $_POST['codigo'] ?? null;
    $contagem = str_replace(',', '.', $_POST['contagem'] ?? 0);
    
    if ($codigo) {
        $stmt = $pdo->prepare("UPDATE Inventario SET Contagem = :contagem, Saldo = :contagem * Ultimo_Preco WHERE Codigo = :codigo");
        $stmt->execute(['contagem' => $contagem, 'codigo' => $codigo]);
    }
    header('Location: ../index.php?status=sucesso');
    exit;
}

if ($acao == 'novo_produto') {
    $descricao = mb_strtoupper(trim($_POST['descricao'] ?? ''));
    $subgrupo = trim($_POST['subgrupo'] ?? 'Outros');
    $und = mb_strtoupper(trim($_POST['und'] ?? 'UND'));
    $contagem = str_replace(',', '.', $_POST['contagem'] ?? 0);
    $ultimo_preco = str_replace(',', '.', $_POST['ultimo_preco'] ?? 0);
    $saldo = $contagem * $ultimo_preco;

    if ($descricao) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM Inventario WHERE Descricao = :desc");
        $check->execute(['desc' => $descricao]);
        if ($check->fetchColumn() > 0) {
            header('Location: ../index.php?status=erro_duplicado');
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO Inventario (Descricao, Subgrupo, Und, Contagem, Ultimo_Preco, Saldo) VALUES (:desc, :sub, :und, :cont, :preco, :saldo)");
        $stmt->execute([
            'desc' => $descricao,
            'sub' => $subgrupo,
            'und' => $und,
            'cont' => $contagem,
            'preco' => $ultimo_preco,
            'saldo' => $saldo
        ]);
    }
    header('Location: ../index.php?status=criado');
    exit;
}

if ($acao == 'exportar_csv') {
    // Limpa qualquer buffer anterior para evitar saída corrompida
    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Description: File Transfer');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Inventario_Atualizado.csv"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    $output = fopen('php://output', 'w');
    
    // Adiciona o BOM do UTF-R para o Excel reconhecer acentos corretamente
    fwrite($output, "\xEF\xBB\xBF");
    
    // Cabeçalho das colunas separado por ponto e vírgula (padrão Excel brasileiro)
    fputcsv($output, ['Codigo', 'Descricao', 'Subgrupo', 'Und', 'Contagem', 'Ultimo Preco', 'Saldo'], ';');
    
    $stmt = $pdo->query("SELECT Codigo, Descricao, Subgrupo, Und, Contagem, Ultimo_Preco, Saldo FROM Inventario");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Formata os números com vírgula para bater com o padrão da sua planilha
        $row['Contagem'] = number_format((float)$row['Contagem'], 3, ',', '');
        $row['Ultimo_Preco'] = number_format((float)$row['Ultimo_Preco'], 2, ',', '');
        $row['Saldo'] = number_format((float)$row['Saldo'], 2, ',', '');
        
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}
?>