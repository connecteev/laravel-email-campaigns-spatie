---
title: Subscribing to a list
weight: 2
---

You can use a string to subscribe to an email list.

```php
$emailList->subscribe('john@example.com');
```

Behind the scenes, we'll create a `Subscriber` with email `john@example.com` and a `Subscription`, which is the relation between a subscriber and a list.

If you try to subscribe an email that already exists on a list it will be ignored.

You can also use a `Subscriber` to subscribe to an email list.

```php
$subscriber = Subscriber::findForEmail('john@example.com');

$subscriber->subscribeTo($anotherEmailList);
```

## Adding extra attributes

You can add extra attributes to a subscriber, by passing an array as the second argument of the subscribe method.

```php
$emailList->subscribe('john@example.com', [
    'first_name' => 'John',
    'last_name' => 'Doe'
]);

$subscriber = Subscriber::findForEmail('john@example.com');

$subscriber->extra_attributes->get('first_name'); // returns 'John';
$subscriber->extra_attributes->get('last_name'); // returns 'Doe';
```

You can read more on extra attributes in [this section of the docs](https://docs.spatie.be/laravel-email-campaigns/v1/advanced-usage/working-with-extra-attributes-on-subscribers/).

## Checking if someone is subscribed

You can check if a given email is subscribed to an email list.

```php
$emailList->isSubscribed('john@example.com'); // returns a boolean
```

You can use a subscriber to check to if it is subscriber to a list.

```php
$subscriber->isSubscribedTo($emailList) // returns a boolean;
```

## Getting all list subscribers

To get all subscribers of an email list you can use the `emailList` you can call `subscribers` on an email list.

```php
$subscribers = $emailList->subscribers; // returns all subscriber
```

To get the email address of a subscriber call `email` on a subscriber.

```php
$email = $subscribers->first()->email;
```

Calling `subscribers` on an email list will only return subscribers that have a subscription with a `subscribed` status. Subscribers that have unsubscribed or are still pending (when using [double opt in](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-lists/using-double-opt-in/)) will not be returned.

To return all subscribers, including all pending and unsubscribed ones, use `allSubscribers`.

```php
$allSubscribers = $emailList->allSubscribers;
```

## Skipping opt in when subscribing

If [double opt-in](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-lists/using-double-opt-in/) is enabled on a list, then `subscribeTo` won't result in an immediate subscription. Instead, the user must first confirm, by clicking a link in a mail, before their subscription to the new list is completed.

To immediately confirm someone, and skipping sending the confirmation mail, use `subscribeNow`:

```php
$emailList->subscribeNow('john@example.com');

// or using an existing subscriber
$subscriber->subscribeNowTo($anotherEmailList);
```
