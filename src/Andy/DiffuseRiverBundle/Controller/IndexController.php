<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AndyDiffuseRiverBundle:Project')->findAll();

        return $this->render('@AndyDiffuseRiver/Index/index.html.twig', array(
            'projects' => $projects,
        ));
    }
}
