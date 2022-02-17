<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Service\RequestService;
use App\Utils\Delay;
use App\Utils\Dumps;
use App\Validator\CustomConstraint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(
        ClientRepository $repoClient, 
        ProductRepository $repoProduct, 
        Dumps $dumps, 
        RequestService $requestService, 
        $_route, 
        Request $request, 
        Delay $delay,
        CustomConstraint $customConstraint
        ): Response
    {
        //*** DUMP CLIENTS : FIND ALL
        $clients = $repoClient->findBy(array(), array('id' => 'ASC'));
        // $clients = $repoClient->findAll();
        // dd($clients);

        //*** RAW SQL QUERIES
        // $fetchClients = $repoClient->rawSqlQuery();
        // $fetchProducts = $repoProduct->rawSqlQuery();

        //*** DUMPS TESTS
        // $dumps->dumpByCriteriaInArray($clients, 'name');

        // $dumps->dumpGreaterThanInArray($clients, 'name', 7);

        // $dumps->dumpLessThanInArray($clients, 'name', 7);

        // $dumps->dumpIfSpecialCharactersInArray($clients, 'name');

        // $dumps->dumpInRelationMethodInArray($clients, 'theme', 'name');

        //*** DUMP CLIENT : FIND BY ID
        $oneClient = $repoClient->find(1);
        // dd($oneClient);

        // $dumps->dumpLessThan($oneClient, 'name', 24);

        // $dumps->dumpGreaterThan($oneClient, 'name', 4);

        // $dumps->dumpIfSpecialCharacters($oneClient, 'name');

        // $dumps->dumpInRelationMethod($oneClient, 'theme', 'name');

        //*** FILTERED CLIENTS LIST BY IDmin & IDmax
        $clientsList = $repoClient->filterClientsByIdMinAndMax(1, 3);

        //*** FILTERED CLIENTS LIST BY THEME NAME
        $clientsByThemeName = $repoClient->filterClientsByThemeName('Template1');

        //*** FILTERED CLIENTS LIST BY THEME ID
        $clientsByThemeId = $repoClient->filterClientsByThemeId(1);

        //*** DUMP PRODUCTS
        $oneProduct = $repoProduct->find(2);
        // dd($oneProduct);

        // $dumps->dumpLessThan($oneProduct, 'title', 24);

        // $dumps->dumpGreaterThan($oneProduct, 'title', 4);

        // $dumps->dumpIfSpecialCharacters($oneProduct, 'title');

        // $dumps->dumpInRelationMethod($oneProduct, 'client', 'name');

        $products = $repoProduct->findAll();
        // dd($products);

        // $dumps->dumpByCriteriaInArray($products, 'title');

        // $dumps->dumpGreaterThanInArray($products, 'title', 7);

        // $dumps->dumpLessThanInArray($products, 'title', 12);

        // $dumps->dumpIfSpecialCharactersInArray($products, 'title');

        // $dumps->dumpInRelationMethodInArray($products, 'client', 'name');

        //*** SERVICE POUR RÉCUPÉRER DES INFOS SUR LA REQUÊTE EN COURS
        // $requestInfo = $requestService->getRouteName();
        // $requestInfo = $requestService->getController();
        // $requestInfo = $requestService->getSessionInfos();
        // $requestInfo = $requestService->getUriInfo();
        // $requestInfo = $requestService->getPortInfo();
        // dd($requestInfo);

        // dd($_route);
        // dd($request->getUri());
        // dd($request->getPort());

        //*** FAIRE UN AUDIT DU TEMPS D'AFFICHAGE DE LA PAGE D'ACCUEIL
        // $delay->audit($request->server->get('REQUEST_TIME_FLOAT'), microtime(true));

        //*** CHECK VARIABLE WITH CUSTOM CONSTRAINT
        // $john = ['John'];
        // $john = 'John';
        // $customConstraint->validateString($john);
        // $customConstraint->validateArray($john);

        return $this->render('home/index.html.twig', [
            'clients' => $clients,
            'one_client' => $oneClient,
            'clients_list' => $clientsList,
            'clients_theme_name' => $clientsByThemeName,
            'clients_theme_id' => $clientsByThemeId
        ]);

    }

    /**
     * @Route("/watchhome", name="watch_home")
     */
    public function watchHomeQueries(ClientRepository $repoClient, Delay $delay)
    {
        $stopWatch = new Stopwatch();

        $stopWatch->start('watchHomeQueries');

        // sleep(1);

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

}
