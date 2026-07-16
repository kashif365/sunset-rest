<?php

namespace App\Support;

/**
 * Minimal allow-list sanitizer for admin-entered rich text
 * (page bodies, FAQ answers). Not a substitute for escaping
 * untrusted end-user input — that is always escaped in Blade.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><h5><a><blockquote><table><thead><tbody><tr><th><td><img><hr><span>';

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $html = strip_tags($html, self::ALLOWED_TAGS);

        // Drop inline event handlers and javascript:/data: URLs.
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/\s(href|src)\s*=\s*(["\']?)\s*(javascript|vbscript|data)\s*:[^"\'\s>]*\2/i', ' $1="#"', $html);

        return $html;
    }
}
