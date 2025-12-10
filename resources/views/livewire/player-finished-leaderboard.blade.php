<div class="p-4 pb-24">
    <div class="bg-[#2E7D32] text-white text-center py-6 rounded-t-lg -mx-4 -mt-4 mb-6">
        <h1 class="text-2xl font-bold">Eindstand</h1>
    </div>

    <div class="max-w-md mx-auto">
        <x-leaderboard :players="$leaderboardData" :showContinueButton="true">
            <button
                wire:click="showFeedbackForm"
                class="inline-block px-6 py-3 bg-[#2E7D32] hover:bg-green-700 text-white rounded-lg font-semibold transition">
                Verder
            </button>
        </x-leaderboard>
    </div>
</div>

