---
title: Troubleshooting errors during sending
weight: 5
---

When sending a campaign, the package will create `CampaignSend` models for each mail that must be sent. A `CampaignSend` has a property `sent_at` that will have the date time of when an actual email has been sent. If that attribute is `null` the actual email has not yet been sent.

So you have some failure while sending, and the state of your queues has been lost, you should dispatch a `SendMailJob` for each `CampaignSend` that has `sent_at` set to `null`.

```php
CampaignSend::whereNull('sent_at')->each(function(CampaignSend $campaignSend) {
   dispatch(new SendMailJob($campaigSend);
}
```

You can run the above code by executing the `email-campaigns:retry-pending-sends` command.

Should, for any reason, two jobs for the same `CampaignSend` be scheduled, it is highly likely that only one mail will be sent. After a `SendMailJob` has sent an email it will update `sent_at` with the current timestamp. The job will not send a mail for a `SendMail` whose `sent_at` is not set to `null`.







```
