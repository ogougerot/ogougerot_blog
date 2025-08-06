<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Cette méthode filtre les posts en fonction du tag précisé.
     *
     * @return array<int, Post>
     */
    public function filterPostsByTag(int $tag_id): array
    {
        return $this->createQueryBuilder('p')
                    ->join('p.tags', 't')
                    ->select('p')
                    ->where('t.id = :id')
                    ->andWhere('p.isPublished = :val')
                    ->setParameter('id', $tag_id)
                    ->setParameter('val', true)
                    ->orderBy('p.publishedAt', 'DESC')
                    ->getQuery()
                    ->getResult();
    }


    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
