<?php

include_once(__DIR__ . '/../php/connection.php');

$products = mysqli_query(
    $connection,
    "SELECT
        product_id,
        name_product,
        current_price
     FROM Product
     WHERE status_product = 'ATIVO'
     ORDER BY name_product"
);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Atualizar Preço - Supermarket
    </title>

    <link
        rel="stylesheet"
        href="../css/update-price.css"
    >

</head>

<body>

    <header class="header">

        <div class="logo">

            <h1>
                Supermarket
            </h1>

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

            <a href="./consult-product.php">
                Consultar
            </a>

        </nav>

    </header>


    <main class="main">

        <section class="page-header">

            <div>

                <span class="page-tag">
                    Preços
                </span>

                <h2>
                    Atualizar Preço
                </h2>

                <p>
                    Selecione ou pesquise um produto cadastrado
                    e informe o seu novo preço.
                </p>

            </div>

            <a
                href="../index.html"
                class="back-button"
            >
                ← Voltar
            </a>

        </section>


        <section class="update-section">

            <div class="update-info">

                <span>
                    04
                </span>

                <h3>
                    Atualização de Preço
                </h3>

                <p>
                    Altere o preço de venda dos produtos
                    cadastrados no Supermarket.
                </p>


                <div class="info-item">

                    <strong>
                        Produto
                    </strong>

                    <p>
                        Escolha um produto cadastrado
                        utilizando a lista.
                    </p>

                </div>


                <div class="info-item">

                    <strong>
                        Pesquisa
                    </strong>

                    <p>
                        Você também pode informar o nome
                        do produto manualmente.
                    </p>

                </div>


                <div class="info-item">

                    <strong>
                        Novo preço
                    </strong>

                    <p>
                        Informe o novo valor de venda
                        que será aplicado ao produto.
                    </p>

                </div>

            </div>


            <form
                class="update-form"
                action="../php/update.php"
                method="post"
            >

                <div class="form-title">

                    <h3>
                        Alterar preço do produto
                    </h3>

                    <p>
                        Selecione um produto ou informe
                        seu nome para continuar.
                    </p>

                </div>


                <div class="form-group">

                    <label for="product_id">
                        Produto cadastrado
                    </label>

                    <select
                        id="product_id"
                        name="product_id"
                    >

                        <option value="">
                            Selecione um produto
                        </option>

                        <?php while ($product = mysqli_fetch_assoc($products)): ?>

                            <option
                                value="<?= $product['product_id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    $product['name_product']
                                ) ?>

                                -
                                R$

                                <?= number_format(
                                    $product['current_price'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                    <small>
                        O preço atual aparece ao lado do nome do produto.
                    </small>

                </div>


                <div class="separator">

                    <span>
                        OU
                    </span>

                </div>


                <div class="form-group">

                    <label for="product_name">
                        Nome do produto
                    </label>

                    <input
                        type="text"
                        id="product_name"
                        name="product_name"
                        placeholder="Ex: Coca-Cola 2L"
                    >

                    <small>
                        Utilize este campo caso prefira pesquisar pelo nome.
                    </small>

                </div>


                <div class="price-area">

                    <div class="form-group">

                        <label for="new_price">
                            Novo preço
                        </label>

                        <div class="price-input">

                            <span>
                                R$
                            </span>

                            <input
                                type="number"
                                id="new_price"
                                name="new_price"
                                step="0.01"
                                min="0.01"
                                placeholder="0,00"
                                required
                            >

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="reset"
                        class="btn-secondary"
                    >
                        Limpar
                    </button>


                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Atualizar Preço
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