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

$name = $_POST['name'] ?? '';
$sku = $_POST['sku'] ?? '';
$categoryId = $_POST['category_id'] ?? '';
$supplierId = $_POST['supplier_id'] ?? '';
$status = $_POST['status'] ?? '';

$sql = "
    SELECT DISTINCT
        Product.product_id AS product_id,
        Product.name_product AS product,
        Product.description_product AS description,
        Product.sku AS sku,
        Product.current_price AS price,
        Product.quantity_in_stock AS stock,
        Product.minimum_stock AS minimum_stock,
        Product.status_product AS status,
        Category.name AS category

    FROM Product

    INNER JOIN Category
        ON Product.category_id = Category.category_id

    LEFT JOIN ProductSupplier
        ON Product.product_id = ProductSupplier.product_id

    LEFT JOIN Supplier
        ON ProductSupplier.supplier_id = Supplier.supplier_id

    WHERE 1 = 1
";

if ($name != '') {

    $name = mysqli_real_escape_string($connection, $name);

    $sql .= "
        AND Product.name_product LIKE '%$name%'
    ";
}

if ($sku != '') {

    $sku = mysqli_real_escape_string($connection, $sku);

    $sql .= "
        AND Product.sku LIKE '%$sku%'
    ";
}

if ($categoryId != '') {

    $categoryId = intval($categoryId);

    $sql .= "
        AND Category.category_id = $categoryId
    ";
}

if ($supplierId != '') {

    $supplierId = intval($supplierId);

    $sql .= "
        AND Supplier.supplier_id = $supplierId
        AND Supplier.status_supplier = 'ACTIVE'
    ";
}

if ($status != '') {

    $status = mysqli_real_escape_string($connection, $status);

    $sql .= "
        AND Product.status_product = '$status'
    ";
}

$sql .= "
    ORDER BY Product.name_product
";

$consult = mysqli_query($connection, $sql);

if (!$consult) {
    die("Erro na consulta: " . mysqli_error($connection));
}

$totalProducts = mysqli_num_rows($consult);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Resultado da Consulta - Supermarket</title>

    <link
        rel="stylesheet"
        href="../css/consult-response.css"
    >
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

            <a href="../html/consult-product.php">
                Nova Consulta
            </a>

        </nav>

    </header>


    <main class="main">

        <section class="page-header">

            <span class="page-tag">
                Consulta
            </span>

            <h2>
                Resultado da Consulta
            </h2>

            <p>
                Confira os produtos encontrados de acordo com os filtros
                utilizados.
            </p>

        </section>


        <?php if ($totalProducts > 0): ?>

            <div class="search-message success-message">

                <div class="message-icon">
                    ✓
                </div>

                <div>

                    <strong>
                        Consulta realizada com sucesso!
                    </strong>

                    <p>
                        Foram encontrados
                        <?= $totalProducts ?>
                        produto(s).
                    </p>

                </div>

            </div>


            <section class="products-grid">

                <?php while ($result = mysqli_fetch_assoc($consult)): ?>

                    <article class="product-card">

                        <div class="product-top">

                            <span class="category-badge">

                                <?= htmlspecialchars($result['category']) ?>

                            </span>

                            <span class="status-badge">

                                <?= htmlspecialchars($result['status']) ?>

                            </span>

                        </div>


                        <div class="product-title">

                            <h3>

                                <?= htmlspecialchars($result['product']) ?>

                            </h3>

                            <span class="sku">

                                SKU:
                                <?= htmlspecialchars($result['sku']) ?>

                            </span>

                        </div>


                        <div class="description">

                            <p>

                                <?= htmlspecialchars($result['description']) ?>

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
                                        $result['price'],
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

                                    <?= $result['stock'] ?>

                                </strong>

                            </div>


                            <div class="info-box">

                                <span>
                                    Estoque mínimo
                                </span>

                                <strong>

                                    <?= $result['minimum_stock'] ?>

                                </strong>

                            </div>

                        </div>

                    </article>

                <?php endwhile; ?>

            </section>


            <div class="back-area">

                <a
                    href="../html/consult-product.php"
                    class="button-primary"
                >
                    Fazer nova consulta
                </a>

                <a
                    href="../index.html"
                    class="button-secondary"
                >
                    Voltar ao início
                </a>

            </div>


        <?php else: ?>

            <div class="search-message error-message">

                <div class="message-icon">
                    !
                </div>

                <div>

                    <strong>
                        Nenhum produto encontrado
                    </strong>

                    <p>
                        Não existem produtos correspondentes aos filtros
                        informados.
                    </p>

                </div>

            </div>


            <div class="back-area">

                <a
                    href="../html/consult-product.php"
                    class="button-primary"
                >
                    Tentar novamente
                </a>

                <a
                    href="../index.html"
                    class="button-secondary"
                >
                    Voltar ao início
                </a>

            </div>

        <?php endif; ?>

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