<?php

namespace App\Http\Controllers;

use App\Support\Content;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function __construct(private readonly Content $content) {}

    public function home(): View
    {
        return view('pages.home', [
            'site' => $this->content->site(),
            'faq' => $this->content->faq(),
            'integrations' => $this->content->integrations(),
            'demo' => $this->content->demo(),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.legal', [
            'title' => 'Polityka prywatności',
            'html' => $this->content->legalHtml('polityka-prywatnosci'),
        ]);
    }

    public function terms(): View
    {
        return view('pages.legal', [
            'title' => 'Regulamin',
            'html' => $this->content->legalHtml('regulamin'),
        ]);
    }
}
