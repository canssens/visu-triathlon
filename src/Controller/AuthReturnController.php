<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;


class AuthReturnController extends AbstractController
{
    public function callback(Request $request)
    {

        if(null !== $request->query->get('code') && strlen($request->query->get('code'))>1 )
        {
            $code = $request->query->get('code');
            $url = 'https://account.geonaute.com/oauth/accessToken?client_id='.getenv('GEONAUTEACCOUNT_CLIENTID').'&redirect_uri='.getenv('HOST_URI_CALLBACK').'&client_secret='.getenv('GEONAUTEACCOUNT_SECRET').'&grant_type=authorization_code&code='.$code;


            $ch = curl_init(); 
            // set url 
            curl_setopt($ch, CURLOPT_URL, $url); 
            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            // $output contains the output string 
            $output = curl_exec($ch); 
            // close curl resource to free up system resources 
            curl_close($ch);      

            $data = json_decode($output, true);
            if(null !== $data['access_token'])
            {
                $session = new Session();
                $session->start();
                $session->set('access_token', $data['access_token']);

                return $this->redirectToRoute('display');
            }

            

            return new Response(
                '<html><body>Oops KO</body></html>'
            );
        }
        

        return new Response(
            '<html><body>Oops '.$request->query->get('code').'</body></html>'
        );
        

        

    }
}