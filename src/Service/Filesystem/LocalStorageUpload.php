<?php

namespace App\Service\Filesystem;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class takes file from the temporary folder and put it to the public folder on the server
 *
 * Class LocalStorageUpload
 * @package App\Utils\Filesystem
 */
class LocalStorageUpload extends AbstractUpload implements UploadInterface
{
    /**
     * @var string relative path under which image will be reachable in browser
     */
    protected $publicUrl;

    /**
     * LocalStorageUpload constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param UploadedFile $file
     * @return File
     */
    public function upload(UploadedFile $file = null)
    {
        if (!empty($file)) {
            $fileName = $this->reservePath($file);
            return $file->move($this->getServerPath(), $fileName);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    /**
     * @param UploadedFile $file
     * @return string a new file name
     */
    public function reservePath(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        do {
            $fileName = uniqid() . '.' . $extension;
            $filePath = $this->getServerPath() . $fileName;
        } while(file_exists($filePath));

        $this->publicUrl = $this->container->getParameter('image_local_storage') . '/' . $fileName;

        return $fileName;
    }

    protected function getServerPath()
    {
        $root = $this->container->get('kernel')->getProjectDir();
        $public = $this->container->getParameter('image_local_storage');
        return $root . '/public' . $public . '/';

    }
}