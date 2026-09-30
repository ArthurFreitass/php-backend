<?php

include_once(__DIR__ . '/connection.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('America/Sao_Paulo');

$success = false;
$errorMessage = "";

$idTravel = null;
$cancellationReason = "";

$travelOrigin = "";
$travelDestination = "";
$travelDate = "";
$companyName = "";
$driverName = "";
$vehicleName = "";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/cancel-travel.php");
    exit;
}

$idTravel = filter_var(
    $_POST['id_travel'] ?? null,
    FILTER_VALIDATE_INT
);

$cancellationReason = trim(
    $_POST['cancellation_reason'] ?? ''
);

if (
    $idTravel === false ||
    $idTravel === null ||
    $idTravel <= 0
) {
    $errorMessage = "A viagem selecionada é inválida.";
}

if (
    $errorMessage === "" &&
    $cancellationReason === ""
) {
    $errorMessage = "Informe o motivo do cancelamento.";
}

if (
    $errorMessage === "" &&
    strlen($cancellationReason) > 255
) {
    $errorMessage = "O motivo do cancelamento deve possuir no máximo 255 caracteres.";
}

if ($errorMessage === "") {

    try {

        $sqlTravel = "
            SELECT
                t.id_travel,
                t.origin,
                t.destination,
                t.departure_datetime,
                t.status_travel,
                c.name_company,
                d.name_driver,
                v.model,
                v.plate
            FROM travel t
            INNER JOIN company c
                ON t.id_company = c.id_company
            INNER JOIN driver d
                ON t.id_driver = d.id_driver
            INNER JOIN vehicle v
                ON t.id_vehicle = v.id_vehicle
            WHERE t.id_travel = ?
        ";

        $stmtTravel = mysqli_prepare(
            $connect,
            $sqlTravel
        );

        mysqli_stmt_bind_param(
            $stmtTravel,
            "i",
            $idTravel
        );

        mysqli_stmt_execute(
            $stmtTravel
        );

        $resultTravel = mysqli_stmt_get_result(
            $stmtTravel
        );

        if (mysqli_num_rows($resultTravel) === 0) {

            $errorMessage = "A viagem selecionada não existe.";

        } else {

            $travel = mysqli_fetch_assoc(
                $resultTravel
            );

            $travelOrigin = $travel['origin'];
            $travelDestination = $travel['destination'];

            $travelDate = date(
                'd/m/Y H:i',
                strtotime(
                    $travel['departure_datetime']
                )
            );

            $companyName = $travel['name_company'];
            $driverName = $travel['name_driver'];

            $vehicleName =
                $travel['model']
                . " - "
                . $travel['plate'];

            if ($travel['status_travel'] === 'CANCELADA') {

                $errorMessage = "Esta viagem já foi cancelada.";

            } elseif ($travel['status_travel'] === 'CONCLUIDA') {

                $errorMessage = "Uma viagem concluída não pode ser cancelada.";

            } elseif ($travel['status_travel'] !== 'AGENDADA') {

                $errorMessage = "Esta viagem não está disponível para cancelamento.";
            }
        }

        mysqli_stmt_close(
            $stmtTravel
        );

    } catch (mysqli_sql_exception $e) {

        $errorMessage = "Não foi possível consultar os dados da viagem.";
    }
}

if ($errorMessage === "") {

    try {

        $sqlCancel = "
            UPDATE travel
            SET
                status_travel = 'CANCELADA',
                cancelled_at = NOW(),
                cancellation_reason = ?
            WHERE id_travel = ?
            AND status_travel = 'AGENDADA'
        ";

        $stmtCancel = mysqli_prepare(
            $connect,
            $sqlCancel
        );

        mysqli_stmt_bind_param(
            $stmtCancel,
            "si",
            $cancellationReason,
            $idTravel
        );

        mysqli_stmt_execute(
            $stmtCancel
        );

        if (
            mysqli_stmt_affected_rows(
                $stmtCancel
            ) === 1
        ) {

            $success = true;

        } else {

            $errorMessage =
                "A viagem não pôde ser cancelada. Verifique se ela ainda está agendada.";
        }

        mysqli_stmt_close(
            $stmtCancel
        );

    } catch (mysqli_sql_exception $e) {

        $errorMessage =
            "Ocorreu um erro ao cancelar a viagem. Nenhuma alteração foi realizada.";
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
        Resultado do Cancelamento | Brasil Viagens
    </title>

    <link
        rel="stylesheet"
        href="../css/response-cancel.css"
    >
</head>

<body>

    <header class="header">

        <a
            href="../index.html"
            class="logo"
        >
            Brasil Viagens
        </a>

        <nav class="nav">

            <a href="../index.html">
                Início
            </a>

            <a href="../html/cancel-travel.php">
                Cancelar Viagem
            </a>

            <a href="../html/scheduled-travels.php">
                Viagens Agendadas
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
                    Cancelamento concluído
                </span>

                <h1>
                    Viagem cancelada com sucesso!
                </h1>

                <p>
                    O status da viagem foi alterado para
                    <strong>CANCELADA</strong>
                    e o motivo foi registrado no sistema.
                </p>

                <div class="travel-summary">

                    <div class="summary-item">

                        <span>
                            Código da viagem
                        </span>

                        <strong>
                            #<?php echo (int)$idTravel; ?>
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span>
                            Trajeto
                        </span>

                        <strong>
                            <?php
                                echo htmlspecialchars(
                                    $travelOrigin
                                    . " → "
                                    . $travelDestination
                                );
                            ?>
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span>
                            Saída
                        </span>

                        <strong>
                            <?php
                                echo htmlspecialchars(
                                    $travelDate
                                );
                            ?>
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span>
                            Empresa
                        </span>

                        <strong>
                            <?php
                                echo htmlspecialchars(
                                    $companyName
                                );
                            ?>
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span>
                            Motorista
                        </span>

                        <strong>
                            <?php
                                echo htmlspecialchars(
                                    $driverName
                                );
                            ?>
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span>
                            Veículo
                        </span>

                        <strong>
                            <?php
                                echo htmlspecialchars(
                                    $vehicleName
                                );
                            ?>
                        </strong>

                    </div>

                </div>

                <div class="reason-box">

                    <span>
                        Motivo do cancelamento
                    </span>

                    <p>
                        <?php
                            echo htmlspecialchars(
                                $cancellationReason
                            );
                        ?>
                    </p>

                </div>

                <div class="actions">

                    <a
                        href="../html/cancel-travel.php"
                        class="button secondary-button"
                    >
                        Cancelar outra viagem
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
                    Erro no cancelamento
                </span>

                <h1>
                    Não foi possível cancelar a viagem
                </h1>

                <p>
                    <?php
                        echo htmlspecialchars(
                            $errorMessage
                        );
                    ?>
                </p>

                <p class="help-text">
                    Verifique as informações da viagem e tente novamente.
                </p>

                <div class="actions">

                    <a
                        href="../html/cancel-travel.php"
                        class="button primary-button"
                    >
                        Voltar ao cancelamento
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
            &copy; 2026 Brasil Viagens.
            Todos os direitos reservados.
        </p>

    </footer>

</body>

</html>
