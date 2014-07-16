Yandex Metrika API (Yii2-wrapper)
=================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --dev --prefer-dist euromd/yii2-yandex-metrika "*"
```

or add

```
"euromd/yii2-yandex-metrika": "*"
```

to the require-dev section of your composer.json.


General Usage
-------------

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'yandexMetrika' => [
            'class' => '\EuroMD\yandexMetrika\Client',
            'clientID' => 'Your Yandex App client ID...',
            'clientSecret' => 'Your Yandex App client secret...'
        ],
    ],
];
```