<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EmailCampaigns\Enums\EmailCampaignStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EmailCampaigns\Exceptions\CampaignCouldNotBeSent;
use Spatie\EmailCampaigns\Exceptions\CampaignCouldNotBeUpdated;

class EmailCampaign extends Model
{
    protected $guarded = [];

    public $casts = [
        'track_opens' => 'bool',
        'track_clicks' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'send_to_number_of_subscribers' => 'integer',
        'sent_at' => 'timestamp',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (EmailCampaign $emailCampaign) {
            $emailCampaign->status = EmailCampaignStatus::CREATED;
        });
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CampaignLink::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailCampaignSend::class);
    }

    public function subject(string $subject)
    {
        $this->ensureUpdatable();

        $this->update(compact('subject'));

        return $this;
    }

    public function trackOpens(bool $bool = true)
    {
        $this->ensureUpdatable();

        $this->update(['track_opens' => $bool]);

        return $this;
    }

    public function trackClicks(bool $bool = true)
    {
        $this->ensureUpdatable();

        $this->update(['track_clicks' => $bool]);

        return $this;
    }

    public function to(EmailList $emailList)
    {
        $this->ensureUpdatable();

        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function send()
    {
        $this->ensureSendable();

        $this->markAsSending();

        dispatch(new SendCampaignJob($this, $this->emailList));

        return $this;
    }

    public function sendTo(EmailList $emailList)
    {
        return $this->to($emailList)->send();
    }

    protected function ensureSendable()
    {
        if ($this->status === EmailCampaignStatus::SENDING) {
            throw CampaignCouldNotBeSent::beingSent($this);
        }

        if ($this->status === EmailCampaignStatus::SENT) {
            throw CampaignCouldNotBeSent::alreadySent($this);
        }

        if (empty($this->subject)) {
            throw CampaignCouldNotBeSent::noSubjectSet($this);
        }

        if (is_null($this->emailList)) {
            throw CampaignCouldNotBeSent::noListSet($this);
        }
    }

    protected function ensureUpdatable(): void
    {
        if ($this->status === EmailCampaignStatus::SENDING) {
            throw CampaignCouldNotBeUpdated::beingSent($this);
        }

        if ($this->status === EmailCampaignStatus::SENT) {
            throw CampaignCouldNotBeSent::alreadySent($this);
        }
    }

    private function markAsSending()
    {
        $this->update(['status' => EmailCampaignStatus::SENDING]);

        return $this;
    }

    public function markAsSent()
    {
        $this->status = EmailCampaignStatus::SENT;

        $this->sent_at = now();

        $this->update([
            'status' => EmailCampaignStatus::SENT,
            'sent_at' => now(),
        ]);

        return $this;


    }


}
