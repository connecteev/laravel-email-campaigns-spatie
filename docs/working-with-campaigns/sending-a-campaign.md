---
title: Sending a campaign
weight: 1
---

To send a mail to all subscribers of your list you must create and send a campaign.

## Creating a campaign

A campaign can be created list this:

```php
Campaign::create()
    ->subject('My newsletter #1') 
    ->content($html)
    ->trackOpens()
    ->trackClicks()
    ->to($emailList);
```

The `trackOpens` and `trackClicks` calls are optional.

Alternatively you could manually set the attributes on a `Campaign` model.

```php
Campaign::create([
   'subject' => 'My newsletter #1',
   'content' => $html,
   'track_opens' => true,
   'track_clicks' => true,
   'email_list_id' => $emailList->id,
]);
```

## Setting the content and using placeholders

You can send the content of a campaign by setting it's `html` attribute.

```php
$campaign->html = $yourHtml;
$campaign->save();
```

In that html you can use these placeholders which will be replaced when sending out the campaign:

- `@@unsubscribeUrl@@`: this string will be replaced with the url that, when hit, will immediately unsubscribe the person that clicked it
- `@@webviewUrl`: this string will be replaced with a the url that will display the content of your campaign.

## Sending a campaign

Before sending a campaign the `subject`, `html` and `email_list_id` attributes must be set.

A campaign can be sent with the  `send` method.

```php
$campaign->send();
```

Alternatively you can set the email list and send the campaign in one go:

```php
$campaign->sendTo($emailList);
```

## What happens when a campaign is being sent

When you send a campaign a job called `SendCampaign` job will be dispatched. This job will create a `MailSend` model for each of the subscribers of the list your're sending the campaign to. A `MailSend` represent a mail that should be send to one subscriber. 

For each created `SendMail` model, a `SendMailJob` will be started. That job will send that actual mail. After the job has sent the mail, it will mark the `SendMail` as sent, by filling `sent_at` with the current timestamp. 
 
 You can customize on which queue the `SendCampaignJob` and `SendMailJob` jobs are dispatch  is dispatch in the `perform_on_queue` in the `email-campaigns` config file. We recommend the `SendMailJob` having its own queue because it could contain many pending jobs if you are send a campaign to a large list of subscribers.
 
 To not overwhelm your email service with a large amount of calls, the `SendMailJob` is also throttled by default. Only 5 of those jobs will be handle in the timespan of a second. To learn more about this, read [the docs on throttling sends](TODO: addlink).

