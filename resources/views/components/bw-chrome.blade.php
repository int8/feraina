@props(['tabs', 'host', 'hostId' => null, 'path' => '', 'pathId' => null])
<div class="bw-tabs" role="tablist" aria-label="Karty przeglądarki">
    @foreach ($tabs as $tab)
        <button type="button" class="bw-tab {{ ($tab['active'] ?? false) ? 'active' : '' }}" role="tab" data-tab="{{ $tab['id'] }}"
                aria-selected="{{ ($tab['active'] ?? false) ? 'true' : 'false' }}" @if ($tab['hidden'] ?? false) hidden @endif>
            @if (($tab['fav'] ?? 'udaina') === 'udaina')
                <img class="bw-fav" src="{{ asset('favicon.svg') }}" alt="">
            @else
                <span class="bw-fav bw-fav-dot" data-fav></span>
            @endif
            <span class="bw-title">{{ $tab['title'] }}@if (isset($tab['sub'])) · <span @if (isset($tab['subId'])) id="{{ $tab['subId'] }}" @endif>{{ $tab['sub'] }}</span>@endif</span>
            <svg class="bw-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7l10 10M17 7 7 17"/></svg>
        </button>
    @endforeach
    <svg class="bw-ico bw-plus" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
    <span class="bw-win" aria-hidden="true">
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="M5 12h14"/></svg>
        <svg class="bw-ico" viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="14"/></svg>
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </span>
</div>
<div class="bw-bar" aria-hidden="true">
    <span class="bw-nav">
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg>
        <svg class="bw-ico dim" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="M4 12a8 8 0 1 0 2.3-5.6"/><path d="M4 4v5h5"/></svg>
    </span>
    <div class="bw-omni">
        <svg class="bw-ico" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="1.5"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
        <span class="bw-url"><span class="host" @if ($hostId) id="{{ $hostId }}" @endif>{{ $host }}</span><span @if ($pathId) id="{{ $pathId }}" @endif>{{ $path }}</span></span>
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z"/></svg>
    </div>
    <span class="bw-tools">
        <svg class="bw-ico" viewBox="0 0 24 24"><path d="M10 4h4v3a2 2 0 0 0 4 0h2v4h-3a2 2 0 0 0 0 4h3v5H4V4h3a2 2 0 0 1 4 0z"/></svg>
        <span class="bw-avatar">K</span>
        <svg class="bw-ico" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.4"/><circle cx="12" cy="12" r="1.4"/><circle cx="12" cy="19" r="1.4"/></svg>
    </span>
</div>
