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
     * Creates a new parameterValue entity.
     *
     */
    public function newAction(Request $request, Project $project, Point $point)
    {

        // Форма ввода нового параметра вручную
        $parameter = new ParamValue();
        $form = $this->createForm('Andy\DiffuseRiverBundle\Form\ParameterValueType', $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('parameter_index');
        }

        return $this->render('@AndyDiffuseRiver/Parameter/new.html.twig', array(
            'parameter' => $parameter,
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a parameterValue entity.
     *
     */
    public function showAction(Request $request, Project $project, Point $point, Parameter $parameter)
    {

        // Выводим значения параметров текущей точки

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT pv, pd.date
            FROM AndyDiffuseRiverBundle:ParamValue pv
            JOIN AndyDiffuseRiverBundle:ParamDate pd
            WHERE pv.parameterId = :parameter 
            AND pv.paramDateId = pd.id
            ORDER BY pd.date ASC'
        )->setParameter('parameter', $parameter);

        $arParameter = $query->getResult();

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
    public function editAction(Request $request, Project $project, Point $point, ParamValue $param_value)
    {

        $deleteForm = $this->createDeleteForm($project, $point, $param_value);

        $editForm = $this->createForm('Andy\DiffuseRiverBundle\Form\ParamValueType', $param_value);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parameter_value_show', array('id' => $point->getId(), 'project' => $project->getId(), 'parameter' => $param_value->getParameterId()));
        }

        $em = $this->getDoctrine()->getManager();

        $parameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->find($param_value->getParameterId());

        return $this->render('@AndyDiffuseRiver/ParameterValue/edit.html.twig', array(
            'project' => $project,
            'point' => $point,
            'parameter' => $parameter,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parameterValue entity.
     *
     */
    public function deleteAction(Request $request,Project $project, Point $point, ParamValue $param_value)
    {

        $form = $this->createDeleteForm($project, $point, $param_value);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($param_value);
            $em->flush();
        }

        $parameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->find($param_value->getParameterId());

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($param_value);
            $em->flush();
        }


        return $this->redirectToRoute('parameter_value_show', array(
            'id' => $point->getId(),
            'project' => $project->getId(),
            'parameter' => $parameter->getId()));
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

            $delParameter = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')->findAll();
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
    private function createDeleteForm(Project $project, Point $point, ParamValue $param_value)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parameter_value_delete', array('project' => $project->getId(),'id' => $point->getId(), 'param_value' => $param_value->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
