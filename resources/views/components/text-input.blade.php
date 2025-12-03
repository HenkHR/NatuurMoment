@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-surface-medium bg-pure-white text-deep-black focus:border-action-400 focus:ring-1 focus:ring-action-400 rounded-input shadow-sm']) }}>
