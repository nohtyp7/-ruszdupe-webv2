<?php

namespace WykopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class LastDistanceController extends Controller
{
    /**
     * @Route("/lastDistances/{id_tag}")
     */
    public function getDistancesAction($id_tag){
	
	$em = $this->getDoctrine()->getManager();
	
	$distances = $em->getRepository('WykopBundle:LastDistance')->getByTag($id_tag);
	$response = new Response(json_encode($distances), 200);
	$response->headers->set('Content-Type', 'application/json');
	return $response;
    }
}