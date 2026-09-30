<?php

include_once(__DIR__ . '/connection.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/register-client.html");
    exit;
}

$name = $_POST['name_client'];
$cpf = $_POST['cpf'];
$phone = $_POST['phone_number'];
$email = $_POST['email'];
$birthdate = $_POST['birthdate'];


$sql = "INSERT INTO client 
        (name_client, cpf, phone_number, email, birthdate)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($connect, $sql);

$success = false;
$errorMessage = "";

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $name,
        $cpf,
        $phone,
        $email,
        $birthdate
    );

    $success = mysqli_stmt_execute($stmt);

    if (!$success) {

        if (mysqli_errno($connect) === 1062) {
            $errorMessage = "Já existe um cliente cadastrado com este CPF.";
        } else {
            $errorMessage = "Não foi possível realizar o cadastro. Tente novamente.";
        }
    }

    mysqli_stmt_close($stmt);

} else {
    $errorMessage = "Ocorreu um erro ao preparar o cadastro.";
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
        Resultado do cadastro | Brasil Viagens
    </title>

    <link
        rel="stylesheet"
        href="../css/response-register.css"
    >
</head>

<body>

    <header class="header">

        <a href="../index.html" class="logo">
            Brasil Viagens
        </a>

        <nav class="nav">

            <a href="../index.html">
                Início
            </a>

        </nav>

    </header>


    <main class="main">

        <?php if ($success): ?>

            <section class="response-card success">

                <div class="status-symbol">
                    ✓
                </div>

                <span class="status-text">
                    Cadastro realizado
                </span>

                <h1>
                    Cliente cadastrado com sucesso!
                </h1>

                <p>
                    Os dados do cliente foram adicionados ao sistema
                    da Brasil Viagens com sucesso.
                </p>

                <div class="actions">

                    <a
                        href="../html/register-client.html"
                        class="button secondary-button"
                    >
                        Cadastrar outro cliente
                    </a>

                    <a
                        href="../index.html"
                        class="button primary-button"
                    >
                        Voltar ao início
                    </a>

                </div>

            </section>

        <?php else: ?>

            <section class="response-card error">

                <div class="status-symbol">
                    !
                </div>

                <span class="status-text">
                    Erro no cadastro
                </span>

                <h1>
                    Não foi possível cadastrar o cliente
                </h1>

                <p>
                    <?php echo htmlspecialchars($errorMessage); ?>
                </p>

                <p class="help-text">
                    Verifique os dados informados e tente novamente.
                </p>

                <div class="actions">

                    <a
                        href="../html/register-client.html"
                        class="button primary-button"
                    >
                        Voltar ao cadastro
                    </a>

                    <a
                        href="../index.html"
                        class="button secondary-button"
                    >
                        Ir para o início
                    </a>

                </div>

            </section>

        <?php endif; ?>

    </main>


    <footer class="footer">

        <p>
            &copy; 2026 Brasil Viagens. Todos os direitos reservados.
        </p>

    </footer>

</body>

</html>