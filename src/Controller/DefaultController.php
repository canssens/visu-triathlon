<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function number()
    {

        $link_login = "https://account.geonaute.com/oauth/authorize?response_type=code&client_id=".getenv('GEONAUTEACCOUNT_CLIENTID')."&redirect_uri=".getenv('HOST_URI_CALLBACK');


        return $this->render('index.html.twig', [
                    'linklogin' => $link_login,
               ]);

    }
}