---
title: Unsubscribing from a list
weight: 3
---

The most common way that a subscriber is unsubscribed is if the subscriber click the unsubscribe link in a sent campaign. You can add an unsubscribe link by [adding an `::unsubscribeUrl::` placeholder](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-campaigns/creating-a-campaign/#setting-the-content-and-using-placeholders) to the html of your campaign.

When a subscriber visit the actual unsubscribe url a simple text will be displayed to confirm that unsubscribing was successful.

You can customize the response. First, publish the views

```php
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="views"
```

The response displayed after an unsubcribe can now be modified by editing these views in the `/resources/views/vendor/email-campaigns/unsubscribe/` direction:

- `unsubscribed.blade.php`
- `notFound.blade.php`

## Unsubscribing manually

You can unsubscribe someone manually like this

```php
$emailList->unsubscribe('john@example.com');
```

Alternatively you can call unsubscribe on a subscriber

```php
Subscriber::findForEmail('john@example.com')->unsubscribe();
```

## Permanently deleting a subscriber

Behind the scenes the subscriber and the subscription will not be deleted. Instead the status of the subscription will be updated to `unsubscribed`.
If you want to outright delete a subscription you can call `delete` on it

```php
$emailList->getSubscription('john@example.com')->delete();
```

If you want to delete a subscriber entirely you can call `delete` on it.

```php
Subscriber::findForEmail('john@example.com')->delete();
```

The code above will also delete all related subscriptions.
