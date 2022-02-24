<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScrapingController extends AbstractController
{
    /**
     * @Route("/scraping", name="scraping", methods={"GET"})
     */
    public function scraping(): Response
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://test.planetcode.fr/");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $html = curl_exec($ch);

        curl_close($ch);

        // https://symfony.com/doc/current/components/dom_crawler.html
        $crawler = new Crawler($html);

        //*** 1. ATTRIBUTES

        // $attributes = $crawler
        //             ->filterXpath('//body/div')
        //             ->extract(['_name', '_text', 'class'])
        //             ;

        // $attributes = $crawler
        //             ->filter('body')
        //             ->extract(['_name', '_text', 'class'])
        //             ;

        // dd($attributes);

        //*** 2. FILTER

        // $scraped = $crawler->filter('h2')->eq(1)->text();
        // $scraped = $crawler->filter('p')->eq(1)->text();

        // $test = $crawler->filter('h2')->eq(0)->text();
        // dd($test);
        
        //*** 2. LOOP

        // $scraped = $crawler->filter('p')->each(function (Crawler $node, $i) {
        //     return $node->text();
        // });

        // $scraped = $crawler->filter('h2')->each(function (Crawler $node, $i) {
        //     return $node->text();
        // });

        //*** RÉCUPÈRE LE CONTENU DES LIENS SRC="LINK" DE TOUTES LES IMAGES
        $scraped = $crawler->filter('.grid img')->each(function (Crawler $node, $i) {
            return $node->extract(array('src'));
        });

        // dd($scraped);

        // CONVERT MULTIDIMENSIONAL ARRAY INTO SINGLE ARRAY
        $simpleArray = implode(',', array_map(function($el) { 
            return $el[0]; 
        }, $scraped));

        $imgArray = explode(',', $simpleArray);

        // Autre méthode plus simple pour convertir un tableau multidimensionnel (à condition de ne récupérer que les premières valeurs) :
        // $values = array_map('current', $scraped);
        // dd($values);

        foreach($imgArray as $imgSrc){

            echo '
                <img src="http://test.planetcode.fr/'. $imgSrc .'" />
            ';
   
        }

        return $this->render('scraping/scraping.html.twig', [
            // 'scraped' => $scraped
        ]);

    }

    /**
     * @Route("/exportcsv", name="export_csv")
     */
    public function exportCsv(): Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://test.planetcode.fr/");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $html = curl_exec($ch);

        curl_close($ch);

        $crawler = new Crawler($html);

        $imgScraped = $crawler->filter('.grid img')->each(function (Crawler $node, $i) {
            return $node->extract(array('src'));
        });

        $fp = fopen('php://temp', 'w');

        foreach ($imgScraped as $fields) {

            fputcsv($fp, $fields);

        }

        rewind($fp);

        $response = new Response(stream_get_contents($fp));

        fclose($fp);

        $response->headers->set('Content-Type', 'text/csv');

        $response->headers->set('Content-Disposition', 'attachment; filename="test.csv"');

        return $response;

    }

    /**
     * @Route("/scrapingtable", name="scraping_table")
     */
    public function scrapingTable()
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://fr.wikipedia.org/wiki/Langues_en_Europe");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $html = curl_exec($ch);

        curl_close($ch);

        // https://symfony.com/doc/current/components/dom_crawler.html
        $crawler = new Crawler($html);

        // $table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
        //     return $tr->filter('td')->each(function ($td, $i) {
        //         return trim($td->text());
        //     });
        // });

        // dd($table);

        $tableElements = $crawler->filter("table tr");

        foreach ($tableElements as $i => $content) {
            
            // Ici on pourrait limiter les résultats sur la longueur du tableau en utilisant une boucle for :
            // if($i < 12) { ...
            // Cela permettrait de ne prendre que les 12 premières lignes du tableau

            $tds = array();

            // CREATE CRAWLER INSTANCE FOR RESULT
            $crawler = new Crawler($content);

            // ITERATE AGAIN
            foreach ($crawler->filter('td') as $i => $node) {

                // EXTRACT THE VALUE
                // Pour récupérer l'ensemble des résultats, pour toutes les colonnes :
                // $tds[] = $node->nodeValue;

                // Pour limiter les résultats aux deux premières colonnes du tableau : 
                if($i < 2) {
                    $tds[] = $node->nodeValue;
                }
    
            }
            $rows[] = $tds;
    
        }

        dd($rows);

    }

}
