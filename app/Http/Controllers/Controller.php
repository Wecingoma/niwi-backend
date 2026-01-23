<?php

namespace App\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function storePublicFile(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        $target = public_path($directory);

        File::ensureDirectoryExists($target);

        $name = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $file->move($target, $name);

        return '/' . $directory . '/' . $name;
    }

    protected function deletePublicFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $relative = ltrim($path, '/');
        $absolute = public_path($relative);

        if (File::exists($absolute)) {
            File::delete($absolute);
        }
    }

    protected function deletePublicFiles(?array $paths): void
    {
        if (! $paths) {
            return;
        }

        foreach ($paths as $path) {
            if (is_string($path)) {
                $this->deletePublicFile($path);
            }
        }
    }
}
