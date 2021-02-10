

# NpServerSettings

Данный модуль добавляет страницу "NetPing Server Settings" в интерфейс Zabbix.
С её помощью пользователь может изменить сетевые настройки, такие как: IPv4, Gateway, DNS (auto/manual).

## Установка модуля: 

1. При помощи терминала перейдите в папку: /usr/share/zabbix/modules
2. Выполните команду **git clone https://github.com/antoncom/NpServerSettings**
3. Смените владельца папки, например: **chown -R www-data. NpServerSettings**
5. Перейдите в интерфейс Zabbix: Menu -> Administration -> Gereral -> Modules
6. Используйте кнопку "Scan directory" для вывода модуля в список модулей
7. Активируйте модуль, кликнув по ссылке "Enable/Disable"
8. Перейдите в меню Zabbix: Menu -> Configuration -> NetPing Server Settings
9. Пользуйтесь модулем.

Установка модуля и демонстрация его работы показаны в данном скринкасте:

![enter image description here](https://github.com/antoncom/NpServerSettings/blob/main/screenshorts/simplescreenrecorder-2021-02-10_10.53.46.gif)

## Валидация данных в браузере

Данные формы защищены от ошибок пользователя, согласно данной инструкции:
[Универсальный метод валидации html-форм NetPing](https://netping.atlassian.net/wiki/spaces/PROJ/pages/2809857522/html-)

Проверочные диаграммы валидации полей формы:
* [IP v4 адрес (статический)](http://htmlpreview.github.io/?https://github.com/antoncom/NpServerSettings/blob/main/npm/ipv4.html)
* [Адрес гейта](http://htmlpreview.github.io/?https://github.com/antoncom/NpServerSettings/blob/main/npm/ip_single.html)
* [Адрес DNS сервера](http://htmlpreview.github.io/?https://github.com/antoncom/NpServerSettings/blob/main/npm/ip_single.html)

## Особенности запуска Bash-скрпита

Данный модуль управляет сетевым интерфейсом посредством bash-скрипта. Разрешение на запуск скрипта реализовано согласно данной инструкции: [UI-модуль Zabbix v.5 - разрешение на запуск Bash-скриптов на сервере](https://netping.atlassian.net/wiki/spaces/PROJ/pages/2822635521/UI-+Zabbix+v.5+-+Bash-)

## Known Issues
1. Если web-адрес Zabbix задан не доменным именем, а числовым IP-адресом, то при изменении IPv4 может произойти отключение связи с Zabbix. В этом случае, нужно указать в адресной строке браузера новый IP-адрес сервера Zabbix.