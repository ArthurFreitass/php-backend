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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $categoryId = $_POST['category_id'];
    $sku = $_POST['sku'];
    $description = $_POST['description'];
    $current_price = $_POST['current_price'];
    $quantity = $_POST['quantity_in_stock'];
    $minimum_stock = $_POST['minimum_stock'];
    $status = $_POST['status'];

    $supplierId = $_POST['supplier_id'];
    $supplierPrice = $_POST['supplier_price'];
    $supplierProductCode = $_POST['supplier_product_code'];
    $leadTimeDays = $_POST['lead_time_days'];

    $insertProductInBD = mysqli_query(
        $connection,
        "INSERT INTO Product (
            category_id,
            name_product,
            description_product,
            sku,
            current_price,
            quantity_in_stock,
            minimum_stock,
            status_product
        )
        VALUES (
            '$categoryId',
            '$name',
            '$description',
            '$sku',
            '$current_price',
            '$quantity',
            '$minimum_stock',
            '$status'
        )"
    );

    if ($insertProductInBD) {

        $productId = mysqli_insert_id($connection);

        $insertProductSupplier = mysqli_query(
            $connection,
            "INSERT INTO ProductSupplier (
                product_id,
                supplier_id,
                supplier_price,
                supplier_product_code,
                lead_time_days
            )
            VALUES (
                '$productId',
                '$supplierId',
                '$supplierPrice',
                '$supplierProductCode',
                '$leadTimeDays'
            )"
        );

        if ($insertProductSupplier) {

            echo "
            <!DOCTYPE html>
            <html lang='pt-br'>

            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>

                <title>Produto cadastrado - Supermarket</title>

                <link rel='stylesheet' href='../css/product-response.css'>
            </head>

            <body class='response-page'>

                <main class='response-container'>

                    <div class='response-card success-card'>

                        <div class='response-icon'>
                            ✓
                        </div>

                        <h2>
                            Produto cadastrado com sucesso!
                        </h2>

                        <p>
                            O produto <strong>$name</strong> foi cadastrado
                            e associado ao fornecedor com sucesso.
                        </p>

                        <div class='response-actions'>

                            <a
                                href='../html/register-product.php'
                                class='response-button'
                            >
                                Cadastrar outro produto
                            </a>

                            <a
                                href='../index.html'
                                class='response-button secondary-button'
                            >
                                Voltar ao início
                            </a>

                        </div>

                    </div>

                </main>

            </body>

            </html>
            ";

        } else {

            $error = mysqli_error($connection);

            echo "
            <!DOCTYPE html>
            <html lang='pt-br'>

            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>

                <title>Erro ao associar fornecedor - Supermarket</title>

                <link rel='stylesheet' href='../css/product-response.css'>
            </head>

            <body class='response-page'>

                <main class='response-container'>

                    <div class='response-card error-card'>

                        <div class='response-icon'>
                            !
                        </div>

                        <h2>
                            Erro ao associar fornecedor
                        </h2>

                        <p>
                            O produto foi cadastrado, mas não foi possível
                            associá-lo ao fornecedor selecionado.
                        </p>

                        <div class='error-message'>
                            $error
                        </div>

                        <div class='response-actions'>

                            <a
                                href='../html/register-product.php'
                                class='response-button'
                            >
                                Voltar ao cadastro
                            </a>

                            <a
                                href='../index.html'
                                class='response-button secondary-button'
                            >
                                Voltar ao início
                            </a>

                        </div>

                    </div>

                </main>

            </body>

            </html>
            ";
        }

    } else {

        $error = mysqli_error($connection);

        echo "
        <!DOCTYPE html>
        <html lang='pt-br'>

        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>

            <title>Erro ao cadastrar produto - Supermarket</title>

            <link rel='stylesheet' href='../css/product-response.css'>
        </head>

        <body class='response-page'>

            <main class='response-container'>

                <div class='response-card error-card'>

                    <div class='response-icon'>
                        !
                    </div>

                    <h2>
                        Erro ao cadastrar produto
                    </h2>

                    <p>
                        Não foi possível adicionar o produto ao banco de dados.
                    </p>

                    <div class='error-message'>
                        $error
                    </div>

                    <div class='response-actions'>

                        <a
                            href='../html/register-product.php'
                            class='response-button'
                        >
                            Tentar novamente
                        </a>

                        <a
                            href='../index.html'
                            class='response-button secondary-button'
                        >
                            Voltar ao início
                        </a>

                    </div>

                </div>

            </main>

        </body>

        </html>
        ";
    }
}

?>