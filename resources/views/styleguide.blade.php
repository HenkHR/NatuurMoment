<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NatuurGame - Ontwikkelaar Instructies</title>
    <link rel="icon" type="image/png" href="{{ asset('images/website-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-surface-light text-deep-black p-8">
    <div class="max-w-3xl mx-auto space-y-8">

        <h1 class="text-h1 text-forest">NatuurGame Ontwikkelaar Instructies</h1>

        <!-- Font laden -->
        <section class="bg-pure-white rounded-card shadow-card p-6">
            <h2 class="text-h2 mb-4">1. Font laden</h2>
            <p class="mb-4">Voeg dit toe in de <code class="bg-surface-light px-2 py-1 rounded">&lt;head&gt;</code>:</p>
            <pre class="bg-deep-black text-pure-white p-4 rounded overflow-x-auto text-sm"><code>&lt;link rel="preconnect" href="https://fonts.bunny.net"&gt;
&lt;link href="https://fonts.bunny.net/css?family=lexend:400,500,700&display=swap" rel="stylesheet" /&gt;</code></pre>
        </section>

        <!-- Kleuren -->
        <section class="bg-pure-white rounded-card shadow-card p-6">
            <h2 class="text-h2 mb-4">2. Kleuren gebruiken</h2>
            <p class="mb-4">Gebruik <strong>geen hardcoded hex codes</strong>. Gebruik altijd Tailwind classes:</p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-light">
                        <tr>
                            <th class="text-left p-2">Kleur</th>
                            <th class="text-left p-2">Class prefix</th>
                            <th class="text-left p-2">Voorbeeld</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-surface-medium">
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-forest rounded mr-2"></span>Forest Green</td>
                            <td class="p-2"><code>forest</code></td>
                            <td class="p-2"><code>bg-forest</code>, <code>text-forest-700</code>, <code>border-forest-500</code></td>
                        </tr>
                        <tr class="border-b border-surface-medium">
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-action rounded mr-2"></span>Action Orange</td>
                            <td class="p-2"><code>action</code></td>
                            <td class="p-2"><code>bg-action</code>, <code>hover:bg-action-600</code></td>
                        </tr>
                        <tr class="border-b border-surface-medium">
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-sky rounded mr-2"></span>Sky Blue</td>
                            <td class="p-2"><code>sky</code></td>
                            <td class="p-2"><code>bg-sky</code>, <code>text-sky-400</code></td>
                        </tr>
                        <tr class="border-b border-surface-medium">
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-pure-white border rounded mr-2"></span>Pure White</td>
                            <td class="p-2"><code>pure-white</code></td>
                            <td class="p-2"><code>bg-pure-white</code>, <code>text-pure-white</code></td>
                        </tr>
                        <tr class="border-b border-surface-medium">
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-deep-black rounded mr-2"></span>Deep Black</td>
                            <td class="p-2"><code>deep-black</code></td>
                            <td class="p-2"><code>bg-deep-black</code>, <code>text-deep-black</code></td>
                        </tr>
                        <tr>
                            <td class="p-2"><span class="inline-block w-4 h-4 bg-surface-medium rounded mr-2"></span>Surface</td>
                            <td class="p-2"><code>surface-light</code> / <code>surface-medium</code></td>
                            <td class="p-2"><code>bg-surface-light</code>, <code>border-surface-medium</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-gray-600">Shades beschikbaar: 50, 100, 200, 300, 400, 500, 600, 700, 800, 900</p>
        </section>

        <!-- Typography -->
        <section class="bg-pure-white rounded-card shadow-card p-6">
            <h2 class="text-h2 mb-4">3. Typography</h2>
            <p class="mb-4">Gebruik deze classes voor tekst:</p>

            <div class="space-y-3">
                <div class="flex items-center gap-4 p-2 bg-surface-light rounded">
                    <code class="w-24">text-h1</code>
                    <span class="text-h1">Heading 1</span>
                </div>
                <div class="flex items-center gap-4 p-2 bg-surface-light rounded">
                    <code class="w-24">text-h2</code>
                    <span class="text-h2">Heading 2</span>
                </div>
                <div class="flex items-center gap-4 p-2 bg-surface-light rounded">
                    <code class="w-24">text-h3</code>
                    <span class="text-h3">Heading 3</span>
                </div>
                <div class="flex items-center gap-4 p-2 bg-surface-light rounded">
                    <code class="w-24">text-body</code>
                    <span class="text-body">Body tekst</span>
                </div>
                <div class="flex items-center gap-4 p-2 bg-surface-light rounded">
                    <code class="w-24">text-small</code>
                    <span class="text-small">Small tekst</span>
                </div>
            </div>
        </section>

        <!-- Border radius -->
        <section class="bg-pure-white rounded-card shadow-card p-6">
            <h2 class="text-h2 mb-4">4. Border Radius</h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-forest rounded-input mb-2"></div>
                    <code class="text-xs">rounded-input</code>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-forest rounded-card mb-2"></div>
                    <code class="text-xs">rounded-card</code>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-forest rounded-button mb-2"></div>
                    <code class="text-xs">rounded-button</code>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-forest rounded-badge mb-2"></div>
                    <code class="text-xs">rounded-badge</code>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-forest rounded-icon mb-2"></div>
                    <code class="text-xs">rounded-icon</code>
                </div>
            </div>
        </section>

        <!-- Shadows & Layout -->
        <section class="bg-pure-white rounded-card shadow-card p-6">
            <h2 class="text-h2 mb-4">5. Shadows & Layout</h2>

            <div class="space-y-2 text-sm">
                <p><code class="bg-surface-light px-2 py-1 rounded">shadow-card</code> - Standaard card shadow</p>
                <p><code class="bg-surface-light px-2 py-1 rounded">shadow-card-hover</code> - Hover state shadow</p>
                <p><code class="bg-surface-light px-2 py-1 rounded">max-w-container</code> - Max breedte 1200px</p>
            </div>
        </section>

        <!-- Quick Reference -->
        <section class="bg-forest text-pure-white rounded-card p-6">
            <h2 class="text-h2 mb-4">Quick Reference</h2>
            <pre class="text-sm overflow-x-auto"><code>{{-- Button --}}
&lt;button class="bg-action hover:bg-action-600 text-pure-white px-6 py-3 rounded-button"&gt;

{{-- Card --}}
&lt;div class="bg-pure-white rounded-card shadow-card p-6"&gt;

{{-- Input --}}
&lt;input class="border border-surface-medium rounded-input focus:border-action"&gt;

{{-- Badge --}}
&lt;span class="bg-action text-pure-white px-3 py-1 rounded-badge text-small"&gt;</code></pre>
        </section>

    </div>
</body>
</html>
