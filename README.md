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
| json_encode | Controller | /product/{id} |

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

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| StopWatch | Controller | watchHomeQueries |

```
/**
* @Route("/watchhome", name="watch_home")
*/
public function watchHomeQueries(ClientRepository $repoClient, Delay $delay)
{
    $stopWatch = new Stopwatch();

    $stopWatch->start('watchHomeQueries');

    $stopWatch->lap('watchHomeQueries');

    $delay->executionDelay(1);

    $stopWatch->lap('watchHomeQueries');

    $repoClient->findBy(array(), array('id' => 'ASC'));

    $stopWatch->lap('watchHomeQueries');

    $repoClient->find(1);

    $stopWatch->lap('watchHomeQueries');

    $repoClient->filterClientsByIdMinAndMax(1, 3);

    $event = $stopWatch->stop('watchHomeQueries');

    dd(
        $event, 

        $event->getPeriods(),

        "Category the event was started in : " . 
        $event->getCategory(),
        
        "Event start time in milliseconds : " . 
        $event->getOrigin(), 

        "Stops all periods not already stopped : " . 
        $event->ensureStopped(), 
        
        "Start time of the very first period : " . 
        $event->getStartTime(),  

        "End time of the very last period : " . 
        $event->getEndTime(),

        "Event duration, including all periods : " . 
        $event->getDuration(),   

        "Max memory usage of all periods : " . 
        $event->getMemory(), 

    );

}
```

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Request time | Utils | audit |

```
public function audit(float $reqTime, float $resTime): void
{
    $reqMilliSecond = (int) ($reqTime * 1000);
    $resMilliSecond = (int) ($resTime * 1000);
    $reqMicroSecond = (int) ($reqTime * 1000000);
    $resMicroSecond = (int) ($resTime * 1000000);

    $audit = [
        'milliseconds' => [
            'req' => $reqMilliSecond,
            'res' => $resMilliSecond,
            'elapsed' => $resMilliSecond - $reqMilliSecond,
        ],
        'microseconds' => [
            'req' => $reqMicroSecond,
            'res' => $resMicroSecond,
            'elapsed' => $resMicroSecond - $reqMicroSecond,
        ]
    ];

    echo '<pre>';
    var_dump($audit);
    echo '</pre>';

}
```

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Curl Api | Controller | curlApiFilterBy |

```
/**
* @Route("/curlapifilterby", name="curl_api_filterby")
*/
public function curlApiFilterBy(): Response
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?results=10');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    
    $json_array = json_decode(curl_exec($ch))->results;

    $filterBy = 'male';

    $json_filtered = array_filter($json_array, function ($item) use ($filterBy) {

        return ($item->gender == $filterBy);

    });
    
    foreach($json_filtered as $data){

        echo '
            <p>NAME : '. $data->name->first .'</p>
            <p>ADDRESS : '. $data->location->street->name .'</p>
            <p>EMAIL : '. $data->email .'</p>
            <hr>
        ';

    }

    return new Response();
}
```