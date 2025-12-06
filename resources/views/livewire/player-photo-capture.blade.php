<div>
    <!-- Flash message -->
    @if (session('photo-message'))
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
            {{ session('photo-message') }}
        </div>
    @endif

    <!-- Open Camera Button -->
    @if (!$showCamera)
        <button 
            wire:click="openCamera"
            class="bg-[#2E7D32] hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
            📷 Neem Foto
        </button>
    @endif

    <!-- Camera View -->
    @if ($showCamera)
        <div class="fixed inset-0 z-50 bg-black flex flex-col">
            <!-- Camera Container -->
            <div class="relative flex-1 flex items-center justify-center">
                <!-- Video Element -->
                <video 
                    id="camera-video" 
                    autoplay 
                    playsinline
                    class="w-full h-full object-cover">
                </video>
                
                <!-- Canvas for capture -->
                <canvas id="camera-canvas" class="hidden"></canvas>


                <!-- Overlay Text -->
                @if($overlayText)
                    <div class="absolute top-10 left-0 right-0 text-center z-10">
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
                        class="absolute inset-0 w-full h-full object-cover"
                        alt="Preview">
                @endif
            </div>

            <!-- Controls -->
            <div class="bg-black bg-opacity-80 p-4 flex justify-center gap-4">
                @if(!$capturedImage)
                    <!-- Capture Button -->
                    <button 
                        onclick="capturePhoto()"
                        class="bg-white rounded-full p-4 hover:bg-gray-200 transition">
                        <div class="w-16 h-16 border-4 border-gray-800 rounded-full"></div>
                    </button>
                @else
                    <!-- Retake Button -->
                    <button 
                        wire:click="retakePhoto"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold">
                        🔄 Opnieuw
                    </button>
                    
                    <!-- Submit Button -->
                    <button 
                        onclick="submitPhoto()"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                        ✓ Verzenden
                    </button>
                @endif
                
                <!-- Close Button -->
                <button 
                    wire:click="closeCamera"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold">
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
    });
    
    Livewire.on('retake-photo', () => {
        startCamera();
    });
    
    Livewire.on('close-camera', () => {
        stopCamera();
    });
});

async function startCamera() {
    try {
        video = document.getElementById('camera-video');
        canvas = document.getElementById('camera-canvas');
        
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

function capturePhoto() {
    if (!video || !canvas) return;
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    // Convert to base64
    const imageData = canvas.toDataURL('image/jpeg', 0.85);
    
    // Stop camera stream
    stopCamera();
    
    // Update Livewire component
    @this.capturedImage = imageData;
}

function submitPhoto() {
    const imageData = @this.capturedImage;
    if (imageData) {
        @this.savePhoto(imageData);
    }
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