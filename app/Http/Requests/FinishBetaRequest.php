<?php

namespace App\Http\Requests;

use App\Support\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinishBetaRequest extends FormRequest
{
    public function rules(): array
    {
        $content = app(Content::class);

        return [
            'business_type' => ['required', Rule::in($content->businessTypeIds())],
            'business_type_other' => ['exclude_unless:business_type,inne', 'required', 'string', 'max:100'],
            'integrations' => ['required', 'array', 'min:1', 'max:'.config('beta.max_integrations')],
            'integrations.*' => ['string', 'distinct', Rule::in($content->integrationIds())],
            'missing_text' => ['nullable', 'string', 'max:300'],
            'spend_range' => ['nullable', Rule::in($content->spendRangeIds())],
            'consent_contact' => ['accepted'],
            'consent_marketing' => ['nullable', 'boolean'],
            'firma_www' => ['nullable', 'string', 'max:0'], // honeypot
        ];
    }

    public function attributes(): array
    {
        return [
            'business_type' => 'typ firmy',
            'business_type_other' => 'opis działalności',
            'integrations' => 'integracje',
            'integrations.*' => 'integracja',
            'missing_text' => 'opis',
            'spend_range' => 'wydatki',
            'consent_contact' => 'zgoda na kontakt',
            'consent_marketing' => 'zgoda marketingowa',
        ];
    }

    public function messages(): array
    {
        $max = config('beta.max_integrations');

        return [
            'business_type.required' => 'Wybierz, czym zajmuje się Twoja firma.',
            'business_type_other.required' => 'Napisz krótko, czym zajmuje się Twoja firma.',
            'integrations.required' => 'Zaznacz przynajmniej jedną integrację.',
            'integrations.min' => 'Zaznacz przynajmniej jedną integrację.',
            'integrations.max' => "Możesz zaznaczyć najwyżej {$max} integracje.",
            'consent_contact.accepted' => 'Zgoda na kontakt w sprawie bety jest wymagana.',
        ];
    }
}
