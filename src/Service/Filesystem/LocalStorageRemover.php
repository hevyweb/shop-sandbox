<?php

namespace App\Service\Filesystem;

use Psr\Log\LoggerAwareTrait;

/**
 * This class takes file from the temporary folder and put it to the public folder on the server
 *
 * Class LocalStorageUpload
 * @package App\Utils\Filesystem
 */
class LocalStorageRemover extends AbstractRemover
{
    use LoggerAwareTrait;

    public function remove($filePath)
    {
        $root = $this->container->get('kernel')->getProjectDir();
        $fullPath = $root . '/public' . $filePath;

        if (is_file($fullPath)) {
            if (!unlink($fullPath)) {
                $this->logger->error('Not able to remove file: ' . $fullPath);
                return false;
            }
        }

        return true;
    }
}