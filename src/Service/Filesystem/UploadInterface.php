<?php

namespace App\Service\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadInterface
{
    public function upload(UploadedFile $file);

    public function getPublicUrl(): string;

    public function reservePath(UploadedFile $file): string;
}