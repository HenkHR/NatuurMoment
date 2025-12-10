<div>
    @if($showFeedback)
        <!-- Feedback Form -->
        <div class="p-4 pb-24">
            <div class="bg-[#2E7D32] text-white text-center py-6 rounded-t-lg -mx-4 -mt-4 mb-6">
                <h1 class="text-2xl font-bold">Bedankt voor het spelen!</h1>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">Wat vond je van het spel?</label>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach(range(1, 10) as $num)
                            <button
                                wire:click="setRating({{ $num }})"
                                class="w-10 h-10 rounded-lg font-bold text-white transition
                                    {{ $rating === $num ? 'ring-4 ring-yellow-400 scale-110' : '' }}
                                    {{ $num <= 2 ? 'bg-red-500 hover:bg-red-600' : '' }}
                                    {{ $num >= 3 && $num <= 4 ? 'bg-orange-500 hover:bg-orange-600' : '' }}
                                    {{ $num >= 5 && $num <= 6 ? 'bg-yellow-500 hover:bg-yellow-600' : '' }}
                                    {{ $num >= 7 && $num <= 8 ? 'bg-lime-500 hover:bg-lime-600' : '' }}
                                    {{ $num >= 9 ? 'bg-green-500 hover:bg-green-600' : '' }}">
                                {{ $num }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Hoe oud ben je?</label>
                    <input
                        type="text"
                        wire:model="age"
                        placeholder="Leeftijd..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <button
                    wire:click="submitFeedback"
                    class="w-full py-3 bg-[#0076A8] hover:bg-[#005a82] text-white font-semibold rounded-lg transition">
                    Bevestigen
                </button>
            </div>
        </div>
    @elseif($showLeaderboard)
        <!-- Leaderboard View -->
        <div class="p-4 pb-24">
            <x-leaderboard :players="$leaderboardData" :showContinueButton="true">
                <button
                    wire:click="showFeedbackForm"
                    class="inline-block px-6 py-3 bg-[#2E7D32] hover:bg-green-700 text-white rounded-lg font-semibold transition">
                    Verder
                </button>
            </x-leaderboard>
        </div>
    @else
        

        <!-- Flash message -->
        @if (session('photo-message'))
            <div class="absolute top-32 bg-sky-500 text-white px-4 py-2 rounded mb-4 mx-4">
                {{ session('photo-message') }}
            </div>
        @endif

        <!-- Bingo Card Grid -->
        @if(!$showCamera)
        <div class="container mx-auto px-4 absolute transform -translate-y-1/2 top-1/2 left-0 right-0 mt-10">
            <div wire:poll.5s.visible="refreshStatuses" class="grid grid-cols-3 gap-3 max-w-md mx-auto px-4 mt-6 mb-6 bg-[#e0e0e0] p-2 rounded-lg">
                @if(count($bingoItems) > 0)
                    @foreach ($bingoItems as $bingoItem)
                        @php
                            $status = $bingoItemStatuses[$bingoItem['id']] ?? null;
                            $isApproved = $status === 'approved';
                            $statusClass = match($status) {
                                'approved' => 'bg-green-100 border-green-500 border-2',
                                'rejected' => 'bg-red-100 border-red-500 border-2',
                                'pending' => 'bg-yellow-100 border-yellow-500 border-2',
                                default => 'bg-[#FFFFFF] border-[#e0e0e0]'
                            };
                            $statusIcon = match($status) {
                                'approved' => '✓',
                                'rejected' => '✕',
                                'pending' => '⏳',
                                default => ''
                            };
                        @endphp
                        <button
                            wire:key="player-bingo-item-{{ $bingoItem['id'] }}"
                            wire:click="openPhotoCapture({{ $bingoItem['id'] }})"
                            @if($isApproved) disabled @endif
                            class="{{ $statusClass }} rounded-lg shadow w-full aspect-square
                               text-green-700 font-semibold flex flex-col justify-center items-center text-center
                               focus:outline-none focus:ring-2 focus:ring-green-500 relative
                               {{ $isApproved ? 'opacity-75 cursor-not-allowed' : 'hover:bg-green-100 cursor-pointer' }}">
                            @if($statusIcon)
                                <span class="absolute top-1 right-1 text-lg">{{ $statusIcon }}</span>
                            @endif
                            <span class="text-xs">{{ $bingoItem['label'] }}</span>
                        </button>
                    @endforeach
                @else
                    <div class="col-span-3 text-center py-4">
                        <p class="text-gray-600">Geen bingo items gevonden voor deze game.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Camera View -->
        @if ($showCamera)
            <div class="fixed inset-0 z-50 bg-black flex flex-col" style="z-index: 9999;">
                <!-- Camera Container -->
                <div class="relative flex-1 flex items-center justify-center overflow-hidden bg-black" style="min-height: 0;">
                    <!-- Video Element (hidden when preview is shown) -->
                    <div class="w-full max-w-[500px] aspect-square mt-40 overflow-hidden {{ $capturedImage ? 'hidden' : '' }}">
                        <video
                            id="camera-video"
                            autoplay
                            playsinline
                            class="w-full h-full object-cover">
                        </video>
                    </div>

                    <!-- Canvas for capture -->
                    <canvas id="camera-canvas" class="hidden"></canvas>

                    <!-- Bingo Item Name (Top) -->
                    @if($bingoItemLabel)
                    <div class="absolute top-0 left-0 right-0 px-4 pt-6 pb-8 bg-[#2E7D32] z-30" style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%); z-index: 31;">
                        <div class="container mx-auto px-4 flex flex-col justify-between items-center">
                            <h1 class="text-3xl font-bold text-[#FFFFFF] mb-2 text-center">{{ $bingoItemLabel }}</h1>
                        </div>
                    </div>
                    @endif

                    <!-- Overlay Text (Fact) -->
                    @if($overlayText)
                        <div class="w-full bg-sky-700 h-24 absolute top-0 left-0 right-0 z-30" style="z-index: 30;">
                        </div>
                        <div class="absolute top-24 left-0 right-0 text-center z-30 pointer-events-none" style="z-index: 30;">
                            <div class="bg-sky-700 text-white inline-block text-sm w-full px-4 py-2">
                                {{ $overlayText }}
                            </div>
                        </div>
                    @endif

                    <!-- Preview Image (shown after capture) -->
                    @if($capturedImage)
                        <div class="absolute inset-0 flex items-center justify-center z-0 bg-black">
                            <img
                                id="preview-image"
                                src="{{ $capturedImage }}"
                                class="w-full max-w-[500px] aspect-square object-cover"
                                alt="Preview">
                        </div>
                    @endif
                </div>

                <!-- Controls -->
                <div id="camera-controls" class="bg-black bg-opacity-90 p-4 flex justify-center gap-4 z-30 relative flex-shrink-0" style="z-index: 30; display: flex !important; visibility: visible !important;">
                    @if(!$capturedImage)
                        <!-- Capture Button -->
                        <button
                            onclick="capturePhoto()"
                            class="bg-white rounded-full p-4 hover:bg-gray-200 transition relative z-40"
                            style="z-index: 40;">
                            <div class="w-16 h-16 border-4 border-gray-800 rounded-full"></div>
                        </button>
                    @else
                        <!-- Retake Button -->
                        <button
                            wire:click="retakePhoto"
                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg font-semibold relative z-40"
                            style="z-index: 40;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>

                        <!-- Submit Button -->  
                        <button
                            onclick="submitPhoto(event)"
                            class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg font-semibold relative z-40"
                            style="z-index: 40;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        @endif
                        
                    </div>
                    <!-- Close Button -->
                    <button
                        wire:click="closeCamera"
                        class="absolute top-6 right-4 bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg font-semibold z-40"
                        style="z-index: 40;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
            </div>
        @endif
    @endif
