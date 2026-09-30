<?php

include_once(__DIR__ . '/../php/connection.php');

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);

$errorMessage = "";

$travels = [];


try {

    $sqlTravels = "
        SELECT
            t.id_travel,
            t.origin,
            t.destination,
            t.departure_datetime,

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

        WHERE t.status_travel = 'AGENDADA'

        ORDER BY t.departure_datetime ASC
    ";


    $resultTravels = mysqli_query(
        $connect,
        $sqlTravels
    );


    while (
        $travel =
        mysqli_fetch_assoc($resultTravels)
    ) {

        $travels[] = $travel;
    }


} catch (mysqli_sql_exception $e) {

    $errorMessage =
        "Não foi possível carregar as viagens agendadas.";
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
        Cancelar Viagem | Brasil Viagens
    </title>

    <link
        rel="stylesheet"
        href="../css/cancel-travel.css"
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

            <a href="./schedule-travel.php">
                Agendar Viagem
            </a>

            <a href="./scheduled-travels.php">
                Viagens Agendadas
            </a>

        </nav>

    </header>


    <main class="main">


        <section class="cancel-container">
            <div class="cancel-info">

                <span class="subtitle">
                    Gestão de viagens
                </span>


                <h1>
                    Cancelamento de viagem
                </h1>


                <p>
                    Selecione uma viagem agendada
                    para realizar o cancelamento.
                </p>


                <p>
                    A viagem não será excluída do nosso sistema.
                    Seu status será alterado para
                    <strong>CANCELADA</strong>,
                    mantendo o histórico registrado.
                </p>

            </div>

            <div class="form-container">


                <div class="form-header">

                    <h2>
                        Cancelar viagem
                    </h2>

                    <p>
                        Selecione uma viagem e informe
                        o motivo do cancelamento.
                    </p>

                </div>


                <?php if ($errorMessage !== ""): ?>


                    <div class="load-error">

                        <p>

                            <?php

                            echo htmlspecialchars(
                                $errorMessage
                            );

                            ?>

                        </p>

                    </div>


                <?php elseif (count($travels) === 0): ?>


                    <div class="empty-message">

                        <h3>
                            Nenhuma viagem disponível
                        </h3>

                        <p>
                            Não existem viagens agendadas
                            disponíveis para cancelamento.
                        </p>

                    </div>


                <?php else: ?>


                    <form
                        action="../php/cancelTravel.php"
                        method="post"
                    >

                        <div class="form-group">

                            <label for="id_travel">
                                Viagem *
                            </label>


                            <select
                                name="id_travel"
                                id="id_travel"
                                required
                            >

                                <option
                                    value=""
                                    selected
                                    disabled
                                >
                                    Selecione uma viagem
                                </option>


                                <?php foreach ($travels as $travel): ?>


                                    <?php

                                    $formattedDate =
                                        date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $travel['departure_datetime']
                                            )
                                        );

                                    ?>


                                    <option

                                        value="<?php
                                            echo (int)$travel['id_travel'];
                                        ?>"

                                        data-origin="<?php
                                            echo htmlspecialchars(
                                                $travel['origin']
                                            );
                                        ?>"

                                        data-destination="<?php
                                            echo htmlspecialchars(
                                                $travel['destination']
                                            );
                                        ?>"

                                        data-date="<?php
                                            echo htmlspecialchars(
                                                $formattedDate
                                            );
                                        ?>"

                                        data-company="<?php
                                            echo htmlspecialchars(
                                                $travel['name_company']
                                            );
                                        ?>"

                                        data-driver="<?php
                                            echo htmlspecialchars(
                                                $travel['name_driver']
                                            );
                                        ?>"

                                        data-vehicle="<?php
                                            echo htmlspecialchars(
                                                $travel['model']
                                                . ' - '
                                                . $travel['plate']
                                            );
                                        ?>"

                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            '#'
                                            . $travel['id_travel']
                                            . ' - '
                                            . $travel['origin']
                                            . ' → '
                                            . $travel['destination']
                                            . ' - '
                                            . $formattedDate
                                        );

                                        ?>

                                    </option>


                                <?php endforeach; ?>


                            </select>

                        </div>

                        <div
                            class="travel-details"
                            id="travel-details"
                        >

                            <h3>
                                Informações da viagem
                            </h3>


                            <div class="details-grid">


                                <div class="detail-item">

                                    <span>
                                        Origem
                                    </span>

                                    <strong id="detail-origin">
                                        -
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Destino
                                    </span>

                                    <strong id="detail-destination">
                                        -
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Saída
                                    </span>

                                    <strong id="detail-date">
                                        -
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Empresa
                                    </span>

                                    <strong id="detail-company">
                                        -
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Motorista
                                    </span>

                                    <strong id="detail-driver">
                                        -
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Veículo
                                    </span>

                                    <strong id="detail-vehicle">
                                        -
                                    </strong>

                                </div>


                            </div>

                        </div>


                        <div class="form-group">

                            <label for="cancellation_reason">
                                Motivo do cancelamento *
                            </label>


                            <textarea
                                name="cancellation_reason"
                                id="cancellation_reason"
                                maxlength="255"
                                rows="5"
                                placeholder="Informe o motivo do cancelamento"
                                required
                            ></textarea>


                            <small>
                                Máximo de 255 caracteres.
                            </small>

                        </div>

                        <div class="warning">

                            <strong>
                                Atenção
                            </strong>

                            <p>
                                Após confirmar, o status da viagem
                                será alterado para CANCELADA.
                            </p>

                        </div>


                        <!-- ACTIONS -->

                        <div class="form-actions">


                            <a
                                href="../index.html"
                                class="back-button"
                            >
                                Voltar
                            </a>


                            <button
                                type="submit"
                                class="cancel-button"
                            >
                                Confirmar cancelamento
                            </button>


                        </div>


                    </form>


                <?php endif; ?>


            </div>


        </section>


    </main>


    <footer class="footer">

        <p>
            &copy; 2026 Brasil Viagens.
            Todos os direitos reservados.
        </p>

    </footer>


    <script>

        const travelSelect =
            document.getElementById(
                "id_travel"
            );


        if (travelSelect) {


            travelSelect.addEventListener(
                "change",
                function () {


                    const selectedOption =
                        this.options[
                            this.selectedIndex
                        ];


                    document.getElementById(
                        "detail-origin"
                    ).textContent =
                        selectedOption.dataset.origin;


                    document.getElementById(
                        "detail-destination"
                    ).textContent =
                        selectedOption.dataset.destination;


                    document.getElementById(
                        "detail-date"
                    ).textContent =
                        selectedOption.dataset.date;


                    document.getElementById(
                        "detail-company"
                    ).textContent =
                        selectedOption.dataset.company;


                    document.getElementById(
                        "detail-driver"
                    ).textContent =
                        selectedOption.dataset.driver;


                    document.getElementById(
                        "detail-vehicle"
                    ).textContent =
                        selectedOption.dataset.vehicle;


                }
            );

        }

    </script>


</body>

</html>