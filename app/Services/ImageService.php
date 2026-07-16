<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * All uploads land on the public disk with unique names; only the
 * relative storage path is persisted. GD thumbnails are generated
 * alongside originals as "<name>_thumb.<ext>".
 */
class ImageService
{
    public const DISK = 'public';

    public function store(UploadedFile $file, string $directory, ?string $replaces = null, int $thumbWidth = 480): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = substr($filename ?: 'image', 0, 60).'-'.Str::random(8).'.'.$extension;

        $path = $file->storeAs($directory, $filename, self::DISK);

        $this->makeThumbnail($path, $thumbWidth);

        if ($replaces) {
            $this->delete($replaces);
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        // Never allow traversal outside the disk.
        $path = str_replace(['..', '\\'], ['', '/'], $path);

        Storage::disk(self::DISK)->delete($path);
        Storage::disk(self::DISK)->delete($this->thumbPath($path));
    }

    public function thumbPath(string $path): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '') !== '.' ? ($info['dirname'].'/') : '';

        return $dir.$info['filename'].'_thumb.'.($info['extension'] ?? 'jpg');
    }

    /** Public URL for a stored path, with a bundled fallback. */
    public static function url(?string $path, string $fallback = '/images/placeholder-food.svg'): string
    {
        if (! $path) {
            return asset($fallback);
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path; // seeded absolute/bundled asset
        }

        return Storage::disk(self::DISK)->url($path);
    }

    /** Thumbnail URL falling back to the original, then the placeholder. */
    public static function thumbUrl(?string $path, string $fallback = '/images/placeholder-food.svg'): string
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return self::url($path, $fallback);
        }

        $service = app(self::class);
        $thumb = $service->thumbPath($path);

        return Storage::disk(self::DISK)->exists($thumb)
            ? Storage::disk(self::DISK)->url($thumb)
            : self::url($path, $fallback);
    }

    private function makeThumbnail(string $path, int $width): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $absolute = Storage::disk(self::DISK)->path($path);
        $info = @getimagesize($absolute);
        if (! $info) {
            return;
        }

        [$srcWidth, $srcHeight, $type] = $info;
        if ($srcWidth <= $width) {
            return; // already small enough
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolute),
            IMAGETYPE_PNG => @imagecreatefrompng($absolute),
            IMAGETYPE_WEBP => @imagecreatefromwebp($absolute),
            IMAGETYPE_GIF => @imagecreatefromgif($absolute),
            default => null,
        };

        if (! $source) {
            return;
        }

        $height = (int) round($srcHeight * ($width / $srcWidth));
        $thumb = imagecreatetruecolor($width, $height);

        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

        $thumbAbsolute = Storage::disk(self::DISK)->path($this->thumbPath($path));

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($thumb, $thumbAbsolute, 82),
            IMAGETYPE_PNG => imagepng($thumb, $thumbAbsolute, 6),
            IMAGETYPE_WEBP => imagewebp($thumb, $thumbAbsolute, 82),
            IMAGETYPE_GIF => imagegif($thumb, $thumbAbsolute),
            default => null,
        };

        imagedestroy($source);
        imagedestroy($thumb);
    }
}
