<?php

namespace App\Livewire;

use App\Models\Photo;
use App\Models\GamePlayer;
use App\Models\Game;
use App\Models\LocationBingoItem;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;


class PlayerPhotoCapture extends Component
{
    #[Locked]
    public $gameId;
    
    #[Locked]
    public $playerToken;
    
    public $bingoItemId;
    public $showCamera = false;
    public $capturedImage = null;
    public $overlayText = '';
    
    // Maximum file size: 5MB
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    
    public function mount($gameId, $playerToken, $bingoItemId = null)
    {
        // Validate player token and game access
        $this->validatePlayerAccess($gameId, $playerToken);
        
        $this->gameId = $gameId;
        $this->playerToken = $playerToken;
        
        // Validate bingo item if provided
        if ($bingoItemId) {
            $this->validateBingoItem($bingoItemId, $gameId);
            $this->bingoItemId = $bingoItemId;
        }
        
        $this->loadOverlayText();
    }
    
    /**
     * Validate that the player token is valid and belongs to the game
     */
    private function validatePlayerAccess($gameId, $playerToken): void
    {
        $player = GamePlayer::where('token', $playerToken)
            ->where('game_id', $gameId)
            ->first();
            
        if (!$player) {
            abort(403, 'Unauthorized access');
        }
        
        // Verify game is active
        $game = Game::findOrFail($gameId);
        if ($game->status !== 'started') {
            abort(403, 'Game is not active');
        }
    }
    
    /**
     * Validate that bingo item belongs to the game
     */
    private function validateBingoItem($bingoItemId, $gameId): void
    {
        $exists = DB::table('bingo_items')
            ->where('id', $bingoItemId)
            ->where('game_id', $gameId)
            ->exists();
            
        if (!$exists) {
            abort(404, 'Bingo item not found');
        }
    }
    
    public function loadOverlayText()
    {
        if (!$this->bingoItemId) {
            return;
        }
        
        // Re-validate access
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        $this->validateBingoItem($this->bingoItemId, $this->gameId);
        
        try {
            $game = Game::with('location')->findOrFail($this->gameId);
            
            $bingoItem = DB::table('bingo_items')
                ->where('id', $this->bingoItemId)
                ->where('game_id', $this->gameId)
                ->first();
            
            if ($bingoItem && $game->location) {
                $locationBingoItem = LocationBingoItem::where('location_id', $game->location_id)
                    ->where('label', $bingoItem->label)
                    ->first();
                
                if ($locationBingoItem && $locationBingoItem->fact) {
                    $this->overlayText = $locationBingoItem->fact;
                }
            }
        } catch (\Exception $e) {
            // Log error but don't expose details
            Log::error('Failed to load overlay text', [
                'game_id' => $this->gameId,
                'bingo_item_id' => $this->bingoItemId
            ]);
        }
    }
    
    public function openCamera()
    {
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        
        if ($this->bingoItemId) {
            $this->validateBingoItem($this->bingoItemId, $this->gameId);
        }
        
        $this->showCamera = true;   
        $this->capturedImage = null;
        $this->loadOverlayText();
        $this->dispatch('open-camera');
    }
    
