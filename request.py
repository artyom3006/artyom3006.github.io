import requests

# Функция для отправки GET-запроса с данными о температуре и влажности
def send_data(temperature, humidity):
    url = "http://localhost/dht11_project/test_data.php"
    params = {
        "temperature": temperature,
        "humidity": humidity
    }
    try:
        response = requests.get(url, params=params)
        # Проверяем статус ответа
        if response.status_code == 200:
            print("Data sent successfully")
        else:
            print("Failed to send data. Status code:", response.status_code)
    except Exception as e:
        print("An error occurred:", str(e))

# Бесконечный цикл для отправки данных каждую секунду
temperature = 100
humidity = 150
for _ in range(250):
    # Генерация случайных значений температуры и влажности
    temperature -= 0.1 # Значения от 20 до 30
    humidity -= 0.2     # Значения от 30 до 60
    # Отправка данных
    send_data(temperature, humidity)
    
for _ in range (250):
    temperature += 0.1 # Значения от 20 до 30
    humidity += 0.2     # Значения от 30 до 60
    # Отправка данных
    send_data(temperature, humidity)
