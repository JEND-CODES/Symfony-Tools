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

        // $sql = "SELECT `id`, `name` FROM client
        //         WHERE `name` = 'helloWorld'
        //         UNION ALL
        //         SELECT `id`, `title` FROM product
        //         WHERE `title` = 'Product1'
        //         ";

        // TRIER ALPHABÉTIQUEMENT LES RÉSULTATS UNIS DE DEUX TABLES
        // $sql = "SELECT `name` FROM client
        //         UNION ALL
        //         SELECT `title` FROM product
        //         ORDER BY `name` ASC
        //         ";

        // FUSIONNER DEUX TABLES QUI NE COMPORTENT PAS LE MÊME NOMBRE DE COLONNE, OU LES MÊMES NOMS DE COLONNES
        // $sql = "SELECT 'client' AS table_name, `id`, null AS `title`, null AS `description`
        //         FROM client
        //         UNION ALL
        //         SELECT 'product' AS table_name, `id`, null AS `name`, null AS `theme`
        //         FROM product
        //         ORDER BY `id` DESC
        //         ";

        // COMMANDE INTERSECT : PERMET DE RÉCUPÉRER LES VALEURS QUI SONT PRÉSENTES DE FAÇON IDENTIQUES DANS DEUX TABLES
        // Pour cet exemple, les deux tables ne comportant pas le même nombre d'IDs, la requête renvoie seulement les IDs en commun entre les deux tables
        // Cf. https://sql.sh/cours/intersect
        // $sql = 'SELECT DISTINCT `id` FROM `client`
        //         WHERE `id` IN (
        //         SELECT `id` 
        //         FROM `product`
        //         )
        //         ORDER BY `id` ASC
        //         ';

        // COMMANDE -> NOT IN -> à l'inverse de l'exemple précédent, si l'on souhaite récupérer des valeurs qui ne sont pas communes aux deux tables :
        // $sql = 'SELECT DISTINCT `id` FROM `product`
        //         WHERE `id` NOT IN (
        //         SELECT `id` 
        //         FROM `client`
        //         )
        //         ORDER BY `id` ASC
        //         ';

        // RECHERCHE LA CLÉ ÉTRANGÈRE DU CLIENT LA PLUS RÉPÉTÉE DANS LA TABLE PRODUCT
        // $sql = "SELECT client.id, client.name 
        //         FROM client 
        //         WHERE client.id = (
        //             SELECT client_id 
        //             FROM product 
        //             GROUP BY product.client_id 
        //             ORDER BY COUNT(*) DESC 
        //             LIMIT 1
        //             )
        //         ";

        // RECHERCHE LA CLÉ ÉTRANGÈRE DU CLIENT LA MOINS RÉPÉTÉE DANS LA TABLE PRODUCT
        // $sql = "SELECT client.id, client.name 
        //         FROM client 
        //         WHERE client.id = (
        //             SELECT client_id 
        //             FROM product 
        //             GROUP BY product.client_id 
        //             ORDER BY COUNT(*) ASC 
        //             LIMIT 1
        //             )
        //         ";

        // AFFICHE LA LISTE DES CLIENTS QUI N'ONT PAS ENCORE ACHETÉ UN PRODUIT
        // $sql = "SELECT client.name
        //         FROM client
        //         WHERE id NOT IN (
        //             SELECT product.client_id 
        //             FROM product
        //             )
        //         ";

        // AFFICHE LE CLIENT QUI A ACHETE LE PLUS DE PRODUITS
        // $sql = "SELECT 
        //         client.id, 
        //         client.name,
        //         COUNT(product.client_id) AS 'spentmost'
        //     FROM client 
        //     INNER JOIN product ON client.id = product.client_id 
        //     GROUP BY client.id, client.name
        //     HAVING spentmost =  (       
        //         SELECT COUNT(product.client_id) AS spentmost
        //         FROM product
        //         GROUP BY client_id
        //         ORDER BY spentmost DESC
        //         LIMIT 1
        //     )
        //     ";

        // La même recherche dans un format simplifié !
        // $sql = "SELECT product.client_id, client.name, COUNT(*) AS 'spentmost'
        //         FROM product 
        //         JOIN client
        //         ON product.client_id = client.id
        //         GROUP BY product.client_id
        //         LIMIT 1
        //         ";

        // AFFICHE LES 2 MEILLEURS ACHETEURS DE PRODUITS
        $sql = "SELECT product.client_id, client.name, COUNT(*) AS 'spentmost'
                FROM product 
                JOIN client
                ON product.client_id = client.id
                GROUP BY product.client_id
                LIMIT 2
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
