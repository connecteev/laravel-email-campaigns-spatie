---
title: Throttling sends
weight: 2
---

Most email providers have a limit on how many mails you can send within a given amount of time. By default only 5 mails per second will get send. In the config file you can customize this behaviour in the `throttling` key:

```php
'throttling' => [
    'enabled' => false,
    'redis_connection_name' => 'default',
    'redis_key' => 'laravel-email-campaigns',
    'allowed_number_of_jobs_in_timespan' => 5,
    'timespan_in_seconds' => 1,
    'release_in_seconds' => 5,
],
```
