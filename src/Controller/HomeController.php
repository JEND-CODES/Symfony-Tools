<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Utils\Dumps;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * @param ClientRepository $repoClient
     * @return Response
     */
    public function index(ClientRepository $repoClient, Dumps $dumps): Response
    {
        //*** FIND ALL
        $clients = $repoClient->findBy(array(), array('id' => 'ASC'));
        // $clients = $repoClient->findAll();
        // dd($clients);

        // $dumps->dumpByCriteriaInArray($clients, 'name');

        // $dumps->dumpGreaterThanInArray($clients, 'name', 7);

        // $dumps->dumpLessThanInArray($clients, 'name', 7);

        // $dumps->dumpIfSpecialCharactersInArray($clients, 'name');

        // $dumps->dumpInRelationMethodInArray($clients, 'theme', 'name');

        //*** FIND BY ID
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

        return $this->render('home/index.html.twig', [
            'clients' => $clients,
            'one_client' => $oneClient,
            'clients_list' => $clientsList,
            'clients_theme_name' => $clientsByThemeName,
            'clients_theme_id' => $clientsByThemeId
        ]);
    }
}
