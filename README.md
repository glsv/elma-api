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
<?php

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
<?php

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\services\ListService;

$baseUrl = 'https://elma365.domain.com/pub/v1/';
$token = '2ab8026f-ce23-4759-9530-xxxxxxxxxx'; 

$api = new ElmaClientApi($baseUrl, $token);

$service = new ListService($this->api);
$relativeUrl = 'app/test/mylist/list';

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

# $items содержит список всех элементов списка Elma
$items = $service->getAllItems($relativeUrl, $requestData);
```

### 3. Поиск по пользователям
С использованием абстракции `Request` и `Command`
- Request - обеспечивает корректное формирование параметров запроса
- Command - выполняет запрос и возвращает не сырой ответ от API, а только необходимые данные в нужном формате.
```
<?php

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\requests\GetUserListRequest;
use Glsv\ElmaApi\requests\params\TfFilters;
use Glsv\ElmaApi\commands\GetUserListCommand;

$baseUrl = 'https://elma365.domain.com/pub/v1/';
$token = '2ab8026f-ce23-4759-9530-xxxxxxxxxx'; 

$api = new ElmaClientApi($baseUrl, $token);

$tf = new \Glsv\ElmaApi\requests\params\TfFilters();
$tf->addFilter("login", "userlogin");

$r = new \Glsv\ElmaApi\requests\GetUserListRequest();
$r->setFilters($tf);

$command = new \Glsv\ElmaApi\commands\GetUserListCommand($api, $r);
$result = $command->execute();
```
#### Пример $result
`$result` является объектом `ResultListData` с публичными свойствами `$data` и `$total`
 - _total_ - общее кол-во объектов, удовлетворяющее условиям
 - _data_ - список объектов в виде массива их атрибутов 
```
Glsv\ElmaApi\responses\ResultListData Object
(
    [data] => Array
        (
            [0] => Array
                (
                    [__id] => f42bf145-6b1f-4c46-bbc1-8bfba09f0563
                    [email] => d.testerov@domain.com
                    [login] => userlogin
                    [__status] => Array
                        (
                            [order] => 0
                            [status] => 3
                        )

                    [fullname] => Array
                        (
                            [lastname] => 
                            [firstname] => 
                            [middlename] => 
                        )
                    ....
                    [__createdAt] => 2022-12-20T14:18:15.490542994Z
                    [__deletedAt] => 
                    [__updatedAt] => 2022-12-21T09:14:30.636072843Z
                )
        )

    [total] => 1
)

```

### 4. Получить элемент списка по ID
При успешном получении возвращается объект класса `Glsv\ElmaApi\responses\ResultItem`
```
<?php

use Glsv\ElmaApi\ElmaClientApi;
use Glsv\ElmaApi\requests\GetAppItemRequest;
use Glsv\ElmaApi\commands\GetAppItemCommand;

$baseUrl = 'https://elma365.domain.com/pub/v1/';
$token = '2ab8026f-ce23-4759-9530-xxxxxxxxxx'; 

$api = new ElmaClientApi($baseUrl, $token);


$r = new GetAppItemRequest('namespace', 'app_id', 'eb8c404a-0fd6-41c3-b153-xxxxxxxx');
$c = new GetAppItemCommand($api, $r);
$result = $c->execute();
```