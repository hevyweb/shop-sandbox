<?php

namespace App\Service\Filesystem;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractRemover implements RemoveInterface
{
    use ContainerAwareTrait;
}