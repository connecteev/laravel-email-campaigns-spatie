---
title: Viewing statistics of a sent campaign
weight: 6
---

After a campaign is sent, some statistics will be made available.

## Available statistics 
 
 ### On a campaign
 
The scheduled 'email-campaigns:calculate-statistics' will fill these attributes on the `Campaign` model:

- `sent_to_number_of_subscribers`
- `open_count`: this is the total number of times your campaign was opened. Multiple opens by a single subscribers will each be counted.
- `unique_open_count`: contains the number of subscribers that opened your campaign.
- `open_rate`: contains the `unique_open_count` divided by the `sent_to_number_of_subscribers`. The result is multiplied by 100. The maximum value for this attribute is 100, the minimum is 0.
- `click_count`: contains the total number of times the links in your campaign were clicked. Multiple clicks on the same link by a subscriber will each be counted.
- `unique_click_count`: contains the number of subscribers have clicked any of the links in your campaign
- `click_rate`: contains the `unique_click_count`divided by the `sent_to_number_of_subscribers`. The result is multiplied by 100. The maximum value for this attribute is 100, the minimum is 0.

You can also get the opens and clicks of a campaign. Here's an example where we use the `opens` relation to retrieve who first opened the mail.

```php
$open = $campaign->opens->first();
$email = $open->subscriber->email;
```

## On a campaign link

If you enabled click tracking then for each link your campaign contains, a `CampainLink` will have been created. 

It contains these two attributes that hold statistical data:

- `click_count`: contains the total number of times this link was clicked. Multiple clicks on the same link by a subscriber will each be counted.
- `unique_click_count`: contains the number of subscribers have clicked this link

To know who clicked which link you can use the relations on `CampaignLink` model. Here's an example where we get the email of the subscriber who first clicked the first link of a campaign.

```
$campaignLink = $campaign->links->first();
$campaignClick = $campaignLink->links->first();
$email = $campaingClick->subscriber->email;
```

## When are statistics calculated

The statistics are calculated by the scheduled `email-campaigns:calculate-statistics`. This job will recalculate statistics:
- each minute for campaigns that were sent between 0 and 5 minutes ago
- every 10 minutes for campaigns that were send between 5 minutes and two hours ago
- every hour for campaigns that were sent between two hours and a day
- every four hours for campaigns that were sent between a day and two week ago

After two weeks no statistics are calculated anymore. 

## Manually recalculate statistics

To manually trigger a recalculation, execute the command with a campaign id as a parameter.

```bash
php artisan email-campaigns:calculate-statistics <campaign-id>
```

