---
title: Creating a campaign
weight: 1
---

To send a mail to all subscribers of your list you must create campaign, which can be sent to all subscribers of a list.

A campaign can be created like this:

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

If there is no way for subscriber to unsubscribe, it will result in a lot of frustration on the part of the subscriber. We recommend to always use the `@@unsubscribeUrl@@` in the html of each campaign you send.
