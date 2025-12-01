<div class="max-w-md mx-auto p-4 flex flex-col justify-between min-h-screen">
    {{ $header ?? '' }}

    <main class="flex-grow mt-4">
        {{ $slot }}
    </main>

    {{ $footer ?? '' }}
</div>
