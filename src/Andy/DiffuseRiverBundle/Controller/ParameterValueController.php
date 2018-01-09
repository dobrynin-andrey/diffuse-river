<?php

namespace Andy\DiffuseRiverBundle\Controller;


use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Andy\DiffuseRiverBundle\Entity\ParamValue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * ParameterValue controller.
 *
 */
class ParameterValueController extends Controller
{

    /**
     * Finds and displays a parameterValue entity.
     *
     */
    public function showAction(Request $request, Project $project, Point $point, Parameter $parameter)
    {

        // Выводим значения параметров текущей точки

        $em = $this->getDoctrine()->getManager();

        $arParameter = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')->findBy(
            array('parameterId' => $parameter->getId())
        );

        return $this->render('@AndyDiffuseRiver/ParameterValue/show.html.twig', array(
            'arParameter' => $arParameter,
            'parameter' => $parameter,
            'point' => $point,
            'project' => $project,
        ));

    }


    /**
     * Displays a form to edit an existing parameterValue entity.
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
     * Deletes a parameterValue entity.
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

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();

            $delParameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->find($parameter);
            $em->remove($delParameter);
            $em->flush();
        }


        return $this->redirectToRoute('parameter_index');
    }

    /**
     * Deletes all parameterValue entity.
     *
     */
    public function delete_allAction(Request $request)
    {

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();

            $delParameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findAll();
            if ($delParameter) {
                foreach ($delParameter as $itemDel) {
                    $em->remove($itemDel);
                }

                $em->flush();
            }


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
