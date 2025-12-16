@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        previouslyFocused: null,

        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            return [...this.$refs.dialog.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'));
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },

        focusFirst() {
            const first = this.firstFocusable();
            if (first) first.focus();
            else this.$refs.dialog.focus(); // fallback als er geen focusables zijn
        },
        focusLast() {
            const last = this.lastFocusable();
            if (last) last.focus();
            else this.$refs.dialog.focus();
        },
    }"
    x-init="$watch('show', value => {
        if (value) {
            previouslyFocused = document.activeElement;
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => focusFirst(), 50)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
            if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
                setTimeout(() => previouslyFocused.focus(), 0);
            }
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"

    x-on:keydown.tab.prevent="
        if (!show) return;
        if ($event.shiftKey) focusLast();
        else focusFirst();
    "

    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center"
    style="display: {{ $show ? 'flex' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    >
        <div class="absolute inset-0 bg-deep-black opacity-50"></div>
    </div>

    <div
        x-ref="dialog"
        x-show="show"
        role="dialog"
        aria-modal="true"
        tabindex="-1"
        {{ $attributes->only(['aria-labelledby','aria-label','aria-describedby']) }}

        class="relative bg-pure-white rounded-card shadow-xl transform transition-all sm:w-full {{ $maxWidth }} mx-4 sm:mx-auto p-6"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>
