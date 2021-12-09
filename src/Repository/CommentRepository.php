<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function add(Comment $comment)
    {
        $comment->setCreatedDate(new \DateTime());
        $this->save($comment);
    }

    public function save(Comment $comment)
    {
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    public function delete(Comment $comment)
    {
        $this->_em->remove($comment);
        $this->_em->flush();
    }
    
    public function findCommentsByRecipe(Recipe $recipe)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.replyTo = -1')
            ->andWhere('c.recipe = :recipe')
            ->setParameter('recipe', $recipe)
            ->getQuery()
            ->getResult();
    }

    public function findRepliesOf($id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.replyTo = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
