<?php

include_once(__DIR__ . '/../php/connection.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$errorMessage = "";
$companies = [];
$totalScheduled = 0;

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

    $sqlTotal = "
        SELECT COUNT(*) AS total
        FROM travel
        WHERE status_travel = 'AGENDADA'
    ";

    $resultTotal = mysqli_query(
        $connect,
        $sqlTotal
    );

    $totalRow = mysqli_fetch_assoc(
        $resultTotal
    );

    $totalScheduled = (int)$totalRow['total'];

} catch (mysqli_sql_exception $e) {

    $errorMessage =
        "Não foi possível carregar os dados para consulta.";
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Viagens Agendadas | Brasil Viagens
    </title>

    <link
        rel="stylesheet"
        href="../css/scheduled-travels.css"
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

            <a href="./cancel-travel.php">
                Cancelar Viagem
            </a>

        </nav>

    </header>

    <main class="main">

        <section class="consult-container">

            <div class="consult-info">

                <span class="subtitle">
                    Gestão de viagens
                </span>

                <h1>
                    Consulte as viagens agendadas
                </h1>

                <p>
                    Utilize os filtros para localizar viagens
                    cadastradas com status AGENDADA.
                </p>

                <p>
                    Você pode consultar todas as viagens ou
                    restringir a busca por empresa, origem,
                    destino e data de saída.
                </p>

                <div class="scheduled-total">

                    <span>
                        Viagens agendadas
                    </span>

                    <strong>
                        <?php echo $totalScheduled; ?>
                    </strong>

                </div>

            </div>

            <div class="form-container">

                <div class="form-header">

                    <h2>
                        Filtros da consulta
                    </h2>

                    <p>
                        Todos os filtros são opcionais.
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
                        action="../php/scheduledTravels.php"
                        method="get"
                    >

                        <div class="form-group">

                            <label for="id_company">
                                Empresa responsável
                            </label>

                            <select
                                name="id_company"
                                id="id_company"
                            >

                                <option value="0">
                                    Todas as empresas
                                </option>

                                <?php foreach ($companies as $company): ?>

                                    <option
                                        value="<?php echo (int)$company['id_company']; ?>"
                                    >
                                        <?php
                                            echo htmlspecialchars(
                                                $company['name_company']
                                            );
                                        ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-row">

                            <div class="form-group">

                                <label for="origin">
                                    Origem
                                </label>

                                <input
                                    type="text"
                                    name="origin"
                                    id="origin"
                                    maxlength="150"
                                    placeholder="Ex: Belo Horizonte"
                                >

                            </div>

                            <div class="form-group">

                                <label for="destination">
                                    Destino
                                </label>

                                <input
                                    type="text"
                                    name="destination"
                                    id="destination"
                                    maxlength="150"
                                    placeholder="Ex: São Paulo"
                                >

                            </div>

                        </div>

                        <div class="form-group">

                            <label for="departure_date">
                                Data de saída
                            </label>

                            <input
                                type="date"
                                name="departure_date"
                                id="departure_date"
                            >

                        </div>

                        <div class="form-actions">

                            <a
                                href="../index.html"
                                class="secondary-button"
                            >
                                Voltar
                            </a>

                            <button
                                type="submit"
                                class="primary-button"
                            >
                                Consultar viagens
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

</body>

</html>
