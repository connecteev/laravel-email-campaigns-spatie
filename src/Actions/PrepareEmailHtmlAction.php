<?php

namespace Spatie\EmailCampaigns\Actions;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Models\EmailCampaign;

class PrepareEmailHtmlAction
{
    public function execute(EmailCampaign $emailCampaign)
    {
        $emailCampaign->email_html = $emailCampaign->html;

        if ($emailCampaign->track_clicks) {
            $this->trackClicks($emailCampaign);
        }

        $emailCampaign->save();
    }

    protected function trackClicks(EmailCampaign $emailCampaign)
    {
        $dom = new DOMDocument();

        $dom->loadHTML($emailCampaign->email_html);

        collect($dom->getElementsByTagName('a'))
            ->filter(function (DOMElement $linkElement) {
                return Str::startsWith(
                    $linkElement->getAttribute('href'),
                    ['http://', 'https://']
                );
            })
            ->each(function(DOMElement $linkElement) use ($emailCampaign) {
                $originalHref = $linkElement->getAttribute('href');

                $campaignLink = $emailCampaign->links()->create([
                    'original_link' => $originalHref,
                ]);

                $linkElement->setAttribute('href', $campaignLink->link);
            });

        $this->email_html = $dom->saveHtml();
    }
}

