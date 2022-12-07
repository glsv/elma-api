# Назначение
Обертка для работы с API [BPM Elma365](https://elma365.com).
- Валидирует http-коды ответа и правильность возвращаемой структуры;
- Формализует использование для выполнения различных запросов к API;
- Содержит сервисы / методы для более удобной работы.

[Документация на api.elma365.com](https://api.elma365.com/ru/)

## Установка

```shell
composer require glsv/elma-api
```

## Зависимости
- PHP 8.1+
- [guzzlehttp/guzzle](https://github.com/guzzle/guzzle/)

## Использование
### 1. Выполнение произвольного запроса к API
```
use Glsv\ElmaApi\ElmaClientApi;

$baseUrl = 'https://elma365.domain.com/pub/v1/';
$token = '2ab8026f-ce23-4759-9530-xxxxxxxxxx'; 

$api = new ElmaClientApi($baseUrl, $token);

$relativeUrl = 'app/test/mylist/list';

# Опциональные параметры. Формат смотреть в документации на API для каждого применения.  
$requestData = [
    "active" => true,
    "filter" => [
        "tf" => [
            "my_custom_date" => [
                "min" => "2022-01-01",
                "max" => "2022-06-01",
            ]
        ]
    ],
];

# $results содержит оригинальный response (array) 
$results = $api->makePost($relativeUrl, $requestData);
```

### 2. Получение всех элементов списка
В отличие от предыдущего запроса, используется `ListService`,
который получает и возвращает все элементы списка, выполняя внутри себя 
итерации пакетной загрузки данных.

```
use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\services\ListService;

$baseUrl = 'https://elma365.domain.com/pub/v1/';
$token = '2ab8026f-ce23-4759-9530-xxxxxxxxxx'; 

$api = new ElmaClientApi($baseUrl, $token);

$service = new ListService($this->api);
$relativeUrl = 'app/test/mylist/list';

$items = $service->getAllItems($relativeUrl, $requestData);

# Опциональная фильтрация элементов списка по его атрибутам. 
$requestData = [
    "filter" => [
        "tf" => [
            "my_custom_date" => [
                "min" => "2022-01-01",
                "max" => "2022-06-01",
            ]
        ]
    ],
];

# $results содержит оригинальный response (array)
$items = $this->service->getAllItems($relativeUrl, $requestData);
```