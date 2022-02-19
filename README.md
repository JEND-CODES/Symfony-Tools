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

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Class Dumps | Utils | dumpInRelationMethod |

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

| Tool / Method  | Folder | Example |
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

| Tool / Method | Folder | Example |
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

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Serializer | Controller | spentProducts |

```
public function spentProducts(ProductRepository $repoProduct, SerializerInterface $serializer): Response
{
    $products = $repoProduct->spentByClient();

    $productsBy = $serializer->serialize($products, 'json');

    $response = new Response($productsBy, 200, [
        "Content-Type" => "application/json",
    ]);
    
    return $response;

}
```

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| json_encode | Controller | product |

```
/**
* @Route("/product/{id}", name="product", requirements={"id": "\d+"}, methods={"GET"})
*/
public function product(ProductRepository $repoProduct, int $id, RequestService $requestService): Response
{ 
    $product = $repoProduct->find($id);

    if(!$product) {

        $response = new JsonResponse([
            'uri' => $requestService->getUriInfo(),
            'error' => 'not found',
            'http status' => '404'
        ]);

    } else {
        
        $response = new JsonResponse([
            'uri' => $requestService->getUriInfo(),
            'result' => 'ok',
            'http status' => '200',
            'product' => [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'createdAt' => $product->getCreatedAt()
            ]
        ]);

    }

    return $response;

}
```