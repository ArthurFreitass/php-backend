<?php

include_once(__DIR__ . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $position = $_POST['position'];
    $birthdate = $_POST['birthdate'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $insertDB = mysqli_query(
        $connection,
        "INSERT INTO employee
        (position, birthdate, phone_number, address_employee, name_employee)
        VALUES
        ('$position', '$birthdate', '$phone', '$address', '$name')"
    );

    if ($insertDB) {
        echo "Funcionário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . mysqli_error($connection);
    }
}

?>