<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-action border border-transparent rounded-button font-semibold text-xs text-pure-white uppercase tracking-widest hover:bg-action-600 focus:bg-action-600 active:bg-action-700 focus:outline-none focus:ring-2 focus:ring-action focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
