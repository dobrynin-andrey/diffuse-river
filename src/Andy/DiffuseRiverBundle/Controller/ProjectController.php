<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Project;
use Andy\DiffuseRiverBundle\Entity\Point;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project controller.
 *
 */
class ProjectController extends Controller
{

    /**
     * Finds and displays a project entity.
     *
     */
    public function showAction(Request $request, Project $project)
    {

        // Вывести все точки проекта
        $em = $this->getDoctrine()->getManager();

        $points = $em->getRepository('AndyDiffuseRiverBundle:Point')->findBy(array(
            'projectId' => $project->getId()
        ));


        // Форма создания новой точки

        $point = new Point();
        $point->setProjectId((int)$project->getId());
        $form = $this->createForm('Andy\DiffuseRiverBundle\Form\PointType', $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($point);
            $em->flush();

            return $this->redirectToRoute('project_show', array('id' => $project->getId()));
        }

        // Проверка есть ли в базе параметры
        $em = $this->getDoctrine()->getManager();

        $parameters = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findAll();

        if (empty($parameters))
            $parameters = false;
        else
            $parameters = true;

        return $this->render('@AndyDiffuseRiver/Project/show.html.twig',  array(
            'points' => $points,
            'project' => $project,
            'parameters' => $parameters,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing project entity.
     *
     */
    public function editAction(Request $request, Project $project)
    {
        $deleteForm = $this->createDeleteForm($project);
        $editForm = $this->createForm('Andy\DiffuseRiverBundle\Form\ProjectType', $project);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_edit', array('id' => $project->getId()));
        }

        return $this->render('@AndyDiffuseRiver/Project/edit.html.twig', array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a project entity.
     *
     */
    public function deleteAction(Request $request, Project $project)
    {
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('andy_diffuse_river_homepage');
    }

    /**
     * Creates a form to delete a project entity.
     *
     * @param Project $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
