<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-pure-white border border-surface-medium rounded-button font-semibold text-xs text-deep-black uppercase tracking-widest shadow-sm hover:bg-surface-light focus:outline-none focus:ring-1 focus:ring-action focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
