<?php

include_once(__DIR__ . '/connection.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('America/Sao_Paulo');

$success = false;
$errorMessage = "";
$idTravel = null;

$origin = "";
$destination = "";
$departureDatetime = "";
$estimatedArrival = "";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/schedule-travel.html");
    exit;
}

$origin = trim($_POST['origin'] ?? '');

$destination = trim(
    $_POST['destination'] ?? ''
);

$departureDatetime =
    $_POST['departure_datetime'] ?? '';

$estimatedArrival =
    $_POST['estimated_arrival'] ?? '';



$idCompany = filter_var(
    $_POST['id_company'] ?? null,
    FILTER_VALIDATE_INT
);

$idDriver = filter_var(
    $_POST['id_driver'] ?? null,
    FILTER_VALIDATE_INT
);

$idVehicle = filter_var(
    $_POST['id_vehicle'] ?? null,
    FILTER_VALIDATE_INT
);

$idClient = filter_var(
    $_POST['id_client'] ?? null,
    FILTER_VALIDATE_INT
);

$seatNumber = filter_var(
    $_POST['seat_number'] ?? null,
    FILTER_VALIDATE_INT
);


if (
    $origin === '' ||
    $destination === '' ||
    $departureDatetime === '' ||
    $idCompany === false ||
    $idDriver === false ||
    $idVehicle === false ||
    $idClient === false ||
    $seatNumber === false
) {

    $errorMessage =
        "Preencha corretamente todos os campos obrigatórios.";
}


if (
    $errorMessage === "" &&
    (
        $idCompany <= 0 ||
        $idDriver <= 0 ||
        $idVehicle <= 0 ||
        $idClient <= 0 ||
        $seatNumber <= 0
    )
) {

    $errorMessage =
        "Os dados selecionados para empresa, motorista, veículo, passageiro ou assento são inválidos.";
}

if (
    $errorMessage === "" &&
    (
        strlen($origin) > 150 ||
        strlen($destination) > 150
    )
) {

    $errorMessage =
        "Origem e destino devem possuir no máximo 150 caracteres.";
}

if (
    $errorMessage === "" &&
    strcasecmp(
        $origin,
        $destination
    ) === 0
) {

    $errorMessage =
        "A origem e o destino não podem ser iguais.";
}

$departure = null;

$arrival = null;


if ($errorMessage === "") {

    try {

        $departure =
            new DateTime(
                $departureDatetime
            );


        if ($estimatedArrival !== '') {

            $arrival =
                new DateTime(
                    $estimatedArrival
                );
        }


        $now = new DateTime();


        if ($departure <= $now) {

            $errorMessage =
                "A data de saída deve ser posterior ao momento atual.";
        }


        if (
            $errorMessage === "" &&
            $arrival !== null &&
            $arrival <= $departure
        ) {

            $errorMessage =
                "A previsão de chegada deve ser posterior à data de saída.";
        }

    } catch (Exception $e) {

        $errorMessage =
            "A data ou o horário informado é inválido.";
    }
}

$departureSql = null;

$arrivalSql = null;


if ($errorMessage === "") {

    $departureSql =
        $departure->format(
            'Y-m-d H:i:s'
        );


    if ($arrival !== null) {

        $arrivalSql =
            $arrival->format(
                'Y-m-d H:i:s'
            );
    }
}

if ($errorMessage === "") {

    try {

        $sqlCompany = "
            SELECT id_company
            FROM company
            WHERE id_company = ?
        ";


        $stmtCompany =
            mysqli_prepare(
                $connect,
                $sqlCompany
            );


        mysqli_stmt_bind_param(
            $stmtCompany,
            "i",
            $idCompany
        );


        mysqli_stmt_execute(
            $stmtCompany
        );


        $resultCompany =
            mysqli_stmt_get_result(
                $stmtCompany
            );


        if (
            mysqli_num_rows(
                $resultCompany
            ) === 0
        ) {

            $errorMessage =
                "A empresa selecionada não existe.";
        }


        mysqli_stmt_close(
            $stmtCompany
        );


        if ($errorMessage === "") {

            $sqlDriver = "
                SELECT
                    id_driver,
                    id_company,
                    status_driver
                FROM driver
                WHERE id_driver = ?
            ";


            $stmtDriver =
                mysqli_prepare(
                    $connect,
                    $sqlDriver
                );


            mysqli_stmt_bind_param(
                $stmtDriver,
                "i",
                $idDriver
            );


            mysqli_stmt_execute(
                $stmtDriver
            );


            $resultDriver =
                mysqli_stmt_get_result(
                    $stmtDriver
                );


            if (
                mysqli_num_rows(
                    $resultDriver
                ) === 0
            ) {

                $errorMessage =
                    "O motorista selecionado não existe.";

            } else {

                $driver =
                    mysqli_fetch_assoc(
                        $resultDriver
                    );


                if (
                    $driver['status_driver']
                    !== 'ATIVO'
                ) {

                    $errorMessage =
                        "O motorista selecionado está inativo.";

                } elseif (
                    (int)$driver['id_company']
                    !==
                    (int)$idCompany
                ) {

                    $errorMessage =
                        "O motorista selecionado não pertence à empresa responsável pela viagem.";
                }
            }


            mysqli_stmt_close(
                $stmtDriver
            );
        }

        if ($errorMessage === "") {

            $sqlVehicle = "
                SELECT
                    id_vehicle,
                    id_company,
                    capacity,
                    status_vehicle
                FROM vehicle
                WHERE id_vehicle = ?
            ";


            $stmtVehicle =
                mysqli_prepare(
                    $connect,
                    $sqlVehicle
                );


            mysqli_stmt_bind_param(
                $stmtVehicle,
                "i",
                $idVehicle
            );


            mysqli_stmt_execute(
                $stmtVehicle
            );


            $resultVehicle =
                mysqli_stmt_get_result(
                    $stmtVehicle
                );


            if (
                mysqli_num_rows(
                    $resultVehicle
                ) === 0
            ) {

                $errorMessage =
                    "O veículo selecionado não existe.";

            } else {

                $vehicle =
                    mysqli_fetch_assoc(
                        $resultVehicle
                    );


                if (
                    $vehicle['status_vehicle']
                    !== 'DISPONIVEL'
                ) {

                    $errorMessage =
                        "O veículo selecionado está indisponível.";

                } elseif (
                    (int)$vehicle['id_company']
                    !==
                    (int)$idCompany
                ) {

                    $errorMessage =
                        "O veículo selecionado não pertence à empresa responsável pela viagem.";

                } elseif (
                    $seatNumber >
                    (int)$vehicle['capacity']
                ) {

                    $errorMessage =
                        "O número do assento ultrapassa a capacidade do veículo selecionado.";
                }
            }


            mysqli_stmt_close(
                $stmtVehicle
            );
        }

        if ($errorMessage === "") {

            $sqlClient = "
                SELECT id_client
                FROM client
                WHERE id_client = ?
            ";


            $stmtClient =
                mysqli_prepare(
                    $connect,
                    $sqlClient
                );


            mysqli_stmt_bind_param(
                $stmtClient,
                "i",
                $idClient
            );


            mysqli_stmt_execute(
                $stmtClient
            );


            $resultClient =
                mysqli_stmt_get_result(
                    $stmtClient
                );


            if (
                mysqli_num_rows(
                    $resultClient
                ) === 0
            ) {

                $errorMessage =
                    "O passageiro selecionado não existe.";
            }


            mysqli_stmt_close(
                $stmtClient
            );
        }

    } catch (mysqli_sql_exception $e) {

        $errorMessage =
            "Não foi possível validar os dados da viagem no banco de dados.";
    }
}

