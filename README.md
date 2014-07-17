Yandex Metrika API (Yii2-wrapper)
=================================

[![Total Downloads](https://poser.pugx.org/euromd/yii2-yandex-metrika/downloads.svg)](https://packagist.org/packages/euromd/yii2-yandex-metrika)
[![Latest Stable Version](https://poser.pugx.org/euromd/yii2-yandex-metrika/v/stable.svg)](https://packagist.org/packages/euromd/yii2-yandex-metrika)
[![Latest Unstable Version](https://poser.pugx.org/euromd/yii2-yandex-metrika/v/unstable.svg)](https://packagist.org/packages/euromd/yii2-yandex-metrika)
[![License](https://poser.pugx.org/euromd/yii2-yandex-metrika/license.svg)](https://packagist.org/packages/euromd/yii2-yandex-metrika)

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