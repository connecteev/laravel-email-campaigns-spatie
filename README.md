**THIS PACKAGE IS STILL IN DEVELOPMENT, DO NOT USE YET**

# Send email campaigns using Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-email-campaigns.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns)
[![Build Status](https://img.shields.io/travis/spatie/laravel-email-campaigns/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-email-campaigns)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-email-campaigns.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-email-campaigns)
[![StyleCI](https://github.styleci.io/repos/210674796/shield?branch=master)](https://github.styleci.io/repos/210674796)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-email-campaigns.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns)

This package allows you to easily send out email campaigns to a list of subscribers.

Let's take a quick look at how the package can be used. First, you must create an email list:

```php
$emailList = EmailList::create('newsletter subscribers');
```

Next, you can subscribe some people to a list. There's also support for [double opt-in subscriptions](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-lists/using-double-opt-in/)

```php
$emailList->subscribe('john@example.com');
$emailList->subscribe('paul@example.com');
```

You can send an email to all those subscribed to the list.

```
Campaign::create()
    ->subject('test')
    ->content($html)
    ->trackOpens()
    ->trackClicks()
    ->to($emailList)
    ->send();
```

After your campaign is sent, you can view some [interesting statistics](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-campaigns/viewing-statistics-of-a-sent-campaign/).

## Documention

You can view all documentation on [our dedicated documentation site](https://docs.spatie.be/laravel-email-campaigns/v1/introduction/).

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
