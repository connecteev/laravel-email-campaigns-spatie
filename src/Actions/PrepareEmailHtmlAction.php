<?php

namespace Spatie\EmailCampaigns\Actions;

use DOMElement;
use DOMDocument;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Models\Campaign;

class PrepareEmailHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $campaign->email_html = $campaign->html;

        if ($campaign->track_clicks) {
            $this->trackClicks($campaign);
        }

        $campaign->save();
    }

    protected function trackClicks(Campaign $campaign)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadHTML($campaign->email_html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NOWARNING);

        collect($dom->getElementsByTagName('a'))
            ->filter(function (DOMElement $linkElement) {
                return Str::startsWith(
                    $linkElement->getAttribute('href'),
                    ['http://', 'https://']
                );
            })
            ->each(function (DOMElement $linkElement) use ($campaign) {
                $originalHref = $linkElement->getAttribute('href');

                $campaignLink = $campaign->links()->create([
                    'original_link' => $originalHref,
                ]);

                $linkElement->setAttribute('href', $campaignLink->url);
            });

        $campaign->update(['email_html' => $dom->saveHtml()]);
    }
}
