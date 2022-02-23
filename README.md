## Tools tests on Symfony framework

### Tools tests (in progress)

``` bash
* Dumps
* createQueryBuilder
* Raw SQL queries
* bindParam / bindValue
* SerializerInterface
* Json Encode / Json Decode
* Api Platform configurations
* StopWatch
* Calculation delay request
* Pagination (start, limit)
* Ajax Json Data dynamically
* Curl Api (array_filter, array_map)
* Services
* Handlers
* Listeners
* Subscribers
* Validators / Violations
* Copy, upload, delete files
* Form getData
```

### Examples

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Class Dumps | Utils | dumpInRelationMethod |

```php
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

``` php
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

``` php
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

``` php
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
| JsonResponse | Controller | /product/{id} |

``` php
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

``` php
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

``` php
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

``` php
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

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| OverrideListener | EventListener | onKernelController |

``` php
namespace App\EventListener;
 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
 
class OverrideListener
{
    public function onKernelController(ControllerEvent $event)
    {
        
        $message = 'OverrideListener message';
 
        if ($event->getRequest()->get('_route') === 'products') {

            // Override controller response
            $event->setController(

                function() use ($message) {

                    return new Response($message, 400);

                }

            );

        }
        
    }

}
```

| Tool / Method | Folder | Example |
| ------------- | ------------- | ------------- |
| Upload file | Controller | uploadImage |

``` php
/**
* @Route("/uploadimage", name="upload_image")
*/
public function uploadImage(): Response
{
    $imageSource = 'http://test.planetcode.fr/images/02.jpg';

    $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/' . uniqid(). '.jpg';

    file_put_contents($newImage, file_get_contents($imageSource));

    return new Response(
        'File recorded !', 
        200, 
        ['content-type' => 'text/html']
    );

}
```
