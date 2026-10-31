@props(['id', 'label' => false, 'class' => 'brand-ico'])
{{-- Miniaturka marki (content/brands.json). label=true dopisuje nazwę; class zastępuje domyślne brand-ico. --}}
@if ($label)
    <span class="brand">{!! \App\Support\Brands::icon($id, $class) !!}{{ \App\Support\Brands::all()[$id]['name'] ?? $id }}</span>
@else
    {!! \App\Support\Brands::icon($id, $class) !!}
@endif
