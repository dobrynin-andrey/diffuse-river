<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CalculateController extends Controller
{
    public function indexAction(Request $request)
    {

        $data = array();
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        $em = $this->getDoctrine()->getManager();
        $points = $em->getRepository(Point::class)->findBy(array(
            'id' =>   $data
        ));
        return $this->json(json_encode($data));
    }
}


