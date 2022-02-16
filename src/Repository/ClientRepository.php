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

    public function rawSqlQuery() 
    {
        $conn = $this->getEntityManager()->getConnection();

        // EXEMPLES DE REQUÊTES SQL BRUTES

        // $sql = 'SELECT *
        //         FROM client
        //         INNER JOIN product
        //         WHERE client.id = product.id
        //         ';

        // $sql = 'SELECT *
        //         FROM client
        //         INNER JOIN product
        //         ON client.id = product.id
        //         INNER JOIN theme
        //         ON client.theme_id = theme.id
        //         ';

        // La commande UNION de SQL permet de mettre bout-à-bout les résultats de plusieurs requêtes utilisant elles-même la commande SELECT.
        // La commande UNION ALL de SQL est très similaire à la commande UNION. Elle permet de concaténer les enregistrements de plusieurs requêtes, à la seule différence que cette commande permet d’inclure tous les enregistrements, même les doublons. 
        // $sql = 'SELECT `name` FROM client
        //         UNION ALL
        //         SELECT `title` FROM product
        //         ';

        $sql = "SELECT `id`, `name` FROM client
                WHERE `name` = 'helloWorld'
                UNION ALL
                SELECT `id`, `title` FROM product
                WHERE `title` = 'Product1'
                ";

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        dd($resultSet->fetchAllAssociative());

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
