<?php

namespace App\Repository;

use App\Entity\Tournament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tournament|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournament|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournament[]    findAll()
 * @method Tournament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournamentRepository extends ServiceEntityRepository
{
    public const MAX_PER_PAGE = 10;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tournament::class);
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
