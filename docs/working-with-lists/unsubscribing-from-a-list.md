---
title: Unsubscribing from a list
weight: 3
---

You can unsubscribe someone like this

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
