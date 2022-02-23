<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DynamicController extends AbstractController
{
    /**
     * @Route("/dynamic", name="dynamic", methods={"GET"})
     */
    public function dynamic(ProductRepository $repoProduct): Response
    {
        // findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
        $products = $repoProduct->findBy(array(), array('id' => 'ASC'), 1, 0);

        return $this->render('dynamic/dynamic.html.twig', [
            'products' => $products
        ]);

    }

    /**
     * @Route("/fetchby/{limit}/{offset}", name="limit_offset", methods={"GET"})
     */
    public function fetchBy(ProductRepository $repoProduct, int $limit = 1, int $offset = 0): Response
    {
        $products = $repoProduct->findBy(array(), array('id' => 'ASC'), $limit, $offset);

        return $this->render('dynamic/fetchby.html.twig', [
            'products' => $products
        ]);

    }

    /**
     * @Route("/dynamic2", name="dynamic2", methods={"GET"})
     */
    public function dynamic2(ProductRepository $repoProduct): Response
    {
        $limit = 1;
        $offset = 0;

        $products = $repoProduct->filterProductsWith($limit, $offset);
        // $products = $repoProduct->queryProductsWith($limit, $offset);

        return $this->render('dynamic/dynamic2.html.twig', [
            'products' => $products
        ]);

    }

    /**
     * @Route("/fetchby2/{limit}/{offset}", name="limit_offset2", methods={"GET"})
     */
    public function fetchBy2(SerializerInterface $serializer, ProductRepository $repoProduct, int $limit = 1, int $offset = 0): Response
    {
        $products = $repoProduct->filterProductsWith($limit, $offset);
        // $products = $repoProduct->queryProductsWith($limit, $offset);

        $productsBy = $serializer->serialize($products, 'json');

        $response = new Response($productsBy, 200, [
            "Content-Type" => "application/json",
        ]);
        
        return $response;

    }


}
