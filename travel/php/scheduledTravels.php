<?php

include_once(__DIR__ . '/connection.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$errorMessage = "";
$travels = [];

$idCompany = filter_var(
    $_GET['id_company'] ?? 0,
    FILTER_VALIDATE_INT
);

$origin = trim(
    $_GET['origin'] ?? ''
);

$destination = trim(
    $_GET['destination'] ?? ''
);

$departureDate = trim(
    $_GET['departure_date'] ?? ''
);

if (
    $idCompany === false ||
    $idCompany === null ||
    $idCompany < 0
) {
    $errorMessage =
        "A empresa informada para consulta é inválida.";
}

if (
    $errorMessage === "" &&
    strlen($origin) > 150
) {
    $errorMessage =
        "O campo de origem deve possuir no máximo 150 caracteres.";
}

if (
    $errorMessage === "" &&
    strlen($destination) > 150
) {
    $errorMessage =
        "O campo de destino deve possuir no máximo 150 caracteres.";
}

if (
    $errorMessage === "" &&
    $departureDate !== ''
) {

    $date = DateTime::createFromFormat(
        'Y-m-d',
        $departureDate
    );

    if (
        !$date ||
        $date->format('Y-m-d') !== $departureDate
    ) {
        $errorMessage =
            "A data de saída informada é inválida.";
    }
}

if ($errorMessage === "") {

    try {

        $sqlTravels = "
            SELECT
                t.id_travel,
                t.origin,
                t.destination,
                t.departure_datetime,
                t.estimated_arrival,
                t.status_travel,
                c.name_company,
                d.name_driver,
                v.model,
                v.plate,
                v.capacity,
                COUNT(tp.id_client) AS passenger_count

            FROM travel t

            INNER JOIN company c
                ON t.id_company = c.id_company

            INNER JOIN driver d
                ON t.id_driver = d.id_driver

            INNER JOIN vehicle v
                ON t.id_vehicle = v.id_vehicle

            LEFT JOIN travel_passenger tp
                ON t.id_travel = tp.id_travel

            WHERE t.status_travel = 'AGENDADA'

            AND (
                ? = 0
                OR t.id_company = ?
            )

            AND (
                ? = ''
                OR t.origin LIKE CONCAT('%', ?, '%')
            )

            AND (
                ? = ''
                OR t.destination LIKE CONCAT('%', ?, '%')
            )

            AND (
                ? = ''
                OR DATE(t.departure_datetime) = ?
            )

            GROUP BY
                t.id_travel,
                t.origin,
                t.destination,
                t.departure_datetime,
                t.estimated_arrival,
                t.status_travel,
                c.name_company,
                d.name_driver,
                v.model,
                v.plate,
                v.capacity

            ORDER BY
                t.departure_datetime ASC
        ";

        $stmtTravels = mysqli_prepare(
            $connect,
            $sqlTravels
        );

        mysqli_stmt_bind_param(
            $stmtTravels,
            "iissssss",
            $idCompany,
            $idCompany,
            $origin,
            $origin,
            $destination,
            $destination,
            $departureDate,
            $departureDate
        );

        mysqli_stmt_execute(
            $stmtTravels
        );

        $resultTravels =
            mysqli_stmt_get_result(
                $stmtTravels
            );

        while (
            $travel =
            mysqli_fetch_assoc(
                $resultTravels
            )
        ) {
            $travels[] = $travel;
        }

        mysqli_stmt_close(
            $stmtTravels
        );

    } catch (mysqli_sql_exception $e) {

        $errorMessage =
            "Não foi possível consultar as viagens agendadas.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Resultado da Consulta | Brasil Viagens
    </title>

    <link
        rel="stylesheet"
        href="../css/response-scheduled-travels.css"
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

            <a href="../html/scheduled-travels.php">
                Nova Consulta
            </a>

            <a href="../html/schedule-travel.php">
                Agendar Viagem
            </a>

        </nav>

    </header>

    <main class="main">

        <section class="results-container">

            <div class="results-header">

                <span class="subtitle">
                    Consulta de viagens
                </span>

                <h1>
                    Viagens agendadas
                </h1>

                <p>
                    Resultado da consulta realizada no sistema
                    da Brasil Viagens.
                </p>

            </div>

            <?php if ($errorMessage !== ""): ?>

                <div class="message-card error-card">

                    <div class="status-symbol">
                        !
                    </div>

                    <h2>
                        Não foi possível realizar a consulta
                    </h2>

                    <p>
                        <?php
                            echo htmlspecialchars(
                                $errorMessage
                            );
                        ?>
                    </p>

                    <a
                        href="../html/scheduled-travels.php"
                        class="button primary-button"
                    >
                        Voltar aos filtros
                    </a>

                </div>

            <?php elseif (count($travels) === 0): ?>

                <div class="message-card empty-card">

                    <div class="status-symbol">
                        0
                    </div>

                    <h2>
                        Nenhuma viagem encontrada
                    </h2>

                    <p>
                        Não existem viagens agendadas
                        que correspondam aos filtros informados.
                    </p>

                    <div class="actions">

                        <a
                            href="../html/scheduled-travels.php"
                            class="button primary-button"
                        >
                            Alterar filtros
                        </a>

                        <a
                            href="../html/schedule-travel.php"
                            class="button secondary-button"
                        >
                            Agendar viagem
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <div class="results-summary">

                    <span>
                        Viagens encontradas
                    </span>

                    <strong>
                        <?php echo count($travels); ?>
                    </strong>

                </div>

                <div class="travel-list">

                    <?php foreach ($travels as $travel): ?>

                        <?php

                        $departureFormatted =
                            date(
                                'd/m/Y H:i',
                                strtotime(
                                    $travel['departure_datetime']
                                )
                            );

                        $arrivalFormatted =
                            "Não informada";

                        if (
                            !empty(
                                $travel['estimated_arrival']
                            )
                        ) {

                            $arrivalFormatted =
                                date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $travel['estimated_arrival']
                                    )
                                );
                        }

                        ?>

                        <article class="travel-card">

                            <div class="travel-card-header">

                                <div>

                                    <span class="travel-code">
                                        Viagem
                                        #<?php echo (int)$travel['id_travel']; ?>
                                    </span>

                                    <h2>
                                        <?php
                                            echo htmlspecialchars(
                                                $travel['origin']
                                                . " → "
                                                . $travel['destination']
                                            );
                                        ?>
                                    </h2>

                                </div>

                                <span class="status-badge">
                                    <?php
                                        echo htmlspecialchars(
                                            $travel['status_travel']
                                        );
                                    ?>
                                </span>

                            </div>

                            <div class="travel-details">

                                <div class="detail-item">

                                    <span>
                                        Empresa
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $travel['name_company']
                                            );
                                        ?>
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Motorista
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $travel['name_driver']
                                            );
                                        ?>
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Veículo
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $travel['model']
                                                . " - "
                                                . $travel['plate']
                                            );
                                        ?>
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Capacidade
                                    </span>

                                    <strong>
                                        <?php
                                            echo (int)$travel['capacity'];
                                        ?>
                                        lugares
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Data de saída
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $departureFormatted
                                            );
                                        ?>
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Chegada prevista
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $arrivalFormatted
                                            );
                                        ?>
                                    </strong>

                                </div>

                                <div class="detail-item">

                                    <span>
                                        Passageiros
                                    </span>

                                    <strong>
                                        <?php
                                            echo (int)$travel['passenger_count'];
                                        ?>
                                    </strong>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <div class="bottom-actions">

                    <a
                        href="../html/scheduled-travels.php"
                        class="button secondary-button"
                    >
                        Alterar filtros
                    </a>

                    <a
                        href="../html/schedule-travel.php"
                        class="button primary-button"
                    >
                        Agendar nova viagem
                    </a>

                </div>

            <?php endif; ?>

        </section>

    </main>

    <footer class="footer">

        <p>
            &copy; 2026 Brasil Viagens.
            Todos os direitos reservados.
        </p>

    </footer>

</body>

</html>
