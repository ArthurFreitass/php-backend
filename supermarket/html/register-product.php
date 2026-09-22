<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Produto - Supermarket</title>

    <link rel="stylesheet" href="../css/register-product.css">
    <link rel="stylesheet" href="../css/product-response.css">
</head>

<body>

    <header class="header">

        <div class="logo">
            <h1>Supermarket</h1>
            <span>Área Administrativa</span>
        </div>

        <nav class="navbar">
            <a href="../index.html">Início</a>
            <a href="./register-supplier.html">Fornecedores</a>
            <a href="./consult-product.html">Consultar</a>
            <a href="./update-price.html">Preços</a>
        </nav>

    </header>

    <main class="main">

        <section class="page-header">

            <div>

                <span class="page-tag">
                    Produtos
                </span>

                <h2>Cadastrar Produto</h2>

                <p>
                    Adicione um novo produto ao estoque e associe suas
                    informações de categoria e fornecedor.
                </p>

            </div>

            <a href="../index.html" class="back-button">
                ← Voltar
            </a>

        </section>


        <section class="form-section">

            <div class="form-info">

                <span>02</span>

                <h3>Dados do Produto</h3>

                <p>
                    Preencha corretamente as informações para manter
                    o estoque do Supermarket organizado.
                </p>

                <div class="info-item">

                    <strong>Categoria</strong>

                    <p>
                        Organize o produto de acordo com seu tipo.
                    </p>

                </div>

                <div class="info-item">

                    <strong>Estoque</strong>

                    <p>
                        Controle a quantidade disponível e o estoque mínimo.
                    </p>

                </div>

                <div class="info-item">

                    <strong>Fornecedor</strong>

                    <p>
                        Associe o produto a um fornecedor cadastrado.
                    </p>

                </div>

            </div>


            <form class="product-form" action="../php/insert-product.php" method="post">

                <div class="form-title">

                    <h3>Novo Produto</h3>

                    <p>
                        Informe os dados necessários para cadastrar
                        um produto no estoque.
                    </p>

                </div>


                <div class="form-group full-width">

                    <label for="name">
                        Nome do produto
                    </label>

                    <input type="text" id="name" name="name" placeholder="Ex: Coca-Cola 2L" maxlength="150" required>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="sku">
                            SKU
                        </label>

                        <input type="text" id="sku" name="sku" placeholder="Ex: BEB-COCA-2L" maxlength="50" required>

                    </div>


                    <div class="form-group">

                        <label for="category_id">
                            Categoria
                        </label>

                        <select id="category_id" name="category_id" required>

                            <option value="">
                                Selecione uma categoria
                            </option>

                            <?php
                            include_once(__DIR__ . '/../php/insert-product.php');

                            while ($category = mysqli_fetch_assoc($categories)): ?>

                                <option value="<?= $category['category_id'] ?>">

                                    <?= htmlspecialchars($category['name']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                </div>


                <div class="form-group full-width">

                    <label for="description">
                        Descrição
                    </label>

                    <textarea id="description" name="description" maxlength="255"
                        placeholder="Digite uma breve descrição do produto"></textarea>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="current_price">
                            Preço de venda
                        </label>

                        <input type="number" id="current_price" name="current_price" min="0.01" step="0.01"
                            placeholder="0,00" required>

                    </div>


                    <div class="form-group">

                        <label for="quantity_in_stock">
                            Quantidade em estoque
                        </label>

                        <input type="number" id="quantity_in_stock" name="quantity_in_stock" min="0" value="0" required>

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="minimum_stock">
                            Estoque mínimo
                        </label>

                        <input type="number" id="minimum_stock" name="minimum_stock" min="0" value="0" required>

                    </div>


                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <select id="status" name="status" required>

                            <option value="ATIVO">
                                Ativo
                            </option>

                            <option value="INATIVO">
                                Inativo
                            </option>

                        </select>

                    </div>

                </div>


                <div class="form-divider">

                    <span>
                        Fornecedor
                    </span>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="supplier_id">
                            Fornecedor
                        </label>

                        <select id="supplier_id" name="supplier_id" required>

                            <option value="">
                                Selecione um fornecedor
                            </option>

                            <?php
                            include_once(__DIR__ . '/../php/insert-product.php'); 
                            while ($supplier = mysqli_fetch_assoc($suppliers)): ?>

                                <option value="<?= $supplier['supplier_id'] ?>">

                                    <?= htmlspecialchars($supplier['name_supplier']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="supplier_price">
                            Preço do fornecedor
                        </label>

                        <input type="number" id="supplier_price" name="supplier_price" min="0.01" step="0.01"
                            placeholder="0,00" required>

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="supplier_product_code">
                            Código do produto no fornecedor
                        </label>

                        <input type="text" id="supplier_product_code" name="supplier_product_code" maxlength="50"
                            placeholder="Ex: CC-2L-001">

                    </div>


                    <div class="form-group">

                        <label for="lead_time_days">
                            Prazo de entrega
                        </label>

                        <div class="input-unit">

                            <input type="number" id="lead_time_days" name="lead_time_days" min="0" placeholder="Ex: 3">

                            <span>
                                dias
                            </span>

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <button type="reset" class="btn-secondary">
                        Limpar
                    </button>

                    <button type="submit" class="btn-primary">
                        Cadastrar Produto
                    </button>

                </div>

            </form>

        </section>

    </main>


    <footer class="footer">

        <h3>Supermarket</h3>

        <p>
            Sistema interno de gerenciamento.
        </p>

        <span>
            © 2026 Supermarket. Todos os direitos reservados.
        </span>

    </footer>

</body>

</html>