</div>

<script>
let stream = null;
let video = null;
let canvas = null;

document.addEventListener('livewire:init', () => {
    Livewire.on('open-camera', () => {
        startCamera();
        // Ensure controls are visible after Livewire update
        setTimeout(() => {
            ensureControlsVisible();
        }, 100);
    });

    Livewire.on('retake-photo', () => {
        startCamera();
        setTimeout(() => {
            ensureControlsVisible();
        }, 100);
    });

    Livewire.on('close-camera', () => {
        stopCamera();
    });

    // Ensure controls stay visible after Livewire updates
    Livewire.hook('morph.updated', () => {
        if (document.getElementById('camera-controls')) {
            ensureControlsVisible();
        }
    });
});

function ensureControlsVisible() {
    const controlsDiv = document.getElementById('camera-controls');
    if (controlsDiv) {
        controlsDiv.style.display = 'flex';
        controlsDiv.style.visibility = 'visible';
        controlsDiv.style.opacity = '1';
        controlsDiv.style.zIndex = '30';

        // Ensure all buttons are visible
        const buttons = controlsDiv.querySelectorAll('button');
        buttons.forEach(button => {
            button.style.visibility = 'visible';
            button.style.opacity = '1';
            button.style.zIndex = '40';
        });
    }
}

async function startCamera() {
    try {
        video = document.getElementById('camera-video');
        canvas = document.getElementById('camera-canvas');

        // Make sure video wrapper is visible
        if (video) {
            const videoWrapper = video.parentElement;
            if (videoWrapper && videoWrapper.classList.contains('aspect-square')) {
                videoWrapper.style.display = 'block';
            }
            video.style.display = 'block';
            video.style.visibility = 'visible';
        }

        // Hide preview image if visible
        const previewImg = document.getElementById('preview-image');
        if (previewImg) {
            previewImg.style.display = 'none';
        }

        // Ensure controls are visible
        const controlsDiv = document.getElementById('camera-controls');
        if (controlsDiv) {
            controlsDiv.style.display = 'flex';
            controlsDiv.style.visibility = 'visible';
            controlsDiv.style.opacity = '1';
            controlsDiv.style.zIndex = '30';
        }

        // Request camera access
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment', // Use back camera on mobile
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            }
        });

        video.srcObject = stream;
        video.play();
    } catch (err) {
        alert('Camera toegang geweigerd: ' + err.message);
        @this.closeCamera();
    }
}

