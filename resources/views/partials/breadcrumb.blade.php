@props([
    'items' => [],
    'homeRoute' => 'dashboard',
    'homeText' => 'Dashboard',
])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route($homeRoute) }}">
                <i class="fas fa-home"></i>
                {{ $homeText }}
            </a>
        </li>
        
        @foreach($items as $item)
            @if(isset($item['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}">{{ $item['text'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $item['text'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>