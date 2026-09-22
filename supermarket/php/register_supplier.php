<?php

include_once(__DIR__ . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $tax_id = $_POST['tax_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $insertBD = mysqli_query(
        $connection,
        "INSERT INTO supplier (
            name_supplier,
            tax_id,
            email,
            phone,
            address_supplier,
            status_supplier
        ) 
        VALUES (
            '$name',
            '$tax_id',
            '$email',
            '$phone',
            '$address',
            '$status'
        )"
    );

    if ($insertBD) {

        echo "
        <!DOCTYPE html>
        <html lang='pt-br'>

        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>

            <title>Fornecedor cadastrado - Supermarket</title>

            <link rel='stylesheet' href='../css/supplier-response.css'>
        </head>

        <body class='response-page'>

            <main class='response-container'>

                <div class='response-card success-card'>

                    <span class='response-number'>✓</span>

                    <h1>Fornecedor cadastrado!</h1>

                    <p>
                        O fornecedor <strong>$name</strong> foi adicionado
                        com sucesso ao sistema.
                    </p>

                    <div class='response-actions'>

                        <a 
                            href='../html/register-supplier.html'
                            class='response-button'
                        >
                            Cadastrar outro fornecedor
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

            <title>Erro ao cadastrar - Supermarket</title>

            <link rel='stylesheet' href='../css/supplier-response.css'>
        </head>

        <body class='response-page'>

            <main class='response-container'>

                <div class='response-card error-card'>

                    <span class='response-number'>!</span>

                    <h1>Erro ao cadastrar</h1>

                    <p>
                        Não foi possível cadastrar o fornecedor.
                    </p>

                    <p class='error-message'>
                        $error
                    </p>

                    <div class='response-actions'>

                        <a 
                            href='../html/register-supplier.html'
                            class='response-button'
                        >
                            Tentar novamente
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