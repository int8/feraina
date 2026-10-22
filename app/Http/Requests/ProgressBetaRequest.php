<?php

namespace App\Http\Requests;

use App\Support\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Zapis częściowych odpowiedzi (po każdym kroku). Pola opcjonalne, ale jeśli są, muszą być poprawne. */
class ProgressBetaRequest extends FormRequest
{
    public function rules(): array
    {
        $content = app(Content::class);

        return [
            'last_step' => ['required', 'integer', 'between:1,5'],
            'business_type' => ['nullable', Rule::in($content->businessTypeIds())],
            'business_type_other' => ['nullable', 'string', 'max:100'],
            'integrations' => ['nullable', 'array'],
            'integrations.*' => ['string', 'distinct', Rule::in($content->integrationIds())],
            'missing_text' => ['nullable', 'string', 'max:300'],
            'spend_range' => ['nullable', Rule::in($content->spendRangeIds())],
        ];
    }
}
