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
| Dumps  | Utils | dumpInRelationMethod() |

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