---
title: Subscribing to a list
weight: 2
---

You can use a string to subscribe to an email list.

```php
$emailList->subscribe('john@example.com');
```

Behind the scenes, we'll create a `Subscriber` with email `john@example.com` and a `Subscription`, which is the relation between a subscriber and a list. 

If you subscribe an email twice to the same list, only one subscription will be created.

You can also use a `Subscriber` to subscribe to an email list.

```php
$subscriber = Subscriber::findForEmail('john@example.com');

$subscriber->subscribeTo($anotherEmailList);
```

## Checking if someone is subscribed

You can check if a given email is subscribed to an email list.

```php
$emailList->isSubscribed('john@example.com'); // returns a boolean
```

You can use a subscriber to check to if it is subscriber to a list.

```php
$subscriber->isSubscribedTo($emailList) // returns a boolean;
```

## Getting all subscribers of an email list

To get all subscribers of an email list you can use the `emailList` you can call `subscribers` on an email list.

```php
$subscribers = $emailList->subscribers; // returns all subscriber
```

To get the email address of a subscriber call `email` on a subscriber.

```php
$email = $subscribers->first()->email;
```

Calling `subscribers` on an email list will only return subscribers that have a subscription with a `subscribed` status. Subscribers that have unsubscribed or are still pending (in case you use [double opt in](TODO:add link to double opt-in)) will not be returned.

To return all subscribers, including all pending and unsubscribed ones, use `allSubscribers`.

```php
$allSubscribers = $emailList->allSubscribers;
```
