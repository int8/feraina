<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartBetaRequest extends FormRequest
{
    protected $errorBag = 'beta';

    private bool $spam = false;

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'utm_source' => ['nullable', 'string', 'max:200'],
            'utm_medium' => ['nullable', 'string', 'max:200'],
            'utm_campaign' => ['nullable', 'string', 'max:200'],
            'utm_term' => ['nullable', 'string', 'max:200'],
            'utm_content' => ['nullable', 'string', 'max:200'],
            'referrer' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Honeypot wypełniony = bot. Nie zgłaszamy błędu, tylko udajemy sukces (kontroler). */
    public function isSpam(): bool
    {
        return $this->spam;
    }

    protected function prepareForValidation(): void
    {
        $this->spam = filled($this->input('firma_ww'.'w'));
        if ($this->spam) {
            // Bot nie dostaje błędu walidacji e-maila; kontroler przekieruje na stronę podziękowania.
            $this->merge(['email' => 'bot@invalid.local']);
        }
    }
}
