@props(['site' => null, 'title' => null])
@include('layouts.app', ['site' => $site ?? app(\App\Support\Content::class)->site(), 'title' => $title, 'slot' => $slot])
