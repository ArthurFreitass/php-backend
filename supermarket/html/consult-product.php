<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consultar Produto - Supermarket</title>

    <link rel="stylesheet" href="../css/consult-product.css">
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
            <a href="./register-product.php">Produtos</a>
            <a href="./update-price.html">Preços</a>
        </nav>

    </header>


    <main class="main">

        <section class="page-header">

            <div>

                <span class="page-tag">
                    Produtos
                </span>

                <h2>Consultar Produtos</h2>

                <p>
                    Utilize os filtros abaixo para localizar produtos
                    cadastrados no estoque do Supermarket.
                </p>

            </div>

            <a href="../index.html" class="back-button">
                ← Voltar
            </a>

        </section>


        <section class="search-section">

            <div class="search-info">

                <span>03</span>

                <h3>Consulta de Produtos</h3>

                <p>
                    Pesquise produtos utilizando diferentes informações
                    cadastradas no sistema.
                </p>

                <div class="info-item">

                    <strong>Nome</strong>

                    <p>
                        Pesquise pelo nome completo ou parte do nome.
                    </p>

                </div>

                <div class="info-item">

                    <strong>SKU</strong>

                    <p>
                        Localize rapidamente um produto pelo seu código único.
                    </p>

                </div>

                <div class="info-item">

                    <strong>Filtros</strong>

                    <p>
                        Utilize categoria, fornecedor ou status para refinar a busca.
                    </p>

                </div>

            </div>


            <form
                class="search-form"
                action="../php/consult.php"
                method="get">

                <div class="form-title">

                    <h3>Pesquisar Produto</h3>

                    <p>
                        Preencha um ou mais campos para realizar a consulta.
                    </p>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="name">
                            Nome do produto
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Ex: Coca-Cola">

                    </div>


                    <div class="form-group">

                        <label for="sku">
                            SKU
                        </label>

                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            placeholder="Ex: BEB-COCA-2L">

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="category_id">
                            Categoria
                        </label>

                        <select
                            id="category_id"
                            name="category_id">

                            <option value="">
                                Todas as categorias
                            </option>

                            <?php
                            include_once(__DIR__ . '/../php/consult.php');
                            while ($category = mysqli_fetch_assoc($categories)): ?>

                                <option value="<?= $category['category_id'] ?>">

                                    <?= htmlspecialchars($category['name']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="supplier_id">
                            Fornecedor
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id">

                            <option value="">
                                Todos os fornecedores
                            </option>

                            <?php
                                include_once(__DIR__ . '/../php/consult.php');
                            while ($supplier = mysqli_fetch_assoc($suppliers)): ?>

                                <option value="<?= $supplier['supplier_id'] ?>">

                                    <?= htmlspecialchars($supplier['name_supplier']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status">

                            <option value="">
                                Todos
                            </option>

                            <option value="ATIVO">
                                Ativo
                            </option>

                            <option value="INATIVO">
                                Inativo
                            </option>

                        </select>

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="reset"
                        class="btn-secondary">
                        Limpar filtros
                    </button>

                    <button
                        type="submit"
                        class="btn-primary">
                        Consultar Produto
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