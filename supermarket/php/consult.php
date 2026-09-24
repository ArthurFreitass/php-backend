<?php

include_once(__DIR__ . '/../php/connection.php');

$categories = mysqli_query(
    $connection,
    "SELECT category_id, name
     FROM Category
     ORDER BY name"
);

$suppliers = mysqli_query(
    $connection,
    "SELECT supplier_id, name_supplier
     FROM Supplier
     WHERE status_supplier = 'ACTIVE'
     ORDER BY name_supplier"
);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}


$name = trim($_POST['name'] ?? '');
$categoryId = $_POST['category_id'] ?? '';
$supplierId = $_POST['supplier_id'] ?? '';


if (
    $name == '' &&
    $categoryId == '' &&
    $supplierId == ''
) {

    echo "
    <!DOCTYPE html>

    <html lang='pt-br'>

    <head>

        <meta charset='UTF-8'>

        <meta
            name='viewport'
            content='width=device-width, initial-scale=1.0'
        >

        <title>
            Consulta - Supermarket
        </title>

        <link
            rel='stylesheet'
            href='../css/consult-response.css'
        >

    </head>

    <body class='response-page'>

        <main class='response-container'>

            <div class='response-card error-card'>

                <div class='response-icon'>
                    !
                </div>

                <h2>
                    Informe um filtro
                </h2>

                <p>
                    Digite o nome do produto,
                    selecione uma categoria
                    ou escolha um fornecedor.
                </p>

                <div class='response-actions'>

                    <a
                        href='../html/consult-product.php'
                        class='response-button'
                    >
                        Voltar para consulta
                    </a>

                </div>

            </div>

        </main>

    </body>

    </html>
    ";

    exit;
}


$conditions = [];


if ($name != '') {

    $name = mysqli_real_escape_string(
        $connection,
        $name
    );

    $conditions[] =
        "Product.name_product LIKE '%$name%'";
}


if ($categoryId != '') {

    $categoryId = intval($categoryId);

    $conditions[] =
        "Product.category_id = $categoryId";
}


if ($supplierId != '') {

    $supplierId = intval($supplierId);

    $productSupplierJoin =
        "LEFT JOIN ProductSupplier
            ON Product.product_id = ProductSupplier.product_id";

    $conditions[] =
        "ProductSupplier.supplier_id = $supplierId";

    $conditions[] =
        "Supplier.status_supplier = 'ACTIVE'";

} else {

    $productSupplierJoin =
        "LEFT JOIN ProductSupplier
            ON Product.product_id = ProductSupplier.product_id
            AND ProductSupplier.supplier_id = (
                SELECT MIN(ps2.supplier_id)
                FROM ProductSupplier ps2
                WHERE ps2.product_id = Product.product_id
            )";
}


$conditions[] =
    "Product.status_product = 'ATIVO'";


$where = implode(
    " AND ",
    $conditions
);


$consult = mysqli_query(
    $connection,
    "SELECT DISTINCT

        Product.product_id,
        Product.name_product,
        Product.description_product,
        Product.sku,
        Product.current_price,
        Product.quantity_in_stock,
        Product.minimum_stock,
        Product.status_product,

        Category.name AS category_name,

        Supplier.name_supplier

    FROM Product

    INNER JOIN Category
        ON Product.category_id = Category.category_id

    $productSupplierJoin

    LEFT JOIN Supplier
        ON ProductSupplier.supplier_id = Supplier.supplier_id

    WHERE $where

    ORDER BY Product.name_product"
);


if (!$consult) {

    die(
        "Erro na consulta: "
        . mysqli_error($connection)
    );
}

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
        Resultado da Consulta - Supermarket
    </title>

    <link
        rel="stylesheet"
        href="../css/consult-response.css"
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

            <a href="../html/consult-product.php">
                Nova Consulta
            </a>

        </nav>

    </header>


    <main class="main">


        <?php if (mysqli_num_rows($consult) == 0): ?>


            <div class="response-container">

                <div class="response-card error-card">

                    <div class="response-icon">
                        !
                    </div>

                    <h2>
                        Nenhum produto encontrado
                    </h2>

                    <p>
                        Nenhum produto corresponde
                        aos filtros informados.
                    </p>

                    <div class="response-actions">

                        <a
                            href="../html/consult-product.php"
                            class="response-button"
                        >
                            Fazer nova consulta
                        </a>

                        <a
                            href="../index.html"
                            class="response-button secondary-button"
                        >
                            Voltar ao início
                        </a>

                    </div>

                </div>

            </div>


        <?php else: ?>


            <section class="result-header">

                <span class="page-tag">
                    Consulta
                </span>

                <h2>
                    Produtos encontrados
                </h2>

                <p>

                    <?= mysqli_num_rows($consult) ?>

                    resultado(s) encontrado(s).

                </p>

            </section>


            <section class="products-grid">


                <?php while ($product = mysqli_fetch_assoc($consult)): ?>


                    <article class="product-card">


                        <div class="product-top">

                            <span class="category-badge">

                                <?= htmlspecialchars(
                                    $product['category_name']
                                ) ?>

                            </span>


                            <span class="status-badge">

                                <?= htmlspecialchars(
                                    $product['status_product']
                                ) ?>

                            </span>

                        </div>


                        <div class="product-title">

                            <h3>

                                <?= htmlspecialchars(
                                    $product['name_product']
                                ) ?>

                            </h3>


                            <span class="sku">

                                SKU:

                                <?= htmlspecialchars(
                                    $product['sku']
                                ) ?>

                            </span>

                        </div>


                        <div class="product-description">

                            <p>

                                <?= htmlspecialchars(
                                    $product['description_product']
                                ) ?>

                            </p>

                        </div>


                        <div class="product-info">


                            <div class="info-box">

                                <span>
                                    Preço
                                </span>

                                <strong>

                                    R$

                                    <?= number_format(
                                        $product['current_price'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </div>


                            <div class="info-box">

                                <span>
                                    Estoque atual
                                </span>

                                <strong>

                                    <?= $product['quantity_in_stock'] ?>

                                </strong>

                            </div>


                            <div class="info-box">

                                <span>
                                    Estoque mínimo
                                </span>

                                <strong>

                                    <?= $product['minimum_stock'] ?>

                                </strong>

                            </div>

                        </div>


                        <div class="supplier-info">

                            <span>
                                Fornecedor
                            </span>

                            <strong>

                                <?php if ($product['name_supplier'] != null): ?>

                                    <?= htmlspecialchars(
                                        $product['name_supplier']
                                    ) ?>

                                <?php else: ?>

                                    Não informado

                                <?php endif; ?>

                            </strong>

                        </div>


                    </article>


                <?php endwhile; ?>


            </section>


            <div class="back-area">

                <a
                    href="../html/consult-product.php"
                    class="response-button"
                >
                    Nova consulta
                </a>

                <a
                    href="../index.html"
                    class="response-button secondary-button"
                >
                    Voltar ao início
                </a>

            </div>


        <?php endif; ?>


    </main>

</body>

</html>