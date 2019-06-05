<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public const MAX_PER_PAGE = 10;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findWithPagination(int $page = 1, int $maxPerPage = 10): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        return $pagerfanta
            ->setCurrentPage($page)
            ->setMaxPerPage($maxPerPage);
    }
}
