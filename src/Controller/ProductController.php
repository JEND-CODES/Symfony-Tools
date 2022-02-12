<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $allProducts = $serializer->serialize($products, 'json');
        // $allProducts = $serializer->serialize($products, 'json', []);

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
    public function product(ProductRepository $repoProduct, int $id): Response
    { 
        $product = $repoProduct->find($id);

        if(!$product) {

            $response = new JsonResponse([
                'error' => 'not found',
                'http status' => '404'
            ]);

        } else {
            
            $response = new JsonResponse([
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

}
