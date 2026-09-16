<?php

include_once(__DIR__ . '/connection.php');

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pearson Specter - Resultado da busca</title>

    <link rel="stylesheet" href="../css/search.css">
</head>

<body>

    <header class="header">

        <nav class="nav">

            <div id="logo">

                <a href="../index.php">

                    <img
                        src="../img/Pearson_Specter.webp"
                        alt="Logo Pearson Specter">

                </a>

            </div>

            <ul id="links">

                <li>
                    <a href="../index.php#home">
                        Home
                    </a>
                </li>

                <li>
                    <a href="../index.php#funcionarios">
                        Conheça o nosso trabalho
                    </a>
                </li>

                <li>
                    <a href="../index.php#cadastro">
                        Cadastre o funcionário
                    </a>
                </li>

                <li>
                    <a href="../search.html">
                        Buscar funcionário
                    </a>
                </li>

            </ul>

        </nav>

    </header>


    <main>

        <section class="register-section">

            <div class="section-title">

                <span>Resultado da pesquisa</span>

                <h2>Funcionários encontrados</h2>

            </div>


            <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $position = $_POST['position'];

                $positionSelect = $_POST['positionSelect'];


                if ($position != '') {

                    $searchPosition = $position;

                } else {

                    $searchPosition = $positionSelect;

                }


                if ($searchPosition == '') {

                    echo "<h3>Digite ou selecione um cargo!</h3>";

                } else {

                    $searchByPosition = mysqli_query(
                        $connection,
                        "SELECT 
                            name_employee AS Funcionario,
                            position AS Cargo,
                            birthdate AS 'Data de nascimento',
                            phone_number AS Telefone,
                            address_employee AS Endereco
                        FROM employee
                        WHERE position = '$searchPosition'"
                    );


                    if (!$searchByPosition) {

                        echo "Erro na busca: " . mysqli_error($connection);

                    } else {

                        if (mysqli_num_rows($searchByPosition) == 0) {

                            echo "<h1>Nenhum funcionário encontrado!</h1>";

                        } else {

                            while ($line = mysqli_fetch_array($searchByPosition)) {

                                echo "<div class='results'>";

                                echo "<h2>" . $line['Funcionario'] . "</h2>";

                                echo "<p><strong>Cargo:</strong> "
                                    . $line['Cargo']
                                    . "</p>";

                                echo "<p><strong>Data de nascimento:</strong> "
                                    . $line['Data de nascimento']
                                    . "</p>";

                                echo "<p><strong>Telefone:</strong> "
                                    . $line['Telefone']
                                    . "</p>";

                                echo "<p><strong>Endereço:</strong> "
                                    . $line['Endereco']
                                    . "</p>";
                                echo "<br>"."<br>"."<br>";
                                echo "</div>";
                            }
                        }
                    }
                }
            }

            ?>


            <a href="../search.html">
                Voltar para busca
            </a>

        </section>

    </main>


    <footer class="footer">

        <p>
            © 2026 Pearson Specter
        </p>

    </footer>

</body>

</html>
