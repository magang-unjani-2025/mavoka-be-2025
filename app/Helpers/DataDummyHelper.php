<?php

namespace App\Helpers;

class DataDummyHelper
{
    /**
     * Resolve a file or directory path inside data-dummy.
     * Tries in order: public/data-dummy, base path data-dummy, base path public/data-dummy (redundant safeguard).
     */
    public static function resolve(string $relative, bool $mustBeDir = false): ?string
    {
        $candidates = [
            public_path('data-dummy/'.ltrim($relative,'/\\')),
            base_path('data-dummy/'.ltrim($relative,'/\\')),
            base_path('public/data-dummy/'.ltrim($relative,'/\\')),
        ];
        foreach ($candidates as $path) {
            if ($mustBeDir) {
                if (is_dir($path)) return $path;
            } else {
                if (file_exists($path)) return $path;
            }
        }
        return null;
    }
}
