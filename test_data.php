<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "sensor_db";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Database connection is OK";

// Проверяем, есть ли параметры temperature и humidity в URL
if (isset($_GET["temperature"]) && isset($_GET["humidity"])) {
    $t = $_GET["temperature"];
    $h = $_GET["humidity"];

    $sql = "INSERT INTO dht11 (temperature, humidity) VALUES (" . $t . ", " . $h . ")";

    if (mysqli_query($conn, $sql)) {
        echo "<br>New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
