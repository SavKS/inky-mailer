# Встановлення

```bash
composer require savks/inky-mailer
```

# Налаштування

1. Налаштуйте змінні середовища в файлі .env.
    - Загальні:
        - **INKY_MAILER_SERVER_PATH** — директорія в яку буде поміщено файли серверу.
        - _(не обов'язково)_ **INKY_MAILER_CONNECTION** — тип з'єднання з сервером для редеру (tcp або unix).
          _Замовчування — **tcp**._
        - _(не обов'язково)_ **INKY_MAILER_SERVICE_NAME** — назва сервісу для systemd.
          _Замовчування — **Inky render server**._
        - _(не обов'язково)_ **INKY_MAILER_SERVICE_NAME** — назва файлу сервісу для systemd.
          _Замовчування — **inky-render-server**._

    - Для типу з'єднання **TCP**:
        - **INKY_MAILER_TCP_HOST** — адреса рендер-серверу.
        - **INKY_MAILER_TCP_PORT** — порт рендер-серверу.

    - Для типу з'єднання **UNIX**:
        - **INKY_MAILER_UNIX_PATH** — шлях до unix-сокету рендер-сервера.

    - Налаштування рендеру:
        - **INKY_MAILER_RENDER_OPTS_INLINE_CSS** — вмикає вбудовування стилів в HTML-код
          (_значно збільшує час рендеру_).
        - **INKY_MAILER_RENDER_OPTS_MINIFY** — вмикає мініфікацію коду.

2. Опублікуйте файли сервера в довільну директорію, за допомогою команди:

```bash
php artisan inky-mailer:publish:server
```

3. Створіть файл сервісу для systemd:

```bash
php inky-mailer:publish:service
```

4. Наступні кроки відбуваються за допомогою команд `systemctl`:
    - **systemctl enable --user service_file_name** — підключає файл сервісу до systemd. (_не запускаючи сам сервіс_).

    - **systemctl start --user service_file_name** — запускає сервіс через systemd.

    - **systemctl enable --user --now service_file_name** — виконує підключення та запуск сервісу.

    - **systemctl disable --user service_file_name** — від'єднує файл сервісу від systemd. (_не зупиняючи сам сервіс_).

    - **systemctl stop --user service_file_name** — зупиняє сервіс.

    - **systemctl disable --user --now service_file_name** — виконує від'єднання та зупинку роботи сервісу.

    - **systemctl restart --user service_file_name** — перезапускає сервіс.

    - **systemctl status --user service_file_name** — перевірка стану сервісу.

> Для тестового запуску серверу можна використовувати команду `php inky-mailer:server:start`.
