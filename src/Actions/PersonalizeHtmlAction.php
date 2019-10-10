<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class PersonalizeHtmlAction
{
    public function execute($html, CampaignSend $pendingSend)
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        $subscription = $pendingSend->subscription;

        $html = str_replace('::campaignSendUuid::', $pendingSend->uuid, $html);
        $html = str_replace('::subscriptionUuid::', $subscription->uuid, $html);
        $html = str_replace('::subscriber.uuid::', $subscription->subscriber->uuid, $html);
        $html = str_replace('::unsubscribeUrl::', $subscription->unsubscribeUrl(), $html);

        $html = $this->replaceSubscriberAttributes($html, $subscription->subscriber);

        return $html;
    }

    protected function replaceSubscriberAttributes(string $html, Subscriber $subscriber): string
    {
        $html = preg_replace_callback('/::subscriber.([\w.]+)::/', function (array $match) use ($subscriber) {
            $parts = collect(explode('.', $match[1] ?? ''));

            $replace = $parts->reduce(function ($value, $part) {
                if ($value instanceof SchemalessAttributes) {
                    return $value->get($part);
                }

                return $value->$part
                    ?? $value[$part]
                    ?? null;
            }, $subscriber);

            return $replace ?? $match;
        }, $html);

        return $html;
    }
}