async function capturePhoto() {
    if (!video || !canvas) return;

    // Calculate square dimensions (use the smaller dimension)
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;
    const size = Math.min(videoWidth, videoHeight);

    // Set canvas to square dimensions
    canvas.width = size;
    canvas.height = size;

    // Calculate source crop position (center the crop)
    const sourceX = (videoWidth - size) / 2;
    const sourceY = (videoHeight - size) / 2;

    // Draw video frame to canvas (cropped to square)
    const ctx = canvas.getContext('2d');
    ctx.drawImage(
        video,
        sourceX, sourceY, size, size,  // Source rectangle (crop from video)
        0, 0, size, size               // Destination rectangle (draw to canvas)
    );

    // Convert to base64
    const imageData = canvas.toDataURL('image/jpeg', 0.85);

    // Update Livewire component and wait for it to complete
    try {
        await @this.set('capturedImage', imageData);

        // Ensure preview image is visible (fallback if Livewire update is slow)
        const previewImg = document.getElementById('preview-image');
        if (previewImg) {
            previewImg.src = imageData;
            previewImg.style.display = 'block';
        }

        // Hide video wrapper (Livewire will handle this via $capturedImage, but this is a fallback)
        const videoWrapper = video ? video.parentElement : null;
        if (videoWrapper && videoWrapper.classList.contains('aspect-square')) {
            videoWrapper.style.display = 'none';
        }

        // Stop camera stream after property is updated
        stopCamera();
    } catch (error) {
        console.error('Error updating captured image:', error);
        alert('Fout bij het vastleggen van de foto. Probeer opnieuw.');
    }
}

function submitPhoto(event) {
    const imageData = @this.get('capturedImage');
    if (!imageData) {
        alert('Geen foto gevonden. Probeer opnieuw.');
        return;
    }

    // Show loading state
    const submitBtn = event ? event.target : document.querySelector('button[onclick*="submitPhoto"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Bezig...';
    }

    @this.savePhoto(imageData).then(() => {
        // Success - component will handle UI update
    }).catch((error) => {
        // Error handling
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
        const errorMessage = error.message || error.toString() || 'Onbekende fout';
        alert('Fout bij opslaan: ' + errorMessage);
    });
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    if (video) {
        video.srcObject = null;
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopCamera();
});
</script>
