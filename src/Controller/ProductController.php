<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Service\RequestService;
use App\Validator\CustomConstraint;
use App\Validator\Violations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="products", methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function products(ProductRepository $repoProduct, SerializerInterface $serializer): Response
    {
        $products = $repoProduct->findBy(array(), array('id' => 'DESC'));

        // $allProducts = $serializer->serialize($products, 'json');
        // $allProducts = $serializer->serialize($products, 'json', []);

        // On peut filtrer les résultats en passant un groupe directement défini dans les annotations de l'entité Product.php -> @Groups({"show_product"})
        // On peut changer le nom d'une clé du tableau JSON en utilisant l'annotation @SerializedName("custom_name")
        $allProducts = $serializer->serialize($products, 'json', ['groups' => 'show_product']);

        // SÉRIALISER EN IGNORANT UN ATTRIBUT :
        // $allProducts = $serializer->serialize($products, 'json', ['ignored_attributes' => ['client']]);

        // SÉRIALISER EN IGNORANT PLUSIEURS ATTRIBUTS :
        // $allProducts = $serializer->serialize($products, 'json', ['ignored_attributes' => ['id', 'createdAt', 'client']]);

        $response = new Response($allProducts, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

        // return $this->render('product/product.html.twig', [
        //     'controller_name' => 'ProductController',
        // ]);
    }

    /**
     * @Route("/product/{id}", name="product", requirements={"id": "\d+"}, methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function product(ProductRepository $repoProduct, int $id, RequestService $requestService): Response
    { 
        $product = $repoProduct->find($id);

        dd($product);

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

    /**
     * @Route("/productsby/clientid/{clientId}", name="products_by_client_id", requirements={"clientId": "\d+"}, methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function productsByClientId(ProductRepository $repoProduct, SerializerInterface $serializer, int $clientId): Response
    {
        $products = $repoProduct->filterProductsByClientId($clientId);

        $productsBy = $serializer->serialize($products, 'json');

        $response = new Response($productsBy, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

    }

    /**
     * @Route("/productsby/clientname/{clientName}", name="products_by_client_name", methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function productsByClientName(ProductRepository $repoProduct, SerializerInterface $serializer, string $clientName): Response
    {
        $products = $repoProduct->filterProductsByClientName($clientName);

        $productsBy = $serializer->serialize($products, 'json');

        $response = new Response($productsBy, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

    }

    /**
     * @Route("/productsprices", name="products_prices", methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function productsPrices(ProductRepository $repoProduct): Response
    {
        $productsPrices = [];

        $productsCount = $repoProduct->countProducts();

        for ($i = 1; $i <= $productsCount; $i++) {

            array_push(
                $productsPrices, 
                array(
                    "title" => $repoProduct->find($i)->getTitle(),
                    "price" => $repoProduct->find($i)->getPrice()
                )
            );
        
        }

        $json_data = [];

        array_push($json_data, $productsPrices);
        
        $json = json_encode($json_data);

        $response = new Response($json, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

    }

    /**
     * @Route("/product/{id}/price", name="product_price", requirements={"id": "\d+"}, methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function productPrice(ProductRepository $repoProduct, int $id): Response
    {
        $product_array = [];

        array_push(
            $product_array, 
            array(
                "title" => $repoProduct->find($id)->getTitle(),
                "price" => $repoProduct->find($id)->getPrice()
            )
        );
        
        $json_data = json_encode($product_array);

        $response = new Response($json_data, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

    }

    /**
     * @Route("/productspagination", name="products_pagination", methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function jsonProductsPagination(ProductRepository $repoProduct, Request $request, SerializerInterface $serializer): Response
    {
        $startParam = $request->query->get('start');

        $limitParam = $request->query->get('limit');

        if (!$startParam && !$limitParam) {
            // productspagination
            $start = 0;
            $limit = 3;

        } elseif (!$startParam) {
            // productspagination?limit=2
            $start = 0;
            $limit = (int) strip_tags($limitParam);

        } elseif (!$limitParam) {
            // productspagination?start=1
            $start = (int) strip_tags($startParam);
            $limit = 3;

        } else {
            // productspagination?start=1&limit=2
            $start = (int) strip_tags($startParam);
            $limit = (int) strip_tags($limitParam);
        }

        $products = $repoProduct->findBy(array(), array('id' => 'ASC'), $limit, $start);

        $allProducts = $serializer->serialize($products, 'json');

        $response = new Response($allProducts, 200, [
            "Content-Type" => "application/json",
        ]);

        return $response;

    }

    /**
     * @Route("/spentproducts", name="spent_products", methods={"GET"})
     * @param ProductRepository $repoProduct
     * @return Response
     */
    public function spentProducts(ProductRepository $repoProduct, SerializerInterface $serializer, Request $request): Response
    {
        // Requête SQL brute qui renvoie la somme totale dépensée pour les achats de produits, pour chaque client :
        $products = $repoProduct->spentByClient();

        $productsBy = $serializer->serialize($products, 'json');

        $response = new Response($productsBy, 200, [
            "Content-Type" => "application/json",
        ]);
        
        return $response;

    }

    /**
     * @Route("/createproduct", name="create_product", methods={"GET", "POST"})
     */
    public function createProduct(ClientRepository $repoClient, EntityManagerInterface $manager, CustomConstraint $customConstraint, Violations $violations): Response
    {

        $client = $repoClient->find(1);

        $product = new Product();

        $title = 'ProductX';
        $description = 'ProductDescriptionX';
        $price = 120.15;

        // $customConstraint->validateString($title);
        // $customConstraint->validateString($description);
        // $customConstraint->validateFloat($price);
        // $customConstraint->validateObject($client);

        // $violations->checkIfSymbols($title);
        // $violations->checkTypeLower($title);

        $product->setTitle($title)
                ->setDescription($description)
                ->setPrice($price)
                ->setCreatedAt(new \DateTime())
                ->setClient($client)
                ;
        
        $manager->persist($product);

        $manager->flush();

        return new Response(
            'Nouveau produit ajouté !', 
            200, 
            ['content-type' => 'text/html']
        );

    }

}
