<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findRootCategories()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function removeHierarchy(Category $category)
    {
        if ($children = $category->getChildren()){
            if (count($children)) {
                foreach ($children as $child) {
                    $this->removeHierarchy($child);
                }
            }
        }
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }
}
