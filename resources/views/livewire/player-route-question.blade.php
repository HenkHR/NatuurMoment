<div class="space-y-6">
    {{-- Progress indicator --}}
    <div class="flex gap-1.5">
        @foreach($allQuestions as $question)
            <div class="flex-1 h-2 rounded-full {{
                $question->isAnsweredBy($playerId)
                    ? 'bg-green-500'
                    : ($currentQuestion && $question->id === $currentQuestion->id ? 'bg-blue-500' : 'bg-gray-300')
            }}"></div>
        @endforeach
    </div>

    {{-- Progress text --}}
    <p class="text-sm text-gray-600 text-center">
        {{ $answeredCount }} van {{ $totalQuestions }} vragen beantwoord
    </p>

    @if($currentQuestion)
        {{-- Current question card --}}
        <div
            x-data="{
                showFeedback: false,
                feedbackTimeout: null,
                selectedOption: @entangle('selectedOption')
            }"
            x-on:answer-submitted.window="
                showFeedback = true;
                clearTimeout(feedbackTimeout);
                feedbackTimeout = setTimeout(() => {
                    showFeedback = false;
                    $wire.clearFeedback();
                }, 2000);
            "
            class="bg-white rounded-lg shadow-lg p-6"
        >
            {{-- Question header --}}
            <div class="flex items-start justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    Vraag {{ $currentQuestion->sequence }}
                </h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    {{ $currentQuestion->points }} {{ $currentQuestion->points === 1 ? 'punt' : 'punten' }}
                </span>
            </div>

            {{-- Question text --}}
            <p class="text-lg text-gray-700 mb-6">{{ $currentQuestion->question_text }}</p>

            {{-- REQ-006: Feedback indicator (groen/rood) --}}
            @if($feedbackMessage)
                <div
                    x-show="showFeedback"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="mb-4 p-4 rounded-lg flex items-center gap-3 {{ $feedbackType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                >
                    @if($feedbackType === 'success')
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="font-medium">{{ $feedbackMessage }}</span>
                </div>
            @endif

            {{-- REQ-002 & REQ-008: Answer form with only available options --}}
            @if(!$feedbackMessage || $feedbackType !== 'success')
                <form wire:submit.prevent="submitAnswer({{ $currentQuestion->id }})" class="space-y-3">
                    @foreach($currentQuestion->getAvailableOptions() as $optionKey => $optionText)
                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                {{ $selectedOption === $optionKey ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}"
                        >
                            <input
                                type="radio"
                                wire:model="selectedOption"
                                value="{{ $optionKey }}"
                                name="answer_{{ $currentQuestion->id }}"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-3 text-lg text-gray-700">{{ $optionText }}</span>
                        </label>
                    @endforeach

                    @error('selectedOption')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror

                    {{-- Submit button with wire:loading --}}
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        x-bind:disabled="!selectedOption"
                        class="w-full mt-4 py-3 bg-blue-600 text-white rounded-lg font-semibold
                               disabled:opacity-50 disabled:cursor-not-allowed
                               hover:bg-blue-700 transition-colors"
                    >
                        <span wire:loading.remove>Bevestig antwoord</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Bezig...
                        </span>
                    </button>
                </form>
            @endif
        </div>
    @else
        {{-- All questions completed - REQ-011: Auto-redirect after showing feedback --}}
        <div
            class="bg-white rounded-lg shadow-lg p-8 text-center"
            x-data="{ redirecting: false }"
            x-init="
                setTimeout(() => {
                    redirecting = true;
                    $wire.clearFeedback();
                }, 2000);
            "
        >
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-green-600 mb-2">
                Alle vragen beantwoord!
            </h2>
            <p class="text-gray-600">
                Je hebt alle {{ $totalQuestions }} vragen beantwoord.
            </p>
            <p x-show="redirecting" class="text-sm text-gray-500 mt-2">
                Doorsturen naar bingo...
            </p>
        </div>
    @endif
</div>
