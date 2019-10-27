<?php

namespace App\Form\DataTransformer;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CategoryTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms a category instance to string
     *
     * @param  Category|string $category category id
     * @throws TransformationFailedException
     * @return string category object on success
     */
    public function transform($category)
    {
        if ($category instanceof Category) {
            return $category->getId();
        }

        return $category;
    }

    /**
     * Transforms category id to an entity
     *
     * @param  Category $category
     * @return string
     */
    public function reverseTransform($category)
    {
        if (!empty($category) && !($category instanceof Category)){
            $category = $this->entityManager->getRepository(Category::class)->find($category);

            if (empty($category)) {
                throw new TransformationFailedException('Category with id "' . $category . '" not found.');
            }
        }

        return $category;
    }
}