<?php
require_once 'PHP/conexao.php';
$produtos = $pdo->query("SELECT * FROM Inventario ORDER BY Descricao ASC")->fetchAll(PDO::FETCH_ASSOC);
$subgrupos = $pdo->query("SELECT DISTINCT Subgrupo FROM Inventario ORDER BY Subgrupo ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventário</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS para busca avançada -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Contagem de Inventário</h4>
                        <a href="PHP/acoes.php?acao=exportar_csv" class="btn btn-light btn-sm fw-bold">📥 Baixar Planilha</a>
                    </div>
                    <div class="card-body">
                        
                        <?php if(isset($_GET['status'])): ?>
                            <?php if($_GET['status'] == 'sucesso'): ?>
                                <div class="alert alert-success">Contagem atualizada com sucesso!</div>
                            <?php elseif($_GET['status'] == 'criado'): ?>
                                <div class="alert alert-success">Novo produto cadastrado e contagem salva!</div>
                            <?php elseif($_GET['status'] == 'erro_duplicado'): ?>
                                <div class="alert alert-danger">Erro: Já existe um produto cadastrado com este nome!</div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Abas de navegação -->
                        <ul class="nav nav-tabs mb-4" id="inventarioTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="contar-tab" data-bs-toggle="tab" data-bs-target="#contar" type="button" role="tab">Contar / Atualizar</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="novo-tab" data-bs-toggle="tab" data-bs-target="#novo" type="button" role="tab">+ Novo Produto</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="inventarioTabContent">
                            <!-- Aba 1: Contar Item Existente -->
                            <div class="tab-pane fade show active" id="contar" role="tabpanel">
                                <form action="PHP/acoes.php" method="POST">
                                    <input type="hidden" name="acao" value="salvar_contagem">
                                    
                                    <div class="mb-3">
                                        <label for="codigo" class="form-label fw-bold">Selecione ou Busque o Produto:</label>
                                        <select name="codigo" id="codigo" class="form-select" required>
                                            <option value="">Digite o nome ou código...</option>
                                            <?php foreach($produtos as $p): ?>
                                                <option value="<?= $p['Codigo'] ?>" data-und="<?= $p['Und'] ?>" data-contagem="<?= $p['Contagem'] ?>">
                                                    [<?= $p['Codigo'] ?>] <?= $p['Descricao'] ?> (Atual: <?= number_format($p['Contagem'], 3, ',', '.') ?> <?= $p['Und'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contagem" class="form-label fw-bold">Quantidade Contada (até 3 casas decimais):</label>
                                        <input type="text" class="form-control form-control-lg" name="contagem" id="contagem" placeholder="Ex: 12,500 ou 12.5" required>
                                        <div class="form-text">Use vírgula ou ponto para separar os decimais.</div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Salvar Contagem</button>
                                </form>
                            </div>

                            <!-- Aba 2: Criar Novo Produto -->
                            <div class="tab-pane fade" id="novo" role="tabpanel">
                                <form action="PHP/acoes.php" method="POST">
                                    <input type="hidden" name="acao" value="novo_produto">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome / Descrição do Produto:</label>
                                        <input type="text" class="form-control" name="descricao" required style="text-transform: uppercase;">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Subgrupo:</label>
                                            <select name="subgrupo" class="form-select" required>
                                                <?php foreach($subgrupos as $sg): ?>
                                                    <option value="<?= $sg ?>"><?= $sg ?></option>
                                                <?php endforeach; ?>
                                                <option value="Outros">Outros</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Unidade (Und):</label>
                                            <input type="text" class="form-control" name="und" value="UND" required style="text-transform: uppercase;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Quantidade Inicial (Contagem):</label>
                                            <input type="text" class="form-control" name="contagem" value="0,000" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Último Preço (R$):</label>
                                            <input type="text" class="form-control" name="ultimo_preco" value="0,00" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Cadastrar e Salvar</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#codigo').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
</body>
</html>
