<div class="min-h-screen w-full bg-[#e5e5e5] flex flex-col">

    <!-- Header -->
    <header class="relative bg-[#2E7D32] px-6 pt-8 pb-6 w-full">
        <h1 class="text-white text-2xl md:text-3xl font-semibold leading-tight">
            Eindstand
        </h1>
    </header>

    <!-- Content -->
    <section class="flex-1 w-full bg-white pt-8 px-4 pb-24 relative z-10">

        <div class="w-full bg-[#f5f5f5] rounded-2xl px-4 py-6 shadow-sm">

            <div class="text-xs">

                <x-leaderboard
                    :players="$leaderboardData"
                    :showContinueButton="true"
                >
                    <button
                        wire:click="showFeedbackForm"
                        class="mt-4 w-full bg-[#2E7D32] hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition shadow-md"
                    >
                        Verder
                    </button>
                </x-leaderboard>
            </div>

        </div>

    </section>

</div>
