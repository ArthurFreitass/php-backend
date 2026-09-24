<?php

include_once(__DIR__ . '/../php/consult.php');

?>

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

            <span>
                Área Administrativa
            </span>

        </div>

        <nav class="navbar">

            <a href="../index.html">
                Início
            </a>

            <a href="./register-supplier.html">
                Fornecedores
            </a>

            <a href="./register-product.php">
                Produtos
            </a>

            <a href="./update-price.html">
                Preços
            </a>

        </nav>

    </header>


    <main class="main">

        <section class="page-header">

            <div>

                <span class="page-tag">
                    Produtos
                </span>

                <h2>
                    Consultar Produtos
                </h2>

                <p>
                    Consulte produtos pelo nome, categoria
                    ou fornecedor.
                </p>

            </div>

            <a
                href="../index.html"
                class="back-button"
            >
                ← Voltar
            </a>

        </section>


        <section class="search-section">

            <div class="search-info">

                <span>
                    03
                </span>

                <h3>
                    Consulta de Produtos
                </h3>

                <p>
                    Utilize um ou mais campos para localizar
                    produtos cadastrados no Supermarket.
                </p>


                <div class="info-item">

                    <strong>
                        Nome
                    </strong>

                    <p>
                        Pesquise pelo nome completo ou por parte
                        do nome do produto.
                    </p>

                </div>


                <div class="info-item">

                    <strong>
                        Categoria
                    </strong>

                    <p>
                        Consulte os produtos pertencentes
                        a uma categoria específica.
                    </p>

                </div>


                <div class="info-item">

                    <strong>
                        Fornecedor
                    </strong>

                    <p>
                        Consulte os produtos fornecidos
                        por um fornecedor específico.
                    </p>

                </div>

            </div>


            <form
                class="search-form"
                action="../php/consult.php"
                method="post"
            >

                <div class="form-title">

                    <h3>
                        Pesquisar Produto
                    </h3>

                    <p>
                        Preencha pelo menos um campo para
                        realizar a consulta.
                    </p>

                </div>


                <div class="form-group full-width">

                    <label for="name">
                        Nome do produto
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Ex: Coca-Cola"
                    >

                </div>


                <div style="margin-top: 15px;" class="form-row">

                    <div class="form-group">

                        <label for="category_id">
                            Categoria
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                        >

                            <option value="">
                                Selecione uma categoria
                            </option>

                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>

                                <option
                                    value="<?= $category['category_id'] ?>"
                                >

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
                            name="supplier_id"
                        >

                            <option value="">
                                Selecione um fornecedor
                            </option>

                            <?php while ($supplier = mysqli_fetch_assoc($suppliers)): ?>

                                <option
                                    value="<?= $supplier['supplier_id'] ?>"
                                >

                                    <?= htmlspecialchars(
                                        $supplier['name_supplier']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="reset"
                        class="btn-secondary"
                    >
                        Limpar filtros
                    </button>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Consultar Produto
                    </button>

                </div>

            </form>

        </section>

    </main>


    <footer class="footer">

        <h3>
            Supermarket
        </h3>

        <p>
            Sistema interno de gerenciamento.
        </p>

        <span>
            © 2026 Supermarket. Todos os direitos reservados.
        </span>

    </footer>

</body>

</html>