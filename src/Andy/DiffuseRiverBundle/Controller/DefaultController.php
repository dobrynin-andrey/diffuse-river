<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@AndyDiffuseRiver/Default/index.html.twig');
    }
}
