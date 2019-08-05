<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;


class DisplayDataController extends AbstractController
{

    private $jwt = "";

    public function display()
    {
        
        $session = new Session();
        $session->start();
        $access_token = $session->get('access_token');

        //get JWT
        $this->jwt = $this->getJwt($access_token);
        //get activities
        //startdate last 12 weeks
        $list_activities = $this->getActivities();
        $tab = $this->getCalendar($list_activities);



        return $this->render('displaydata.html.twig', [
                    'name' => sizeof($tab),
                    'data' => $tab
               ]);
    }



    private function getJwt($at)
    {
        $url = 'https://account.geonaute.com/api/me?access_token='.$at;
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
        if(null !== $data['requestKey'])
        {
            return $data['requestKey'];
        }
        else return null;
    }

    private function getCalendar($list)
    {
        $actual_week = $today = date("W"); 
        $calendar = array();
        for($i=12;$i>=0;$i--)
        {
            $calendar["week-".$i] = array("run" => 0,"cycle" =>0,"swim" =>0);
        }
        foreach($list as $activity)
        {
            
           $y = ($actual_week-date("W", strtotime($activity['startdate'])));

           if($y<13)
           {
                if($activity['sport'] == "/v2/sports/121") $calendar["week-".$y]["run"] += ($activity['duration'])/60;
                if($activity['sport'] == "/v2/sports/385") $calendar["week-".$y]["cycle"] += ($activity['duration'])/60;
                if($activity['sport'] == "/v2/sports/274") $calendar["week-".$y]["swim"] += ($activity['duration'])/60;
            }
        }

        return $calendar;
    }

    private function getActivities()
    {
        $today = date("d-m-Y"); 

        $day_number = date('N', strtotime(date("d/m/Y")));
        $start_day = 12 * 7 + $day_number;
        $start_date = date("Y-m-d", strtotime("-".$start_day." day"));
        
        //date('Y-m-d', strtotime('-".$start_day." days', strtotime($today)));
        $list_activities = array();
        
        //startdate[after]=2013-03-09
        //&sport[]=121&sport[]385&sport[]274
        $page = 1;
        while($page > 0)
        {
            $url = getenv('STD_ENDPOINT').'/v2/activities?startdate[after]='.$start_date."&sport[]=121&sport[]=385&sport[]=274&page=".$page;
            $ch = curl_init(); 
            // set url 
            curl_setopt($ch, CURLOPT_URL, $url); 
            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer ". $this->jwt,
                "x-api-key: ".getenv('STD_API_KEY')
            ));


            // $output contains the output string 
            $output = curl_exec($ch); 
            // close curl resource to free up system resources 
            curl_close($ch);      

            $data = json_decode($output, true);


            foreach ($data['hydra:member'] as $activity)
            {
                $val = array();
                $val['duration'] = $activity["duration"];
                $val['sport'] = $activity["sport"];
                $val['startdate'] = $activity["startdate"];
                $list_activities[] = $val;
            }


            if(isset($data['hydra:view']['hydra:next'])) $page++;
            else $page=0;

        }

        return $list_activities;
    }


}