---
title: Using double opt in
weight: 3
---

To ensure that all subscribers of your email list really wanted to subscribe you can enable the double opt in requirement. 

```php
EmailList::create([
    'name' => 'My list'
    'requires_double_opt_in' => true',
]);
```

When calling `subscribe` on a list where `requires_double_opt_in` is enabled, a subscription will be created with a `status` set to `pending`. An email will be sent to to the email address you're subscribing. The email contains a link that, when clicked, will confirm the subscription. When a subscription is confirmed, its status will be set to `subscribed`.

When sending a campaign to an email list only subscribers that have a subscription with status `subscribed` will receive the campaign.

## Customizing the double opt in mail

You can customize the content of the double opt in mail.

First, you must publish the views.

```bash
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="views"
```

After that, the content of the double opt in mail can be modify the `/resources/views/vendor/email-campaigns/mails/confirmSubscription.blade.php`.