    public function savePhoto($imageData)
    {
        // Re-validate access
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        
        if ($this->bingoItemId) {
            $this->validateBingoItem($this->bingoItemId, $this->gameId);
        }
        
        // Validate base64 image data
        if (!$this->validateImageData($imageData)) {
            throw ValidationException::withMessages([
                'image' => 'Invalid image data provided'
            ]);
        }
        
        // Remove data:image/jpeg;base64, prefix
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        $decodedData = base64_decode($imageData, true);
        
        // Verify base64 decoding succeeded
        if ($decodedData === false) {
            throw ValidationException::withMessages([
                'image' => 'Failed to decode image data'
            ]);
        }
        
        // Validate file size
        if (strlen($decodedData) > self::MAX_FILE_SIZE) {
            throw ValidationException::withMessages([
                'image' => 'Image file is too large (max 5MB)'
            ]);
        }
        
        // Validate it's actually an image
        $imageInfo = @getimagesizefromstring($decodedData);
        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                'image' => 'Invalid image format'
            ]);
        }
        
        // Only allow JPEG and PNG
        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) {
            throw ValidationException::withMessages([
                'image' => 'Only JPEG and PNG images are allowed'
            ]);
        }
        
        // Get player (already validated in validatePlayerAccess)
        $player = GamePlayer::where('token', $this->playerToken)
        ->where('game_id', $this->gameId)
        ->firstOrFail();

        // Compress and optimize the image
        $compressedData = $this->compressImage($decodedData, $imageInfo);

        // Generate secure filename
        $filename = 'photos/' . $this->gameId . '/' . $player->id . '/' . uniqid('', true) . '.jpg';

        // Store on public disk
        Storage::disk('public')->put($filename, $compressedData);

        // Save to database
        Photo::create([
            'game_id' => $this->gameId,
            'game_player_id' => $player->id,
            'bingo_item_id' => $this->bingoItemId,
            'path' => $filename,
            'status' => 'pending',
            'taken_at' => now(),
        ]);

        // Reset state
        $this->showCamera = false;
        $this->capturedImage = null;

        session()->flash('photo-message', 'Foto opgeslagen!');
        $this->dispatch('photo-saved');
    }

    /**
     * Compress and optimize image
     * 
     * @param string $imageData Raw image data
     * @param array|false $imageInfo Result from getimagesizefromstring
     * @return string Compressed JPEG image data
    */
    private function compressImage($imageData, $imageInfo): string
    {
        // Maximum dimensions (maintains aspect ratio)
        $maxWidth = 1920;
        $maxHeight = 1920;
        
        // JPEG quality (0-100, lower = smaller file but lower quality)
        $quality = 85;
        
        // Create image resource from string
        $sourceImage = @imagecreatefromstring($imageData);
        
        if ($sourceImage === false) {
            throw ValidationException::withMessages([
                'image' => 'Failed to process image'
            ]);
        }
        
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        

        if ($originalWidth <= 0 || $originalHeight <= 0) {
            throw ValidationException::withMessages([
                'image' => 'Invalid image dimensions'
            ]);
        }
        // Calculate new dimensions if resizing needed
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);
        
        // Only resize if image is larger than max dimensions
        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            // Create new image with calculated dimensions
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG (though we'll convert to JPEG)
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            
            // Resize with high-quality resampling
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );
            
            imagedestroy($sourceImage);
            $sourceImage = $resizedImage;
        }
        
        // Output to buffer as JPEG
        ob_start();
        imagejpeg($sourceImage, null, $quality);
        $compressedData = ob_get_clean();
        
        // Clean up
        imagedestroy($sourceImage);
        
        return $compressedData;
    }

    #[On('open-photo-capture')]
    public function handleOpenPhotoCapture($bingoItemId)
    {
        $this->bingoItemId = $bingoItemId;
        $this->validateBingoItem($bingoItemId, $this->gameId);
        $this->loadOverlayText();
        $this->openCamera();
    }
    
    /**
     * Validate base64 image data format
     */
    private function validateImageData($imageData): bool
    {
        if (!is_string($imageData) || empty($imageData)) {
            return false;
        }
        
        // Check if it's a valid base64 data URI
        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $imageData)) {
            return false;
        }
        
        return true;
    }
    
    public function retakePhoto()
    {
        $this->validatePlayerAccess($this->gameId, $this->playerToken);
        $this->capturedImage = null;
        $this->dispatch('retake-photo');
    }
    
    public function closeCamera()
    {
        $this->showCamera = false;
        $this->capturedImage = null;
        $this->dispatch('close-camera');
    }
    
    public function render()
    {
        return view('livewire.player-photo-capture');
    }
}