<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    // Retourne une liste de clients filtrée selon la valeur minimale et la valeur maximale d'Ids
    public function filterClientsByIdMinAndMax(int $idMin, int $idMax) 
    {
        $query = $this->createQueryBuilder('c')
                        ->where('c.id <= :idmax')
                        ->andWhere('c.id >= :idmin')
                        ->setParameters(
                            array(
                                'idmin' => $idMin,
                                'idmax' => $idMax,
                            )
                        )
                        ->getQuery();

        return $query->getResult();

    }

    // Retourne une liste de clients filtrée en fonction du nom du thème qui leur est associé
    public function filterClientsByThemeName(string $themeName) 
    {
        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.theme', 't')
                ->where('t.name = :themename')
                ->setParameter('themename', $themeName)
                ->getQuery();

        return $query->getResult();

    }

    // Retourne une liste de clients filtrée selon l'Id du thème qui leur est associé
    public function filterClientsByThemeId(int $themeId) 
    {
        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.theme', 't')
                ->where('t.id = :themeid')
                ->setParameter('themeid', $themeId)
                ->getQuery();

        return $query->getResult();

    }

    // /**
    //  * @return Client[] Returns an array of Client objects
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
    public function findOneBySomeField($value): ?Client
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
