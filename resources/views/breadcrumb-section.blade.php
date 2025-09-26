@props(['title', 'items' => []])

<div class="breadcrumb-section | padding-block-1000 flow" style="--flow-spacer:1em">
    <h3 class="fs-secondary-heading text-center">{{ $title }}</h3>
    <nav class="align-center justify-center" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach($items as $label => $url)
                @if($loop->last || $url === null)
                    <li class="breadcrumb-item active | label" aria-current="page">
                        {{ $label }}
                    </li>
                @else
                    <li class="breadcrumb-item | label">
                        <a href="{{ $url }}">{{ $label }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>