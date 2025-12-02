@props([
    'rules' => [],
    'buttonLabel' => 'Spel spelen',
])

<section {{ $attributes->merge(['class' => 'bg-pure-white rounded-card shadow-card overflow-hidden']) }}>
    <div class="divide-y divide-forest-700/10">
        @foreach($rules as $index => $rule)
            <div class="
                px-4 py-4 text-small text-pure-white
                @if($index === 0) bg-forest-700
                @elseif($index === 1) bg-forest-500
                @elseif($index === 2) bg-forest-600
                @else bg-forest-400
                @endif
            ">
                {{ $rule }}
            </div>
        @endforeach
    </div>

    <div class="px-4 py-5 flex justify-center">
        <button
            type="button"
            class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button shadow-card transition"
        >
            {{ $buttonLabel }}
        </button>
    </div>
</section>
