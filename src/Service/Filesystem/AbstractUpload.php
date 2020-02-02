<?php

namespace App\Service\Filesystem;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractUpload implements UploadInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
}