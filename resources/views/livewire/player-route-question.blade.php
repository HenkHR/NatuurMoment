<div class="space-y-6">
    @if($currentQuestion)
        {{-- Progress text --}}
        <p class="text-sm text-gray-600 text-center">
            Vraag {{ $answeredCount + 1 }} van {{ $totalQuestions }}
        </p>

        {{-- Current question card --}}
        <div
            x-data="{ selectedOption: @entangle('selectedOption') }"
            x-on:answer-submitted.window="setTimeout(() => $wire.clearFeedback(), 2000)"
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
            <p class="text-lg text-gray-700 mb-4">{{ $currentQuestion->question_text }}</p>

            {{-- Question image (if available) --}}
            @if($currentQuestion->image_path)
                <div class="mb-6">
                    <img
                        src="{{ Storage::url($currentQuestion->image_path) }}"
                        alt="Vraag afbeelding"
                        class="w-full max-w-md mx-auto rounded-lg shadow-md"
                    >
                </div>
            @endif

            {{-- REQ-002 & REQ-008: Answer options --}}
            {{-- REQ-006: Inline feedback on answered option (groen/rood border + background) --}}
            <div class="space-y-3">
                @foreach($currentQuestion->getAvailableOptions() as $optionKey => $optionText)
                    @if(!$answeredOption)
                        {{-- Selectable option (before answer) --}}
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
                    @else
                        {{-- Feedback option (after answer) --}}
                        <div
                            class="flex items-center p-4 border-2 rounded-lg transition-all
                                @if($answeredOption === $optionKey)
                                    {{ $feedbackType === 'success' ? 'border-green-500 bg-green-100' : 'border-red-500 bg-red-100' }}
                                @else
                                    border-gray-200 bg-gray-50 opacity-60
                                @endif"
                        >
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                                @if($answeredOption === $optionKey)
                                    {{ $feedbackType === 'success' ? 'border-green-500 bg-green-500' : 'border-red-500 bg-red-500' }}
                                @else
                                    border-gray-300
                                @endif">
                                @if($answeredOption === $optionKey)
                                    @if($feedbackType === 'success')
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                            <span class="ml-3 text-lg {{ $answeredOption === $optionKey ? ($feedbackType === 'success' ? 'text-green-800 font-medium' : 'text-red-800 font-medium') : 'text-gray-500' }}">
                                {{ $optionText }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>

            @error('selectedOption')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror

            {{-- Submit button - always visible, changes state based on answeredOption --}}
            <button
                type="button"
                wire:click="submitAnswer({{ $currentQuestion->id }})"
                wire:loading.attr="disabled"
                @if($answeredOption)
                    disabled
                @else
                    x-bind:disabled="!selectedOption"
                @endif
                class="w-full mt-4 py-3 rounded-lg font-semibold transition-colors
                    @if($answeredOption)
                        {{ $feedbackType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}
                    @else
                        bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                    @endif"
            >
                <span class="inline-flex items-center justify-center gap-2">
                    @if($answeredOption)
                        @if($feedbackType === 'success')
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Correct!
                        @else
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Helaas, fout
                        @endif
                    @else
                        <span wire:loading.remove wire:target="submitAnswer">Bevestig antwoord</span>
                        <span wire:loading wire:target="submitAnswer">
                            <svg class="animate-spin h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Bezig...
                        </span>
                    @endif
                </span>
            </button>
        </div>
    @else
        {{-- All questions completed --}}
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Je hebt alle vragen al beantwoord
            </h2>
        </div>
    @endif
</div>
