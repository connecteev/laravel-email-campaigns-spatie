<?php

namespace Spatie\EmailCampaigns\Mails;

use Illuminate\Mail\Mailable;

class CampaignMail extends Mailable
{
    /** @var string */
    public $content;

    /** @var string */
    public $subject;

    public function __construct(string $subject, string $content)
    {
        $this->content = $content;

        $this->subject = $subject;
    }

    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('email-campaigns::mails.campaign');
    }
}
