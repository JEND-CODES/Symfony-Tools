<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    
    /**
     * @Route("/showimagefrom", name="show_image_from")
     */
    public function showImageFrom()/*: Response*/
    {

    //     $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
    //    . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
    //    . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
    //    . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

    //     $data = base64_decode($data);

    //     $image = imagecreatefromstring($data);

        $image = imagecreatefromstring(file_get_contents('http://test.planetcode.fr/images/02.jpg'));

        if ($image !== false) {
            // header('Content-Type: image/png');
            header('Content-Type: image/jpeg');
            // imagepng($image);
            imagejpeg($image);

            // Fonction pour fermer la ressource
            // Libère toute la mémoire associée à l'image
            imagedestroy($image);
        }
        else {
            echo 'Error not found';
        }

        // return new Response(
        //     null, 
        //     200, 
        //     // ['content-type' => 'image/png']
        // );

    }

    /**
     * @Route("/curlimage", name="curl_image", methods={"GET"})
     */
    public function curlImage()/*: Response*/
    {

        $url = "http://test.planetcode.fr/images/02.jpg";

        $ch = curl_init();

        // Automatically update the referer header
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);

        // Pass headers to the data stream
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // True : pour retourner le transfert en tant que chaîne de caractères de la valeur retournée par curl_exec() au lieu de l'afficher directement.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // URL for this transfer
        curl_setopt($ch, CURLOPT_URL, $url);

        // Follow HTTP Redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  

        // Verify the certificate's name
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     

        // Verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $html = curl_exec($ch);

        curl_close($ch);

        // echo $html;

        // Crée une image à partir d'une chaîne
        $image = imagecreatefromstring($html);

        if ($image !== false) {
            header('Content-Type: image/jpeg');
            imagejpeg($image);
            imagedestroy($image);
        }
        else {
            echo 'Error not found';
        }

        // return new Response(
        //     null, 
        //     200, 
        //     // ['content-type' => 'image/png']
        // );

    }

    /**
     * @Route("/echoimagefrom", name="echo_image_from")
     */
    public function echoImageFrom(): Response
    {

        // $imageEncoded = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
        //    . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
        //    . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
        //    . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

        $imageDecoded = base64_encode(file_get_contents('http://test.planetcode.fr/images/02.jpg'));

        echo '<img src="data:image/png;base64,'. $imageDecoded .'" />';

        return new Response(
            'Image décodée !', 
            200, 
            ['content-type' => 'text/html']
        );

    }

    /**
     * @Route("/echojsonimage", name="echo_json_image")
     */
    public function echoJsonImage(): Response
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?gender=male&results=1');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        $json_url = [];
        
        $json_array = json_decode(curl_exec($ch))->results;

        foreach ($json_array as $result) {

            // echo '
            //     <img src="'. $result->picture->large .'" />
            // ';

            array_push($json_url, $result->picture->large);
        }

        // dd($json_url);
        // dd(current($json_url));
        // dd(array_values($json_url)[0]);
        // $imageSource = implode(',', $json_url);

        $imageSource = array_values($json_url)[0];

        $imageDecoded = base64_encode(file_get_contents($imageSource));

        echo '<img src="data:image/png;base64,'. $imageDecoded .'" />';

        return new Response(
            'Image Json décodée !', 
            200, 
            ['content-type' => 'text/html']
        );

    }

    /**
     * @Route("/copyimage", name="copy_image")
     */
    public function copyImage(ParameterBagInterface $parameterBagInterface): Response
    {
        // $webPath = $parameterBagInterface->get('webDir');
        // $webPath = $parameterBagInterface->get('webDir') . '/build';
        // Renvoie : C:\wamp64\www\newsymfotest\public
        // dd($webPath);

        // $projectDir = $this->getParameter('kernel.project_dir');
        // $projectDir = $this->getParameter('kernel.project_dir') . '/public';
        // $projectDir = $this->getParameter('kernel.project_dir') . '/public/build';
        // dd($projectDir);
        // dd(scandir($projectDir));

        // $file = 'C:\wamp64\www\newsymfotest\rouge.jpg';

        // $data = file_get_contents($file);

        // $newImage = 'C:\wamp64\www\newsymfotest\newimage.jpg';

        // file_put_contents($newImage, $data);

        $file = $this->getParameter('kernel.project_dir') . '/public/pictures/picture.jpg';

        $data = file_get_contents($file);

        // $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/new-picture.jpg';

        // $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/' . md5(uniqid()) . '.' . basename($file) . '';

        // $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/' . time().uniqid(rand()).basename($file) . '';

        $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/' . uniqid(). '-' . basename($file) . '';

        file_put_contents($newImage, $data);

        return new Response(
            'Image copiée et renommée !', 
            200, 
            ['content-type' => 'text/html']
        );

    }

    /**
     * @Route("/uploadimage", name="upload_image")
     */
    public function uploadImage(): Response
    {
        $imageSource = 'http://test.planetcode.fr/images/02.jpg';

        $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/' . uniqid(). '.jpg';

        file_put_contents($newImage, file_get_contents($imageSource));

        return new Response(
            'Image copiée et enregistrée !', 
            200, 
            ['content-type' => 'text/html']
        );

    }

    /**
     * @Route("/addimagetoproduct", name="add_image_to_product", methods={"GET", "POST"})
     */
    public function addImageToProduct(ClientRepository $repoClient, EntityManagerInterface $manager): Response
    {

        // Étapes qui permettraient, à partir d'une URL absolue renseignée dans un formulaire, de télécharger l'image choisie par l'utilisateur, puis d'associer un chemin relatif vers cette image (URL relative - image copiée par exemple dans le dossier /public)

        $imageSource = 'http://test.planetcode.fr/images/02.jpg';

        $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/product.jpg';

        file_put_contents($newImage, file_get_contents($imageSource));

        $client = $repoClient->find(1);

        $product = new Product();

        $product->setTitle('ProductX')
                ->setDescription('ProductDescription')
                ->setPrice(120.15)
                ->setPicture('/pictures/product.jpg')
                ->setCreatedAt(new \DateTime())
                ->setClient($client)
                ;
        
        $manager->persist($product);

        $manager->flush();

        return new Response(
            'Image associée à un produit !', 
            200, 
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/showimageofproduct", name="show_image_of_product", methods={"GET"})
     */
    public function showImageOfProduct(ProductRepository $repoProduct): Response
    {

        $product = $repoProduct->find(1);

        // dd($product->getPicture());
        echo '<img src="'. $product->getPicture() .'" />';

        return new Response(
            'Produit avec image !', 
            200, 
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/newproduct", name="new_product", methods={"GET", "POST"})
     */
    public function newProduct(Product $product = null, Request $request, ClientRepository $repoClient, EntityManagerInterface $manager): Response
    {
        $client = $repoClient->find(1);

        $product = new Product();

        $createProduct = $this->createForm(ProductType::class, $product);

        $createProduct->handleRequest($request);

        if($createProduct->isSubmitted() && $createProduct->isValid())
        {
            $product->setCreatedAt(new \DateTime());

            $product->setClient($client);

            $manager->persist($product);

            // Inspection des données du formulaire
            $data = $createProduct->getData();

            // On spécifie une donnée précise en appelant la méthode définie dans l'entité Product - ex: $data->getPicture()

            // dd(
            //     // $data,
            //     $data->getTitle(),
            //     $data->getPicture()
            // );

            // On récupère donc l'url de l'image, pour faire ensuite une copie de cette image dans le dossier /public/pictures/... en lui ajoutant un nouveau nom, assorti d'un identifiant unique
            $imageSource = $data->getPicture();

            // Autre manière d'utiliser getData() sans spécifier le getter de l'entité :
            // $pictureSource = $createProduct->get('picture')->getData();
            // dd($pictureSource);

            $imageName = ''. $data->getTitle() .'-' . uniqid(). '.jpg';

            $newImage = $this->getParameter('kernel.project_dir') . '/public/pictures/'. $imageName .'';

            file_put_contents($newImage, file_get_contents($imageSource));

            $product->setPicture('pictures/'. $imageName .'');

            $manager->flush();

            return $this->redirectToRoute('home',[
            ]);

        }

        return $this->render('product/product.html.twig', [
            'createProduct' => $createProduct->createView()
        ]);
    }

    /**
     * @Route("/deleteimage", name="delete_image")
     */
    public function deleteImage(ProductRepository $repoProduct, EntityManagerInterface $manager): Response
    {
        $product = $repoProduct->find(21);
        
        $imageProduct = $product->getPicture();

        $fileName = str_replace('pictures/', '', $imageProduct); 

        // dd($fileName);
        // $projectDir = $this->getParameter('kernel.project_dir') . '/public/pictures/';
        // dd(scandir($projectDir));
        // $files = scandir($projectDir);
        // dd($files);

        $filePath = $this->getParameter('kernel.project_dir') . '/public/pictures/' . $fileName;

        if (file_exists($filePath)) {
            
            // dd(
            //     $fileName,
            //     $filePath
            // );

            // Suppression du fichier
            unlink($filePath);

            // + Valeur nulle en base de données
            $product->setPicture(null);

            $manager->persist($product);

            $manager->flush();

        } /* else {

            dd('file not found');
            
        } */

        return new Response(
            'Image du produit supprimée !', 
            200, 
            ['content-type' => 'text/html']
        );
    }
   
}