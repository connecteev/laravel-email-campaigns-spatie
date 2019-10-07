---
title: Introduction
weight: 1
---

**THIS PACKAGE IS STILL IN DEVELOPMENT, DO NOT USE YET**

This package allows people to subscribe to an email list. You can send a mail campaign to such a list. The package can track opens and clicks on links in the mails that are sent out.

```php
Campaign::create()
    ->subject('test')
    ->content('my content')
    ->trackOpens()
    ->trackClicks()
    ->to($list)
    ->send();
```

## We have badges!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-email-campaigns.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns)
[![Build Status](https://img.shields.io/travis/spatie/laravel-email-campaigns/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-email-campaigns)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-email-campaigns.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-email-campaigns)
[![StyleCI](https://github.styleci.io/repos/210674796/shield?branch=master)](https://github.styleci.io/repos/210674796)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-email-campaigns.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns)
