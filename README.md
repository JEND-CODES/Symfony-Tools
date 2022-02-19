## Tools tests on Symfony framework

### Tools tests (in progress)

``` bash
* Dumps
* createQueryBuilder
* Raw SQL queries
* SerializerInterface
* Json Encode / Json Decode
* Api Platform configurations
* StopWatch
* Calculation delay request
* Pagination (start, limit)
* Curl Api (array_filter, array_map)
* Services
* Handlers
* Listeners
* Subscribers
* Validators
* Upload Files
```

| Tool  | Folder | Example |
| ------------- | ------------- | ------------- |
| Dumps Class | Utils | dumpInRelationMethod |

```
 public function dumpInRelationMethod($itemsQueried, string $methodName, string $relatedMethod) 
 {

    $method = 'get'.ucfirst($methodName);

    $related = 'get'.ucfirst($relatedMethod);

    $string = $itemsQueried->$method();

    $result = $string->$related();

    dd($result);

}
```

| Tool  | Folder | Example |
| ------------- | ------------- | ------------- |
| createQueryBuilder | Repository | filterProductsByClientName |

```
public function filterProductsByClientName(string $clientName) 
{
    $query = $this->createQueryBuilder('p')
            ->innerJoin('p.client', 'c')
            ->where('c.name = :clientname')
            ->setParameter('clientname', $clientName)
            ->getQuery();

    return $query->getResult();

}
```

| Tool  | Folder | Example |
| ------------- | ------------- | ------------- |
| Raw Sql query | Repository | spentByClient |

```
public function spentByClient() 
{
    $conn = $this->getEntityManager()->getConnection();

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
```