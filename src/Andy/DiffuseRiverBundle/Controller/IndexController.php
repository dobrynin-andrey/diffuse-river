<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {


        // Вывести все прокты
        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AndyDiffuseRiverBundle:Project')->findAll();


        // Форма создания нового проекта

        $project = new Project();
        $form = $this->createForm('Andy\DiffuseRiverBundle\Form\ProjectType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_show', array('id' => $project->getId()));
        }

        return $this->render('@AndyDiffuseRiver/Index/index.html.twig',  array(
            'projects' => $projects,
            'project' => $project,
            'form' => $form->createView(),
        ));
    }
}
