<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Point controller.
 *
 */
class PointController extends Controller
{


    /**
     * Finds and displays a point entity.
     *
     */
    public function showAction(Project $project, Point $point)
    {
        $deleteForm = $this->createDeleteForm($project, $point);

        return $this->render('@AndyDiffuseRiver/Point/show.html.twig', array(
            'project' => $project,
            'point' => $point,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing point entity.
     *
     */
    public function editAction(Request $request, Project $project, Point $point)
    {
        $deleteForm = $this->createDeleteForm($project, $point);
        $editForm = $this->createForm('Andy\DiffuseRiverBundle\Form\PointType', $point);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('point_edit', array('project' => $project->getId(),'id' => $point->getId()));
        }

        return $this->render('@AndyDiffuseRiver/Point/edit.html.twig', array(
            'point' => $point,
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a point entity.
     *
     */
    public function deleteAction(Request $request, Project $project, Point $point)
    {
        $form = $this->createDeleteForm($project ,$point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($point);
            $em->flush();
        }

        return $this->redirectToRoute('project_show', array('id' => $project->getId()));
    }

    /**
     * Creates a form to delete a point entity.
     *
     * @param Point $point The point entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $project, Point $point)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('point_delete', array('project' => $project->getId(), 'id' => $point->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
