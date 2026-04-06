@props(['rating', 'showText' => true])
@php $r = (float)($rating ?? 0); @endphp

<div class="flex items-center gap-1">
    @for ($i = 1; $i <= 5; $i++)
        @if ($r >= $i)
            <span class="text-amber-400">★</span>
        @elseif ($r >= $i - 0.5)
            <span class="text-amber-400">☆</span>
        @else
            <span class="text-gray-300">☆</span>
        @endif
    @endfor
    @if ($showText)
        <span class="ml-1 text-sm text-gray-600">{{ number_format($r, 1) }}</span>
    @endif
</div>
