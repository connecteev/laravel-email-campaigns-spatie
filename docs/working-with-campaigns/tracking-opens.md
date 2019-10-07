---
title: Tracking opens
weight: 3
---

The package can track when and how many times a subscriber opens a campaign. 

## Enabling open tracking

To use this feature you must set `track_opens` to `true` of a campaign you're going to send. You can find an example of how to do this in the section on [how to create a campaign](https://docs.spatie.be/laravel-email-campaigns/v1/working-with-campaigns/creating-a-campaign/).

## How it works under the hood

When you send a campaign that has open tracking enabled we'll add a web beacon to it.  A web beacon is extra `img` tag in the html of your mail.  It's `src` attribute points to an endpoint that was added by the route macro, `Route::emailCampaigns` that you added when installing the package. 
 
 Here's how such a web beacon could look like:
 
```html
<img src="https://yourapp.com/email-campaigns/track-opens/xxxx-xxxx-xxxx-xxxx" />
```

The last segment of the link contains the uuid of an `EmailSend` model. An `EmailSend` represents a mail that has been sent for a campaign and contains a relation to a subscriber.
 
Each time an email client tries to display the web beacon it will send a get request to the `Spatie\EmailCampaigns\Http\Controllers\TrackOpensController`. 

## Queuing open tracking

When sending a campaign to a large list, that endpoint could get hit a lot in a short timespan. To ensure a very fast response time, we don't do any database updates in the controller itself. Instead, the controller will dispatch a `TrackOpenJob`. 

Because there potentially a great many of these jobs could be scheduled, we recommend using a separate queue for handling them. You can configure the queue to be used in the `perform_on_queue.register_open_job` key of the `email-campaigns` config file.

