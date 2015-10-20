# ADM Notify

ADM Notify - Amazon Device Messaging for amazon devices

- [Github Project](https://github.com/vladdnepr/adm-notify)

## Library is in initial development.

Anything may change at any time.

## Using

    $notify = \vladdnepr\amazon\adm\Notify;
    $notify->clientId = 'CLIENT_ID';
    $notify->clientSecret = 'CLIENT_SECRET';
    $notify->sendData('DEVICE_ID', ['key1' => 'value1', 'key2' => 'value2']);
    $notify->sendMessage('DEVICE_ID', 'MESSAGE');

## Installation

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this package using the following command:

```php
php composer.phar require "vladdnepr/adm-notify" "*"
```
or add

```json
"vladdnepr/adm-notify": "*"
```

to the require section of your application's `composer.json` file.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Vladislav Lyshenko](https://github.com/vladdnepr)
- [All Contributors](../../contributors)

## License

Public domain. Please see [License File](LICENSE.md) for more information.
