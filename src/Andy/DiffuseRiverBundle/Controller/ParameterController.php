<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parameter controller.
 *
 */
class ParameterController extends Controller
{
    /**
     * Lists all parameter entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findAll();

        return $this->render('@AndyDiffuseRiver/Parameter/index.html.twig', array(
            'parameters' => $parameters,
        ));
    }

    /**
     * Creates a new parameter entity.
     *
     */
    public function newAction(Request $request)
    {
        $parameter = new Parameter();
        $form = $this->createForm('Andy\DiffuseRiverBundle\Form\ParameterType', $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('parameter_show', array('id' => $parameter->getId()));
        }

        return $this->render('@AndyDiffuseRiver/Parameter/new.html.twig', array(
            'parameter' => $parameter,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parameter entity.
     *
     */
    public function showAction(Parameter $parameter)
    {
        $deleteForm = $this->createDeleteForm($parameter);

        return $this->render('@AndyDiffuseRiver/Parameter/show.html.twig', array(
            'parameter' => $parameter,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parameter entity.
     *
     */
    public function editAction(Request $request, Parameter $parameter)
    {
        $deleteForm = $this->createDeleteForm($parameter);
        $editForm = $this->createForm('Andy\DiffuseRiverBundle\Form\ParameterType', $parameter);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parameter_edit', array('id' => $parameter->getId()));
        }

        return $this->render('@AndyDiffuseRiver/Parameter/edit.html.twig', array(
            'parameter' => $parameter,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parameter entity.
     *
     */
    public function deleteAction(Request $request, Parameter $parameter)
    {
        $form = $this->createDeleteForm($parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parameter);
            $em->flush();
        }

        return $this->redirectToRoute('parameter_index');
    }

    /**
     * Creates a form to delete a parameter entity.
     *
     * @param Parameter $parameter The parameter entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parameter $parameter)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parameter_delete', array('id' => $parameter->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
