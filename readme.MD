SMSTRAFFIC Notifier
=============

Provides [SMSC](https://smstraffic.ru/) integration for Symfony Notifier.

Installation
-----------

composer.json:

```json
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/proklung/smstraffic-notifier"
        }
    ]
```

```bash

composer require proklung/smstraffic-notifier

```

DSN example
-----------

```
SMSTRAFFIC_DSN=smstraffic://LOGIN:PASSWORD@default?from=FROM
```

where:
 - `LOGIN` is your login
 - `PASSWORD` is your API password
 - `FROM` is your sender (NB: text identity, not a phone number)
 
 Нюансы
 ------
 
 В кастомных вариантах `Symfony` фабрику `SmsTrafficTransportFactory` надо пометить тэгом вручную (автоконфигурация
 не срабатывает).
 
 `services.yaml`
 
 ```yaml
  Prokl\Component\Notifier\Bridge\SmsTrafficTransportFactory:
    class: Prokl\Component\Notifier\Bridge\SmsTrafficTransportFactory
    tags: ['texter.transport_factory']
 ```
 
 