<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // Retourne une liste de produits filtrée selon l'Id du client qui leur est associé
    public function filterProductsByClientId(int $clientId) 
    {
        $query = $this->createQueryBuilder('p')
                ->where('p.client = :clientid')
                ->setParameter('clientid', $clientId)
                ->getQuery();

        return $query->getResult();

    }

    // Retourne une liste de produits filtrée selon le nom du client qui leur est associé
    public function filterProductsByClientName(string $clientName) 
    {
        $query = $this->createQueryBuilder('p')
                ->innerJoin('p.client', 'c')
                ->where('c.name = :clientname')
                ->setParameter('clientname', $clientName)
                ->getQuery();

        return $query->getResult();

    }

    // Retourne le nombre total de produits
    public function countProducts()
    {
        $query = $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function rawSqlQuery() 
    {
        $conn = $this->getEntityManager()->getConnection();

        // REQUÊTES SQL BRUTES

        // Exemple de calcul de sommes par colonnes
        // $sql = "SELECT 
        //         SUM(price) AS price_sum,
        //         SUM(id) AS id_sum,
        //         SUM(client_id) AS client_id_sum
        //         FROM product
        //         ";

        // Addition des sommes des colonnes
        // $sql = "SELECT 
        //         SUM(price) AS price_sum,
        //         SUM(id) AS id_sum,
        //         SUM(client_id) AS client_id_sum,
        //         (SUM(price) + SUM(id) + SUM(client_id)) as 'Total'
        //         FROM product
        //         ";

        // La fonction d’agrégation AVG() dans le langage SQL permet de calculer une valeur moyenne sur un ensemble d’enregistrement de type numérique et non nul.
        // $sql = "SELECT `title`, `client_id`,
        //         AVG(price) AS price_avg
        //         FROM product
        //         INNER JOIN client
        //         WHERE product.client_id = client.id
        //         ";
        
        // La même requête peut s'écrire de la manière suivante :
        // $sql = "SELECT product.title, 
        //         product.client_id,
        //         AVG(product.price) AS price_avg
        //         FROM product
        //         INNER JOIN client
        //         WHERE product.client_id = client.id
        //         ";

        // DÉPENSE EFFECTUÉE PAR CHAQUE CLIENT POUR CHAQUE PRODUIT :
        // Jointure de deux tables : cela renvoie le prix du produit et le nom de l'acheteur (le nom du client) :
        // $sql = "SELECT 
        //             product.price AS spent,
        //             client.name AS buyer
        //         FROM 
        //             product
        //         INNER JOIN 
        //             client
        //         WHERE 
        //             product.client_id = client.id
        //         GROUP BY 
        //             product.id
        //         ";

        // DÉPENSES TOTALES EFFECTUÉES PAR CHAQUE CLIENT :
        // Cela renvoie la somme totale dépensée pour les achats de produits, pour chaque client :
        // $sql = "SELECT 
        //             SUM(product.price) AS spent_sum,
        //             client.name AS client_name
        //         FROM 
        //             product
        //         INNER JOIN 
        //             client
        //         WHERE 
        //             product.client_id = client.id
        //         GROUP BY 
        //             product.client_id
        //         ";

        // DÉPENSES MOYENNES EFFECTUÉES PAR CHAQUE CLIENT :
        // Cela renvoie la somme moyenne dépensée pour les achats de produits, pour chaque client :
        // $sql = "SELECT 
        //             AVG(product.price) AS spent_avg,
        //             client.name AS client_name
        //         FROM 
        //             product
        //         INNER JOIN 
        //             client
        //         WHERE 
        //             product.client_id = client.id
        //         GROUP BY 
        //             product.client_id
        //         ";

        // PASSER UN PARAMÈTRE ASSOCIÉ À UNE VARIABLE
        // $param1 = 20;

        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         WHERE product.price <= :param1
        //         ";

        // PASSER 2 PARAMÈTRES
        // $param1 = 20;
        // $param2 = 50;

        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         WHERE product.price BETWEEN :param1 AND :param2
        //         ";

        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         WHERE product.price >= :param1
        //         AND product.price <= :param2
        //         ";

        // PASSER PLUSIEURS VALEURS D'UN ARRAY DANS LA REQUÊTE
        // $values = array('20', '30', '40');
        // $values = "'" . implode("','", $values) . "'";

        // $sql = "SELECT product.title 
        //         FROM product 
        //         WHERE product.price IN (" . $values . ")
        //         ";

        // CALCULER LE PRIX MOYEN DE TOUS LES PRODUITS
        // $sql = "SELECT sum(price) / count(price) 
        //         AS 'medium_price'
        //         FROM product
        //         ";

        // CALCULER LE NOMBRE DE PRODUITS DONT LE PRIX DÉPASSE UNE VALEUR
        // $sql = "SELECT COUNT(*) AS 'total'
        //         FROM product
        //         WHERE product.price >= 50
        //         ";

        // AFFICHER LE PRODUIT DONT LE PRIX EST LE PLUS BAS
        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         WHERE product.price = (
        //             SELECT MIN(product.price)
        //             FROM product
        //             )
        //         ";

        // Ce qui équivaut à :
        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         ORDER BY product.price ASC
        //         LIMIT 1
        //         ";

        // AFFICHER LE PRODUIT DONT LE PRIX EST LE PLUS ÉLEVÉ
        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         WHERE product.price = (
        //             SELECT MAX(product.price)
        //             FROM product
        //             )
        //         ";

        // AFFICHE LES 3 PRODUITS LES PLUS CHERS
        // $sql = "SELECT product.title, product.price 
        //         FROM product 
        //         ORDER BY product.price DESC
        //         LIMIT 3
        //         ";

        // AFFICHE TOUS LES PRODUITS DONT LE NOM COMMENCE PAR LA LETTRE P
        // $sql = "SELECT * 
        //         FROM product 
        //         WHERE product.title 
        //         LIKE 'P%'
        //         ";

        // AFFICHE TOUS LES PRODUITS DONT LE NOM CONTIENT LA LETTRE P
        // $sql = "SELECT * 
        //         FROM product 
        //         WHERE product.title 
        //         LIKE '%P%'
        //         ";

        // AFFICHE TOUS LES PRODUITS DONT LES PRIX SONT SUPÉRIEURS AU PRIX MOYEN DE TOUS LES PRODUITS
        $sql = "SELECT * 
                FROM product 
                WHERE product.price > (
                    SELECT sum(product.price)/count(product.price)
                    FROM product
                    ) 
                ";

        $stmt = $conn->prepare($sql);

        // bindParam -> LIE UN PARAMÈTRE À UN NOM DE VARIABLE SPÉCIFIQUE
        // $stmt->bindParam(':param1', $param1);
        // $stmt->bindParam(':param2', $param2);

        // bindValue -> ASSOCIE UNE VALEUR À UN PARAMÈTRE
        // $stmt->bindValue(':param1', $param1);

        $resultSet = $stmt->executeQuery();

        dd($resultSet->fetchAllAssociative());

    }

    public function spentByClient() 
    {
        $conn = $this->getEntityManager()->getConnection();

        // Somme totale dépensée pour les achats de produits, pour chaque client
        $sql = "SELECT 
                    SUM(product.price) AS spent_sum,
                    client.name AS client_name
                FROM 
                    product
                INNER JOIN 
                    client
                WHERE 
                    product.client_id = client.id
                GROUP BY 
                    product.client_id
                ";

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
