<?php

namespace Spatie\EmailCampaigns\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EmailCampaigns\Enums\CampaignStatus;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Jobs\SendTestMailJob;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\EmailCampaigns\Exceptions\CampaignCouldNotSent;
use Spatie\EmailCampaigns\Exceptions\CampaignCouldNotUpdate;
use Spatie\EmailCampaigns\Http\Controllers\CampaignWebviewController;

class Campaign extends Model
{
    use HasUuid;

    public $table = 'email_campaigns';

    protected $guarded = [];

    public $casts = [
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'send_to_number_of_subscribers' => 'integer',
        'sent_at' => 'datetime',
        'requires_double_opt_in' => 'boolean',
        'statistics_calculated_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Campaign $campaign) {
            $campaign->status = CampaignStatus::CREATED;
        });
    }

    public static function scopeSentBetween(Builder $query, Carbon $periodStart, Carbon $periodEnd): void
    {
        $query
            ->where('sent_at', '>=', $periodStart)
            ->where('sent_at', '<', $periodEnd);
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CampaignLink::class, 'email_campaign_id');
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(CampaignClick::class, CampaignLink::class, 'email_campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class, 'email_campaign_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(CampaignSend::class, 'email_campaign_id');
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

    public function content(string $html)
    {
        $this->ensureUpdatable();

        $this->update(compact('html'));

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
        if ($this->status === CampaignStatus::SENDING) {
            throw CampaignCouldNotSent::beingSent($this);
        }

        if ($this->status === CampaignStatus::SENT) {
            throw CampaignCouldNotSent::alreadySent($this);
        }

        if (empty($this->subject)) {
            throw CampaignCouldNotSent::noSubjectSet($this);
        }

        if (is_null($this->emailList)) {
            throw CampaignCouldNotSent::noListSet($this);
        }

        if (empty($this->html)) {
            throw CampaignCouldNotSent::noContent($this);
        }
    }

    protected function ensureUpdatable(): void
    {
        if ($this->status === CampaignStatus::SENDING) {
            throw CampaignCouldNotUpdate::beingSent($this);
        }

        if ($this->status === CampaignStatus::SENT) {
            throw CampaignCouldNotSent::alreadySent($this);
        }
    }

    private function markAsSending()
    {
        $this->update(['status' => CampaignStatus::SENDING]);

        return $this;
    }

    public function markAsSent(int $numberOfSubscribers)
    {
        $this->update([
            'status' => CampaignStatus::SENT,
            'sent_at' => now(),
            'statistics_calculated_at' => now(),
            'sent_to_number_of_subscribers' => $numberOfSubscribers,
        ]);

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return $this->status === CampaignStatus::SENT;
    }

    /**
     * @param $email string|array|\Illuminate\Support\Collection
     */
    public function sendTestMail($emails)
    {
        collect($emails)->each(function (string $email) {
            dispatch(new SendTestMailJob($this, $email));
        });
    }

    public function webViewUrl(): string
    {
        return url(action(CampaignWebviewController::class, $this->uuid));
    }
}
