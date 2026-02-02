@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full text-start ps-3 pe-4 py-2 border-l-4 border-white text-base font-bold text-white bg-white/20 focus:outline-none transition duration-150 ease-in-out group'
            : 'block w-full text-start ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-white/90 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center">
        @if (isset($icon))
            <span class="flex-shrink-0 w-6 h-6 mr-3 transition-all duration-200">
                {{ $icon }}
            </span>
        @endif
        <span class="sidebar-text truncate transition-all duration-200">
            {{ $slot }}
        </span>
    </div>
</a>
