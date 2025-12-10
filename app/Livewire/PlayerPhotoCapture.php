<?php

namespace App\Livewire;

use App\Livewire\Concerns\LoadsLeaderboard;
use App\Models\Photo;
use App\Models\GamePlayer;
use App\Models\Game;
use App\Models\BingoItem;
use App\Models\LocationBingoItem;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class PlayerPhotoCapture extends Component
{
    use LoadsLeaderboard;
    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    #[Locked]
    public $bingoItemId;
    public $showCamera = false;
    public $capturedImage = null;
    public $overlayText = '';
    public $bingoItemLabel = '';
    public $bingoItems = [];
    public $bingoItemStatuses = []; // Array of [bingo_item_id => status]

    // Timer & Game Status properties
    public $game = null;
    public bool $showLeaderboard = false;
    public array $leaderboardData = [];

    // Feedback form properties
    public bool $showFeedback = false;
    public ?int $rating = null;
    public ?string $age = null;
    
    // Cached player ID to avoid repeated lookups
    private ?int $cachedPlayerId = null;
    
    // Maximum file size: 5MB
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    
    public function mount($gameId, $playerToken, $bingoItemId = null)
    {
        $this->gameId = (int) $gameId;
        $this->playerToken = $playerToken;

        // Load game data first
        $this->game = Game::findOrFail($gameId);

        // Check if game is finished - show leaderboard
        if ($this->game->status === 'finished') {
            $this->loadLeaderboard();
            $this->showLeaderboard = true;
            return;
        }

        // Validate player token and game access
        $this->validatePlayerAccess($gameId, $playerToken);

        // Load bingo items for this game
        $this->loadBingoItems();

        // Load photo statuses for each bingo item
        $this->loadBingoItemStatuses();

        // Validate bingo item if provided
        if ($bingoItemId) {
            $this->validateBingoItem($bingoItemId, $gameId);
            $this->bingoItemId = $bingoItemId;
        }

        $this->loadOverlayText();
    }
    
    /**
     * Load bingo items for the current game
     */
    private function loadBingoItems(): void
    {
        $this->bingoItems = BingoItem::where('game_id', $this->gameId)
            ->orderBy('position')
            ->get()
            ->toArray();
    }
    
    /**
     * Load photo status for each bingo item
     */
    private function loadBingoItemStatuses(): void
    {
        $playerId = $this->getPlayerId();

        // Use pluck() for more efficient query - returns [bingo_item_id => status]
        $this->bingoItemStatuses = Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $playerId)
            ->pluck('status', 'bingo_item_id')
            ->toArray();
    }
    
    /**
     * Refresh photo statuses and check game status (called by polling)
     */
    public function refreshStatuses()
    {
        // Refresh game data
        $this->game = Game::findOrFail($this->gameId);

        // Check if game has finished
        if ($this->game->status === 'finished') {
            $this->loadLeaderboard();
            $this->showLeaderboard = true;
            return;
        }

        $this->loadBingoItemStatuses();
    }

    /**
     * Load leaderboard data sorted by score
     * Uses LoadsLeaderboard trait for shared implementation
     */
    private function loadLeaderboard()
    {
        $this->leaderboardData = $this->loadLeaderboardData($this->gameId);
    }
    
    /**
     * Handle clicking a bingo item to open camera
     */
    public function openPhotoCapture($bingoItemId)
    {
        // Validate type
        if (!is_numeric($bingoItemId)) {
            abort(400, 'Invalid bingo item ID');
        }
        
        $this->bingoItemId = (int)$bingoItemId;
        $this->validateBingoItem($this->bingoItemId, $this->gameId);
        
        // Check if player already has an approved photo for this bingo item
        $playerId = $this->getPlayerId();
        
        $approvedPhoto = Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $playerId)
            ->where('bingo_item_id', $this->bingoItemId)
            ->where('status', 'approved')
            ->exists();
        
        if ($approvedPhoto) {
            session()->flash('photo-message', 'Je hebt al een goedgekeurde foto voor dit item!');
            return;
        }
        
        $this->loadOverlayText();
        $this->openCamera();
    }
    
    /**
     * Validate that the player token is valid and belongs to the game
     * Caches the player ID to avoid repeated lookups
     */
    private function validatePlayerAccess($gameId, $playerToken): void
    {
        // Use cached player ID if available
        if ($this->cachedPlayerId !== null) {
            return;
        }
        
        $player = GamePlayer::where('token', $playerToken)
            ->where('game_id', $gameId)
            ->first();
            
        if (!$player) {
            abort(403, 'Unauthorized access');
        }
        
        // Cache the player ID
        $this->cachedPlayerId = $player->id;
        
        // Verify game is active
        $game = Game::findOrFail($gameId);
        if ($game->status !== 'started') {
            abort(403, 'Game is not active');
        }
    }

    /**
     * Get the cached player ID or fetch it if not cached
     */
    private function getPlayerId(): int
    {
        if ($this->cachedPlayerId === null) {
            $this->validatePlayerAccess($this->gameId, $this->playerToken);
        }
        
        return $this->cachedPlayerId;
    }
    
    /**
     * Validate that bingo item belongs to the game
     */
    private function validateBingoItem($bingoItemId, $gameId): void
    {
        $exists = BingoItem::where('id', $bingoItemId)
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
            
            $bingoItem = BingoItem::where('id', $this->bingoItemId)
                ->where('game_id', $this->gameId)
                ->first();
            
            if ($bingoItem) {
                // Store the bingo item label
                $this->bingoItemLabel = $bingoItem->label;
                
                // Load the fact if available
                if ($game->location) {
                    $locationBingoItem = LocationBingoItem::where('location_id', $game->location_id)
                        ->where('label', $bingoItem->label)
                        ->first();
                    
                    if ($locationBingoItem && $locationBingoItem->fact) {
                        $this->overlayText = $locationBingoItem->fact;
                    }
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
        
        // Bingo item is required
        if (!$this->bingoItemId) {
            throw ValidationException::withMessages([
                'bingo_item_id' => 'Bingo item is required'
            ]);
        }
        
        $this->validateBingoItem($this->bingoItemId, $this->gameId);
        
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
        
        // Get player ID (already validated and cached in validatePlayerAccess)
        $playerId = $this->getPlayerId();

        // Compress and optimize the image
        $compressedData = $this->compressImage($decodedData, $imageInfo);

        // Check if there's already a photo for this bingo item
        $existingPhotos = Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $playerId)
            ->where('bingo_item_id', $this->bingoItemId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Delete old files and remove duplicate photos (keep only the most recent one)
        $existingPhoto = null;
        foreach ($existingPhotos as $index => $photo) {
            if ($index === 0) {
                // Keep the first (most recent) photo record to update
                $existingPhoto = $photo;
            } else {
                // Delete duplicate photo files and records from both disks
                if ($photo->path) {
                    Storage::disk('public')->delete($photo->path);
                    // Try to delete from cloud storage too (if configured)
                    if (config("filesystems.disks.photos.driver") === 's3') {
                        try {
                            Storage::disk('photos')->delete($photo->path);
                        } catch (\Exception $e) {
                            // Log but don't fail if cloud delete fails
                            Log::warning('Failed to delete from cloud storage', [
                                'path' => $photo->path,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
                $photo->delete();
            }
        }

        // Delete old file if keeping existing photo (from both disks)
        if ($existingPhoto && $existingPhoto->path) {
            Storage::disk('public')->delete($existingPhoto->path);
            // Try to delete from cloud storage too
            if (config("filesystems.disks.photos.driver") === 's3') {
                try {
                    Storage::disk('photos')->delete($existingPhoto->path);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete from cloud storage', [
                        'path' => $existingPhoto->path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Generate secure filename
        $filename = 'photos/' . $this->gameId . '/' . $playerId . '/' . uniqid('', true) . '.jpg';

        // Store on local disk (public) - always save locally
        Storage::disk('public')->put($filename, $compressedData);

        // Also store on cloud disk (R2) if configured
        $cloudUploadFailed = false;
        if (config("filesystems.disks.photos.driver") === 's3') {
            try {
                Storage::disk('photos')->put($filename, $compressedData);
                Log::info('Photo saved to cloud storage', ['path' => $filename]);
            } catch (\Exception $e) {
                // Log error but don't fail - local storage is the primary
                Log::error('Failed to save to cloud storage', [
                    'path' => $filename,
                    'error' => $e->getMessage()
                ]);
                $cloudUploadFailed = true;
            }
        }

        // Update existing photo or create new one
        if ($existingPhoto) {
            // Update existing photo (overwrite)
            $existingPhoto->update([
                'path' => $filename,
                'status' => 'pending', // Reset to pending when overwriting
                'taken_at' => now(),
            ]);
        } else {
            // Create new photo
            Photo::create([
                'game_id' => $this->gameId,
                'game_player_id' => $playerId,
                'bingo_item_id' => $this->bingoItemId,
                'path' => $filename,
                'status' => 'pending',
                'taken_at' => now(),
            ]);
        }

        // Reload photo statuses after saving
        $this->loadBingoItemStatuses();

        // Reset state
        $this->showCamera = false;
        $this->capturedImage = null;

        // Show success message, with warning if cloud upload failed
        if ($cloudUploadFailed) {
            session()->flash('photo-message', 'Foto verzonden!');
        } else {
            session()->flash('photo-message', 'Foto verzonden!');
        }
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
        
        // Fix EXIF orientation for mobile photos
        $sourceImage = $this->fixImageOrientation($sourceImage, $imageData);
        
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
    
    /**
     * Fix EXIF orientation for mobile photos
     * Mobile devices often store photos with EXIF orientation data
     * that needs to be applied to display correctly
     */
    private function fixImageOrientation($image, $imageData)
    {
        // Check if EXIF extension is available
        if (!function_exists('exif_read_data')) {
            return $image; // Can't read EXIF, return as-is
        }
        
        // Try to read EXIF data from the image data
        // We need to write to a temp file to read EXIF (exif_read_data requires a file)
        $tempFile = tmpfile();
        if ($tempFile === false) {
            return $image; // Can't create temp file, return as-is
        }
        
        $tempPath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempPath, $imageData);
        
        $exif = @exif_read_data($tempPath);
        
        // Clean up temp file
        fclose($tempFile);
        @unlink($tempPath);
        
        if (!$exif || !isset($exif['Orientation'])) {
            return $image; // No orientation data, return as-is
        }
        
        $orientation = $exif['Orientation'];
        
        // Apply rotation/flip based on EXIF orientation
        // Note: imagerotate uses degrees, positive = counter-clockwise
        switch ($orientation) {
            case 2:
                // Horizontal flip
                if (function_exists('imageflip')) {
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                }
                break;
            case 3:
                // 180° rotation
                $image = imagerotate($image, 180, 0);
                break;
            case 4:
                // Vertical flip
                if (function_exists('imageflip')) {
                    imageflip($image, IMG_FLIP_VERTICAL);
                }
                break;
            case 5:
                // Vertical flip + 90° clockwise (270° counter-clockwise)
                if (function_exists('imageflip')) {
                    imageflip($image, IMG_FLIP_VERTICAL);
                }
                $image = imagerotate($image, -90, 0);
                break;
            case 6:
                // 90° clockwise (270° counter-clockwise)
                $image = imagerotate($image, -90, 0);
                break;
            case 7:
                // Horizontal flip + 90° clockwise (270° counter-clockwise)
                if (function_exists('imageflip')) {
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                }
                $image = imagerotate($image, -90, 0);
                break;
            case 8:
                // 90° counter-clockwise
                $image = imagerotate($image, 90, 0);
                break;
            default:
                // Orientation 1 or unknown - no change needed
                break;
        }
        
        return $image;
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

    /**
     * Show the feedback form after leaderboard
     */
    public function showFeedbackForm()
    {
        $this->showFeedback = true;
    }

    /**
     * Set rating value
     */
    public function setRating(int $value)
    {
        $this->rating = $value;
    }

    /**
     * Submit feedback and redirect to home
     */
    public function submitFeedback()
    {
        // Validate rating (1-10)
        if ($this->rating !== null && ($this->rating < 1 || $this->rating > 10)) {
            return;
        }

        // Validate age (0-120, must be numeric)
        if ($this->age !== null && $this->age !== '') {
            if (!is_numeric($this->age) || (int)$this->age < 0 || (int)$this->age > 120) {
                return;
            }
        }

        // Get player to save feedback
        $player = GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->first();

        if ($player) {
            $player->update([
                'feedback_rating' => $this->rating,
                'feedback_age' => $this->age !== null && $this->age !== '' ? (int)$this->age : null,
            ]);
        }

        // Redirect to home
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.player-photo-capture');
    }
}