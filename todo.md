### Log tool client todo:
- добавить проверку доступности временной папки.                                +                        
- добавить вывод количества ошибок за последние 5 минут.                        +
- добавить опцию времени жизни окна с ошибкой                                   +

- реализовать self-update                                                       
- написать документацию для client
    - запуск
    - остановка
    - добавление в автозагрузку.
    - обновления
- рефакторинг creator.php
- добавить проверку на доступность сервиса. 

### self-update -plan:
1. Добавить в log-tool api для получения последней верси log-tool-client. 
 - api-url-name: client-latest-version
 - api-url: log-tool.carid.com/api/get-client-latest-version

2. Добавить хранилище [log-tool-client](log-tool.carid.com/client/).

3. Добавить в log-tool api для скачки последнией версии log-tool-client
 - api-url-name: last-phar-client
 - api-url: log-tool.carid.com/api/get-last-phar-client

## Логика роботы обновления клиента

if latestVersion > currentVersion: 
    - Скачать новую версию. Отправка пост запроса к log-tool.carid.com/client/{version}            
    - Переименовать текущую версию в версию бэкап        
    - Переименовать новую в текущию (в тоже имя что было у бэкап версии)        
    - Удалить бэкап версию            
    - вывести сообщение об успехи: Update was successful!     
else:
    - вывести сообщение что у пользователя последняя доступная версия: You have the latest version of the log-tool client!
