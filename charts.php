<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "sensor_db";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Проверяем, был ли передан параметр даты в URL
if (isset($_GET["date"])) {
    $selected_date = $_GET["date"];

    // Получаем данные за выбранный день из базы данных
    if (isset($_GET["start_time"]) && isset($_GET["end_time"])) {
        $start_time = $_GET["start_time"];
        $end_time = $_GET["end_time"];
        
        // Получаем данные за выбранный временной интервал
        $sql = "SELECT * FROM dht11 WHERE datetime BETWEEN '$selected_date $start_time' AND '$selected_date $end_time'";
    } else {
        // Получаем данные за весь выбранный день
        $sql = "SELECT * FROM dht11 WHERE DATE(datetime) = '$selected_date'";
    }

    $result = mysqli_query($conn, $sql);

    // Инициализируем массивы для хранения данных температуры, влажности и времени
    $temperature_data = [];
    $humidity_data = [];
    $time_labels = [];

    // Обрабатываем результаты запроса
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Добавляем данные в массивы
            $temperature_data[] = $row['temperature'];
            $humidity_data[] = $row['humidity'];
            // Форматируем время и добавляем его в массив меток
            $time_labels[] = date("H:i:s", strtotime($row['datetime']));
        }
    } else {
        echo "No data available for the selected date.";
    }
} else {
    $selected_date = date("Y-m-d");
}

// Если не указаны параметры времени, выставляем их на начало и конец дня
if (!isset($_GET["start_time"])) {
    $_GET["start_time"] = "00:00:00";
}
if (!isset($_GET["end_time"])) {
    $_GET["end_time"] = "23:59:59";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Графики</title>
    <!-- Подключаем библиотеку Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Графики за <?php echo $selected_date; ?></h1>
    <!-- Создаем canvas для графика -->
    <canvas id="myChart" width="400" height="200"></canvas>

    <div>
        <!-- Форма для выбора временного интервала -->
        <form action="" method="get">
            <input type="date" name="date" value="<?php echo $selected_date; ?>">
            <input type="time" name="start_time" value="<?php echo $_GET["start_time"]; ?>">
            <input type="time" name="end_time" value="<?php echo $_GET["end_time"]; ?>">
            <button type="submit">Показать</button>
        </form>
    </div>

    <script>
        // Получаем данные из PHP и передаем их в JavaScript
        var temperatureData = <?php echo json_encode($temperature_data); ?>;
        var humidityData = <?php echo json_encode($humidity_data); ?>;
        var timeLabels = <?php echo json_encode($time_labels); ?>;

        // Создаем объект графика
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timeLabels, // Метки оси X теперь содержат время
                datasets: [{
                    label: 'Температура',
                    data: temperatureData,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2 // Увеличиваем толщину линии
                }, {
                    label: 'Влажность',
                    data: humidityData,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2 // Увеличиваем толщину линии
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
