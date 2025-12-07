<div>
    <!-- Flash message -->
    @if (session('photo-message'))
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
            {{ session('photo-message') }}
        </div>
    @endif

    <!-- Bingo Card Grid -->
    @if(!$showCamera)
        <div wire:poll.5s="refreshStatuses" class="grid grid-cols-3 gap-3 max-w-md mx-auto px-4 mt-6 mb-6 bg-[#e0e0e0] p-2 rounded-lg">
            @if(count($bingoItems) > 0)
                @foreach ($bingoItems as $bingoItem)
                    @php
                        $status = $bingoItemStatuses[$bingoItem->id] ?? null;
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
                        wire:click="openPhotoCapture({{ $bingoItem->id }})"
                        @if($isApproved) disabled @endif
                        class="{{ $statusClass }} rounded-lg shadow w-28 h-28
                           text-green-700 font-semibold flex flex-col justify-center items-center text-center
                           focus:outline-none focus:ring-2 focus:ring-green-500 relative
                           {{ $isApproved ? 'opacity-75 cursor-not-allowed' : 'hover:bg-green-100 cursor-pointer' }}">
                        @if($statusIcon)
                            <span class="absolute top-1 right-1 text-lg">{{ $statusIcon }}</span>
                        @endif
                        <span class="text-sm">{{ $bingoItem->label }}</span>
                    </button>
                @endforeach
            @else
                <div class="col-span-3 text-center py-4">
                    <p class="text-gray-600">Geen bingo items gevonden voor deze game.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Camera View -->
    @if ($showCamera)
        <div class="fixed inset-0 z-50 bg-black flex flex-col" style="z-index: 9999;">
            <!-- Camera Container -->
            <div class="relative flex-1 flex items-center justify-center overflow-hidden" style="min-height: 0;">
                <!-- Video Element (hidden when preview is shown) -->
                <video 
                    id="camera-video" 
                    autoplay 
                    playsinline
                    class="w-full h-full object-cover {{ $capturedImage ? 'hidden' : '' }}">
                </video>
                
                <!-- Canvas for capture -->
                <canvas id="camera-canvas" class="hidden"></canvas>

                <!-- Bingo Item Name (Top) -->
                @if($bingoItemLabel)
                    <div class="absolute top-4 left-0 right-0 text-center z-20 pointer-events-none">
                        <div class="bg-[#2E7D32] text-white px-6 py-3 rounded-lg inline-block text-xl font-bold max-w-[90%] shadow-lg">
                            {{ $bingoItemLabel }}
                        </div>
                    </div>
                @endif

                <!-- Overlay Text (Fact) -->
                @if($overlayText)
                    <div class="absolute top-24 left-0 right-0 text-center z-20 pointer-events-none">
                        <div class="bg-black bg-opacity-70 text-white px-4 py-2 rounded-lg inline-block text-lg font-bold max-w-[90%]">
                            {{ $overlayText }}
                        </div>
                    </div>
                @endif
                
                <!-- Preview Image (shown after capture) -->
                @if($capturedImage)
                    <img 
                        id="preview-image" 
                        src="{{ $capturedImage }}" 
                        class="absolute inset-0 w-full h-full object-cover z-0"
                        alt="Preview">
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
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold relative z-40"
                        style="z-index: 40;">
                        🔄 Opnieuw
                    </button>
                    
                    <!-- Submit Button -->
                    <button 
                        onclick="submitPhoto(event)"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold relative z-40"
                        style="z-index: 40;">
                        ✓ Verzenden
                    </button>
                @endif
                
                <!-- Close Button -->
                <button 
                    wire:click="closeCamera"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold relative z-40"
                    style="z-index: 40;">
                    ✕ Sluiten
                </button>
            </div>
        </div>
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
        
        // Make sure video is visible
        if (video) {
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
    
    // Get video dimensions
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;
    
    // Set canvas dimensions to match video (ensure correct orientation)
    canvas.width = videoWidth;
    canvas.height = videoHeight;
    
    // Draw video frame to canvas
    const ctx = canvas.getContext('2d');
    
    // Ensure proper image rendering
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    
    // Draw the video frame
    ctx.drawImage(video, 0, 0, videoWidth, videoHeight);
    
    // Convert to base64 with high quality
    const imageData = canvas.toDataURL('image/jpeg', 0.92);
    
    // Update Livewire component and wait for it to complete
    try {
        await @this.set('capturedImage', imageData);
        
        // Ensure preview image is visible (fallback if Livewire update is slow)
        const previewImg = document.getElementById('preview-image');
        if (previewImg) {
            previewImg.src = imageData;
            previewImg.style.display = 'block';
            previewImg.style.objectFit = 'contain';
        }
        
        // Hide video element
        if (video) {
            video.style.display = 'none';
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