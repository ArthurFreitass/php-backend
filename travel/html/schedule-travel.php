<?php

include_once(__DIR__ . '/../php/connection.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$errorMessage = "";

$companies = [];
$drivers = [];
$vehicles = [];
$clients = [];

// Date time

date_default_timezone_set('America/Sao_Paulo');
$todayDateTime = date('Y-m-d') . 'T00:00';

try {

    $sqlCompanies = "
        SELECT
            id_company,
            name_company
        FROM company
        ORDER BY name_company
    ";

    $resultCompanies = mysqli_query(
        $connect,
        $sqlCompanies
    );

    while ($company = mysqli_fetch_assoc($resultCompanies)) {

        $companies[] = $company;
    }


    $sqlDrivers = "
        SELECT
            id_driver,
            name_driver,
            id_company
        FROM driver
        WHERE status_driver = 'ATIVO'
        ORDER BY name_driver
    ";

    $resultDrivers = mysqli_query(
        $connect,
        $sqlDrivers
    );

    while ($driver = mysqli_fetch_assoc($resultDrivers)) {

        $drivers[] = $driver;
    }


    $sqlVehicles = "
        SELECT
            id_vehicle,
            model,
            plate,
            capacity,
            id_company
        FROM vehicle
        WHERE status_vehicle = 'DISPONIVEL'
        ORDER BY model
    ";

    $resultVehicles = mysqli_query(
        $connect,
        $sqlVehicles
    );

    while ($vehicle = mysqli_fetch_assoc($resultVehicles)) {

        $vehicles[] = $vehicle;
    }


    $sqlClients = "
        SELECT
            id_client,
            name_client,
            cpf
        FROM client
        ORDER BY name_client
    ";

    $resultClients = mysqli_query(
        $connect,
        $sqlClients
    );

    while ($client = mysqli_fetch_assoc($resultClients)) {

        $clients[] = $client;
    }
} catch (mysqli_sql_exception $e) {

    $errorMessage =
        "Não foi possível carregar os dados necessários para o agendamento.";
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Agendar Viagem | Brasil Bus
    </title>

    <link
        rel="stylesheet"
        href="../css/schedule-travel.css">

</head>


<body>


    <header class="header">

        <a
            href="../index.html"
            class="logo">
            Brasil Bus
        </a>


        <nav class="nav">

            <a href="../index.html">
                Início
            </a>

            <a href="./register-client.html">
                Cadastrar Cliente
            </a>

            <a href="./scheduled-travels.php">
                Viagens Agendadas
            </a>

        </nav>

    </header>


    <main class="main">


        <section class="schedule-container">


            <div class="schedule-info">

                <span class="subtitle">
                    Gestão de viagens
                </span>


                <h1>
                    Agende uma nova viagem
                </h1>


                <p>
                    Preencha os dados abaixo para cadastrar
                    uma nova viagem no sistema da Brasil Bus.
                </p>


                <p>
                    Escolha a empresa responsável,
                    o motorista, o veículo e o passageiro,
                    além das informações referentes ao trajeto.
                </p>

            </div>


            <div class="form-container">


                <div class="form-header">

                    <h2>
                        Dados da viagem
                    </h2>

                    <p>
                        Campos marcados com * são obrigatórios.
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

                <?php else: ?>


                    <form
                        action="../php/schedule.php"
                        method="post">


                        <div class="form-group">

                            <label for="id_company">
                                Empresa responsável *
                            </label>


                            <select
                                name="id_company"
                                id="id_company"
                                required>

                                <option
                                    value=""
                                    selected
                                    disabled>
                                    Selecione uma empresa
                                </option>


                                <?php foreach ($companies as $company): ?>

                                    <option
                                        value="<?php echo (int)$company['id_company']; ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $company['name_company']
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="id_driver">
                                Motorista *
                            </label>


                            <select
                                name="id_driver"
                                id="id_driver"
                                required
                                disabled>

                                <option
                                    value=""
                                    selected
                                    disabled>
                                    Primeiro selecione uma empresa
                                </option>


                                <?php foreach ($drivers as $driver): ?>

                                    <option
                                        value="<?php echo (int)$driver['id_driver']; ?>"
                                        data-company="<?php echo (int)$driver['id_company']; ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $driver['name_driver']
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="id_vehicle">
                                Veículo *
                            </label>


                            <select
                                name="id_vehicle"
                                id="id_vehicle"
                                required
                                disabled>

                                <option
                                    value=""
                                    selected
                                    disabled>
                                    Primeiro selecione uma empresa
                                </option>


                                <?php foreach ($vehicles as $vehicle): ?>

                                    <option
                                        value="<?php echo (int)$vehicle['id_vehicle']; ?>"
                                        data-company="<?php echo (int)$vehicle['id_company']; ?>"
                                        data-capacity="<?php echo (int)$vehicle['capacity']; ?>">

                                        <?php

                                        echo htmlspecialchars(
                                            $vehicle['model']
                                                . " - "
                                                . $vehicle['plate']
                                                . " - "
                                                . $vehicle['capacity']
                                                . " lugares"
                                        );

                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-row">


                            <div class="form-group">

                                <label for="origin">
                                    Origem *
                                </label>


                                <input
                                    type="text"
                                    name="origin"
                                    id="origin"
                                    maxlength="150"
                                    placeholder="Ex: Belo Horizonte - MG"
                                    required>

                            </div>


                            <div class="form-group">

                                <label for="destination">
                                    Destino *
                                </label>


                                <input
                                    type="text"
                                    name="destination"
                                    id="destination"
                                    maxlength="150"
                                    placeholder="Ex: São Paulo - SP"
                                    required>

                            </div>


                        </div>


                        <div class="form-row">


                            <div class="form-group">

                                <label for="departure_datetime">
                                    Data e horário de saída *
                                </label>

                                <input
                                    type="datetime-local"
                                    name="departure_datetime"
                                    id="departure_datetime"
                                    value="<?php echo $todayDateTime; ?>"
                                    min="<?php echo $todayDateTime; ?>"
                                    required>

                            </div>


                            <div class="form-group">

                                <label for="estimated_arrival">
                                    Previsão de chegada
                                </label>


                                <input
                                    type="datetime-local"
                                    name="estimated_arrival"
                                    id="estimated_arrival">

                            </div>


                        </div>


                        <div class="form-group">

                            <label for="id_client">
                                Passageiro *
                            </label>


                            <select
                                name="id_client"
                                id="id_client"
                                required>

                                <option
                                    value=""
                                    selected
                                    disabled>
                                    Selecione um passageiro
                                </option>


                                <?php foreach ($clients as $client): ?>

                                    <option
                                        value="<?php echo (int)$client['id_client']; ?>">

                                        <?php

                                        echo htmlspecialchars(
                                            $client['name_client']
                                                . " - "
                                                . $client['cpf']
                                        );

                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="seat_number">
                                Número do assento *
                            </label>


                            <input
                                type="number"
                                name="seat_number"
                                id="seat_number"
                                min="1"
                                placeholder="Selecione primeiro um veículo"
                                required
                                disabled>


                            <small id="seat-info">
                                O limite de assentos será definido
                                de acordo com o veículo escolhido.
                            </small>

                        </div>


                        <div class="form-actions">


                            <a
                                href="../index.html"
                                class="cancel-button">
                                Cancelar
                            </a>


                            <button
                                type="submit"
                                class="submit-button">
                                Agendar viagem
                            </button>


                        </div>


                    </form>


                <?php endif; ?>


            </div>


        </section>


    </main>


    <footer class="footer">

        <p>
            &copy; 2026 Brasil Bus.
            Todos os direitos reservados.
        </p>

    </footer>


    <script>
        const companySelect =
            document.getElementById("id_company");

        const driverSelect =
            document.getElementById("id_driver");

        const vehicleSelect =
            document.getElementById("id_vehicle");

        const seatInput =
            document.getElementById("seat_number");

        const seatInfo =
            document.getElementById("seat-info");


        companySelect.addEventListener(
            "change",
            function() {

                const companyId =
                    this.value;


                filterSelectByCompany(
                    driverSelect,
                    companyId,
                    "Selecione um motorista"
                );


                filterSelectByCompany(
                    vehicleSelect,
                    companyId,
                    "Selecione um veículo"
                );


                seatInput.value = "";

                seatInput.disabled = true;

                seatInput.removeAttribute("max");


                seatInfo.textContent =
                    "Selecione um veículo para definir o limite de assentos.";

            }
        );


        function filterSelectByCompany(
            select,
            companyId,
            placeholder
        ) {

            select.disabled = false;

            select.value = "";


            const options =
                select.querySelectorAll(
                    "option[data-company]"
                );


            let availableOptions = 0;


            options.forEach(
                function(option) {

                    const belongsToCompany =
                        option.dataset.company === companyId;


                    option.hidden = !belongsToCompany;

                    option.disabled = !belongsToCompany;


                    if (belongsToCompany) {
                        availableOptions++;
                    }

                }
            );


            const firstOption =
                select.options[0];


            if (availableOptions > 0) {

                firstOption.textContent =
                    placeholder;

            } else {

                firstOption.textContent =
                    "Nenhuma opção disponível";

            }

        }


        vehicleSelect.addEventListener(
            "change",
            function() {

                const selectedOption =
                    this.options[
                        this.selectedIndex
                    ];


                const capacity =
                    selectedOption.dataset.capacity;


                if (capacity) {

                    seatInput.disabled = false;

                    seatInput.max =
                        capacity;

                    seatInput.placeholder =
                        "Ex: 15";


                    seatInfo.textContent =
                        "Este veículo possui " +
                        capacity +
                        " lugares.";

                } else {

                    seatInput.disabled = true;

                    seatInput.value = "";

                }

            }
        );
    </script>


</body>

</html>