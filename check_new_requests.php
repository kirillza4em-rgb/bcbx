<?php
// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаемся к базе данных
$servername = "localhost";
$username = "047582029_diplom";
$password = "Diplom_41";
$dbname = "j38202257_diplom";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем временную метку, переданную из JavaScript
// Устанавливаем на 5 минут назад, если last_time не передан
$lastCheckTime = isset($_GET['last_time']) ? $_GET['last_time'] : date('Y-m-d H:i:s', strtotime('-5 minutes'));

// Преобразуем временную метку в формат, совместимый с SQL
$lastCheckTime = date('Y-m-d H:i:s', strtotime($lastCheckTime));

// Запрос для получения новых заявок за последние 5 минут
$sql = "SELECT FIO, otdel, categoriy, prioritet FROM nepoladki WHERE DateTime > ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Ошибка подготовки: ' . $conn->error);
}
$stmt->bind_param("s", $lastCheckTime);
$stmt->execute();
$result = $stmt->get_result();

// Собираем данные о новых заявках
$newRequests = [];
while ($row = $result->fetch_assoc()) {
    $newRequests[] = $row;
}

$stmt->close();
$conn->close();

// Возвращаем результат в формате JSON
echo json_encode(['new_count' => count($newRequests), 'new_requests' => $newRequests]);
?>