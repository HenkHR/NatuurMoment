<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesFileUploads
{
    /**
     * Store an uploaded file and return the path.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $directory The storage directory
     * @return string|false The stored path or false on failure
     */
    protected function storeUploadedFile(UploadedFile $file, string $directory): string|false
    {
        return $file->store($directory, 'public');
    }

    /**
     * Delete an existing file from storage.
     *
     * @param string|null $path The file path to delete
     */
    protected function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Handle file upload with optional replacement of existing file.
     *
     * @param Request $request The request containing the file
     * @param string $fieldName The form field name
     * @param string $directory The storage directory
     * @param string|null $existingPath Path to existing file (will be deleted if new file uploaded)
     * @return array{path: string|null, error: string|null} Result with path or error
     */
    protected function handleFileUpload(
        Request $request,
        string $fieldName,
        string $directory,
        ?string $existingPath = null
    ): array {
        if (!$request->hasFile($fieldName)) {
            return ['path' => null, 'error' => null];
        }

        // Delete existing file if present
        $this->deleteStoredFile($existingPath);

        // Store new file
        $path = $this->storeUploadedFile($request->file($fieldName), $directory);

        if ($path === false) {
            return ['path' => null, 'error' => 'Bestand kon niet worden opgeslagen.'];
        }

        return ['path' => $path, 'error' => null];
    }

    /**
     * Handle file removal request.
     *
     * @param Request $request The request
     * @param string $removeFieldName The form field name for remove checkbox
     * @param string|null $existingPath Path to existing file
     * @return bool True if file was removed
     */
    protected function handleFileRemoval(
        Request $request,
        string $removeFieldName,
        ?string $existingPath
    ): bool {
        if ($request->boolean($removeFieldName)) {
            $this->deleteStoredFile($existingPath);
            return true;
        }

        return false;
    }
}
