<?php

namespace Andy\DiffuseRiverBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction (Request $request) {
        return $this->render("@AndyDiffuseRiver/About/index.html.twig");
    }


}