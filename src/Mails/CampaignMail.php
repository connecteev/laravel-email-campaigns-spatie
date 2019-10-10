<?php

namespace Spatie\EmailCampaigns\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\EmailCampaigns\Models\CampaignSend;

class CampaignMail extends Mailable
{
    use SerializesModels;

    /** @var string */
    public $content;

    /** @var string */
    public $subject;

    /** @var \Spatie\EmailCampaigns\Models\CampaignSend */
    public $campaignSend;

    public function __construct(string $subject, string $content, CampaignSend $campaignSend = null)
    {
        $this->content = $content;

        $this->subject = $subject;

        $this->campaignSend = $campaignSend;
    }

    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('email-campaigns::mails.campaign')
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()
                        ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $message->getHeaders()
                        ->addTextHeader('List-Unsubscribe', '<' . secure_url('unsubscribe/' . $this->campaignSend->subscription->uuid) . '>');
            });
    }
}
