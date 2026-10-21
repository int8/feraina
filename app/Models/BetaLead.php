<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BetaLead extends Model
{
    public const STATUS_INCOMPLETE = 'niedokonczony';

    public const STATUS_COMPLETE = 'kompletny';

    public const STATUS_CONFIRMED = 'potwierdzony';

    public const STATUS_UNSUBSCRIBED = 'wypisany';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'integrations' => 'array',
            'consent_contact_at' => 'datetime',
            'consent_marketing_at' => 'datetime',
            'completed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public static function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public function isOnList(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETE, self::STATUS_CONFIRMED], true);
    }

    /** Zgłoszenia liczone w zestawieniach (kompletne lub potwierdzone). */
    public function scopeCounted(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_COMPLETE, self::STATUS_CONFIRMED]);
    }

    public function ensureTokens(): void
    {
        $this->confirm_token ??= Str::random(48);
        $this->unsubscribe_token ??= Str::random(48);
    }

    /** Wypisanie = anonimizacja: usuwamy dane osobowe, zostawiamy odpowiedzi do statystyk. */
    public function anonymize(): void
    {
        $this->forceFill([
            'email' => null,
            'business_type_other' => null,
            'missing_text' => null,
            'confirm_token' => null,
            'unsubscribe_token' => null,
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ])->save();
    }
}