if ($errorMessage === "") {

    $transactionStarted = false;


    try {

        mysqli_begin_transaction(
            $connect
        );


        $transactionStarted = true;

        $sqlTravel = "
            INSERT INTO travel (
                origin,
                destination,
                departure_datetime,
                estimated_arrival,
                status_travel,
                id_company,
                id_driver,
                id_vehicle
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                'AGENDADA',
                ?,
                ?,
                ?
            )
        ";


        $stmtTravel =
            mysqli_prepare(
                $connect,
                $sqlTravel
            );


        mysqli_stmt_bind_param(
            $stmtTravel,
            "ssssiii",
            $origin,
            $destination,
            $departureSql,
            $arrivalSql,
            $idCompany,
            $idDriver,
            $idVehicle
        );


        mysqli_stmt_execute(
            $stmtTravel
        );


        $idTravel =
            mysqli_insert_id(
                $connect
            );


        mysqli_stmt_close(
            $stmtTravel
        );


        $sqlPassenger = "
            INSERT INTO travel_passenger (
                id_travel,
                id_client,
                seat_number
            )
            VALUES (?, ?, ?)
        ";


        $stmtPassenger =
            mysqli_prepare(
                $connect,
                $sqlPassenger
            );


        mysqli_stmt_bind_param(
            $stmtPassenger,
            "iii",
            $idTravel,
            $idClient,
            $seatNumber
        );


        mysqli_stmt_execute(
            $stmtPassenger
        );


        mysqli_stmt_close(
            $stmtPassenger
        );


        mysqli_commit(
            $connect
        );


        $success = true;

    } catch (mysqli_sql_exception $e) {

        if ($transactionStarted) {

            mysqli_rollback(
                $connect
            );
        }


        if ($e->getCode() === 1062) {

            $errorMessage =
                "Já existe um registro com os mesmos dados para esta viagem.";

        } else {

            $errorMessage =
                "Não foi possível agendar a viagem. Nenhuma informação foi salva.";
        }
    }
}


$formattedDeparture = "";


if ($departure instanceof DateTime) {

    $formattedDeparture =
        $departure->format(
            'd/m/Y \à\s H:i'
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
        Resultado do Agendamento | Brasil Bus
    </title>

    <link
        rel="stylesheet"
        href="../css/response-schedule.css"
    >

</head>


<body>


    <header class="header">

        <a
            href="../index.html"
            class="logo"
        >
            Brasil Bus
        </a>


        <nav class="nav">

            <a href="../index.html">
                Início
            </a>

            <a href="../html/schedule-travel.php">
                Agendar Viagem
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
                    Viagem agendada
                </span>


                <h1>
                    Agendamento realizado com sucesso!
                </h1>


                <p>
                    A viagem foi cadastrada no sistema
                    da Brasil Bus e está com o status
                    <strong>AGENDADA</strong>.
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
                                $origin .
                                " → " .
                                $destination
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
                                $formattedDeparture
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="summary-item">

                        <span>
                            Assento
                        </span>

                        <strong>
                            <?php echo (int)$seatNumber; ?>
                        </strong>

                    </div>


                </div>


                <div class="actions">


                    <a
                        href="../html/schedule-travel.php"
                        class="button secondary-button"
                    >
                        Agendar outra viagem
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
                    Erro no agendamento
                </span>


                <h1>
                    Não foi possível agendar a viagem
                </h1>


                <p>

                    <?php

                    echo htmlspecialchars(
                        $errorMessage
                    );

                    ?>

                </p>


                <p class="help-text">
                    Revise os dados informados e tente novamente.
                </p>


                <div class="actions">


                    <a
                        href="../html/schedule-travel.php"
                        class="button primary-button"
                    >
                        Voltar ao agendamento
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
            &copy; 2026 Brasil Bus.
            Todos os direitos reservados.
        </p>

    </footer>


</body>

</html>