<div class="h-screen w-full bg-white flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="w-full px-4 pt-6 pb-8 bg-forest-700 flex-shrink-0"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <div class="container max-w-md mx-auto px-4 flex flex-col justify-between relative">
            <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Eindstand</h1>
        </div>
    </div>

    <!-- Content -->
    <section class="flex-1 w-full pt-8 px-4 pb-4 relative z-10 overflow-hidden min-h-0">

        <div class="w-full rounded-2xl shadow-sm max-w-md mx-auto h-full flex flex-col">

            <div class="flex-1 overflow-hidden min-h-0">
                <x-leaderboard
                    :players="$leaderboardData"
                    :showContinueButton="false"
                    :isFinished="true"
                    class="h-full flex flex-col"
                />
            </div>

        </div>

    </section>

    <!-- Fixed Continue Button at Bottom -->
    <div class="flex-shrink-0 bg-white pt-4 pb-4 px-4 z-50 shadow-lg mt-4">
        <div class="max-w-md mx-auto">
            <button
                wire:click="showFeedbackForm"
                class="w-full bg-forest-700 hover:bg-forest-600 text-white py-3 rounded-lg font-semibold transition shadow-md"
            >
                Verder
            </button>
        </div>
    </div>
</div>
