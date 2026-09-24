<?php

include_once(__DIR__ . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        "Location: ../html/update-price.php"
    );

    exit;
}


$productId = $_POST['product_id'] ?? '';

$productName = trim(
    $_POST['product_name'] ?? ''
);

$newPrice = trim(
    $_POST['new_price'] ?? ''
);


$title = '';
$message = '';
$responseType = 'error';

$product = null;

$oldPrice = null;
$updatedPrice = null;


if (
    $productId == '' &&
    $productName == ''
) {

    $title = "Produto não informado";

    $message =
        "Selecione um produto ou informe o nome do produto.";

} elseif (
    $newPrice == '' ||
    !is_numeric($newPrice) ||
    $newPrice <= 0
) {

    $title = "Preço inválido";

    $message =
        "Informe um novo preço válido e maior que zero.";

} else {


    if ($productId != '') {

        $productId = intval(
            $productId
        );

        $consultProduct = mysqli_query(
            $connection,
            "SELECT
                product_id,
                name_product,
                current_price
             FROM Product
             WHERE product_id = $productId"
        );


        if (
            mysqli_num_rows($consultProduct) > 0
        ) {

            $product = mysqli_fetch_assoc(
                $consultProduct
            );
        }


    } else {


        $productName = mysqli_real_escape_string(
            $connection,
            $productName
        );


        $consultProduct = mysqli_query(
            $connection,
            "SELECT
                product_id,
                name_product,
                current_price
             FROM Product
             WHERE name_product = '$productName'"
        );


        if (
            mysqli_num_rows($consultProduct) == 1
        ) {

            $product = mysqli_fetch_assoc(
                $consultProduct
            );

        } elseif (
            mysqli_num_rows($consultProduct) > 1
        ) {

            $title =
                "Mais de um produto encontrado";

            $message =
                "Existem produtos com esse mesmo nome. Utilize o select para escolher o produto correto.";
        }

    }


    if (
        $product == null &&
        $title == ''
    ) {

        $title =
            "Produto não encontrado";

        $message =
            "Não foi encontrado nenhum produto com as informações fornecidas.";

    }


    if ($product != null) {


        $productId =
            $product['product_id'];

        $oldPrice =
            $product['current_price'];

        $updatedPrice =
            floatval($newPrice);


        if (
            floatval($oldPrice) ==
            $updatedPrice
        ) {

            $title =
                "Preço não alterado";

            $message =
                "O novo preço informado é igual ao preço atual do produto.";

        } else {


            $insertHistory = mysqli_query(
                $connection,
                "INSERT INTO PriceHistory (
                    product_id,
                    previous_price,
                    new_price
                )
                VALUES (
                    '$productId',
                    '$oldPrice',
                    '$updatedPrice'
                )"
            );


            if ($insertHistory) {


                $updateProduct = mysqli_query(
                    $connection,
                    "UPDATE Product
                     SET current_price = '$updatedPrice'
                     WHERE product_id = '$productId'"
                );


                if ($updateProduct) {

                    $responseType =
                        "success";

                    $title =
                        "Preço atualizado com sucesso!";

                    $message =
                        "O preço do produto foi alterado e o histórico da alteração foi registrado.";

                } else {

                    $title =
                        "Erro ao atualizar produto";

                    $message =
                        "Não foi possível atualizar o preço do produto.";

                }


            } else {

                $title =
                    "Erro ao registrar histórico";

                $message =
                    "Não foi possível registrar a alteração no histórico de preços.";

            }

        }

    }

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
        Atualização de Preço - Supermarket
    </title>

    <link
        rel="stylesheet"
        href="../css/update-response.css"
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

            <a href="../html/register-supplier.html">
                Fornecedores
            </a>

            <a href="../html/register-product.php">
                Produtos
            </a>

            <a href="../html/consult-product.php">
                Consultar
            </a>

        </nav>

    </header>


    <main class="main">

        <section
            class="response-card <?= $responseType ?>"
        >


            <div class="response-icon">

                <?php if ($responseType == 'success'): ?>

                    ✓

                <?php else: ?>

                    !

                <?php endif; ?>

            </div>


            <span class="response-tag">

                <?php if ($responseType == 'success'): ?>

                    Preço atualizado

                <?php else: ?>

                    Não foi possível atualizar

                <?php endif; ?>

            </span>


            <h2>

                <?= htmlspecialchars($title) ?>

            </h2>


            <p class="response-message">

                <?= htmlspecialchars($message) ?>

            </p>


            <?php if (
                $responseType == 'success' &&
                $product != null
            ): ?>


                <div class="product-result">

                    <div class="product-name">

                        <span>
                            Produto
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $product['name_product']
                            ) ?>

                        </strong>

                    </div>


                    <div class="price-comparison">


                        <div class="price-box">

                            <span>
                                Preço anterior
                            </span>

                            <strong>

                                R$

                                <?= number_format(
                                    $oldPrice,
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>


                        <div class="price-arrow">

                            →

                        </div>


                        <div class="price-box new-price">

                            <span>
                                Novo preço
                            </span>

                            <strong>

                                R$

                                <?= number_format(
                                    $updatedPrice,
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </div>


                    </div>

                </div>


            <?php endif; ?>


            <div class="response-actions">


                <a
                    href="../html/update-price.php"
                    class="btn-primary"
                >

                    Atualizar outro preço

                </a>


                <a
                    href="../index.html"
                    class="btn-secondary"
                >

                    Voltar ao início

                </a>


            </div>


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