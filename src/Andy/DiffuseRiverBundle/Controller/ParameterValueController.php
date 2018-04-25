<?php

namespace Andy\DiffuseRiverBundle\Controller;


use Andy\DiffuseRiverBundle\Classes\DiffuseMethods;
use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Andy\DiffuseRiverBundle\Entity\ParamValue;
use Andy\DiffuseRiverBundle\Entity\Result;
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

        $em = $this->getDoctrine()->getManager();

        // Выводим значения параметров текущей точки
        $arParameter = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
            ->getValueFromPointAndParameter($point, $parameter);

        // Получаем значения Расхода поды (Q) для постороения графика
        $arParamQ = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
            ->getValueByCodeParameter($point, 'Q');

        // Получаем результаты сделанных ранее расчетов
        $arResults = $em->getRepository('AndyDiffuseRiverBundle:Result')
            ->getResult(
                $project->getId(),
                $point->getId(),
                $parameter->getId()
            );


        $X = array();
        $Y = array();
        $YnoSort = array();
        $Yfit = array();
        $n = 0;


        // Перебираем полученные параметра
        foreach ($arParameter as $keyParameter => $itemParameter) {
            // Перебираем данные параметра Q
            foreach ($arParamQ as $keyParamQ => $itemParamQ) {
                // Если даты совпадают, вычисляем, иначе выводим ошибку
                if ($itemParameter['date'] == $itemParamQ['date']) {

                    // Если парметр и Q имеют -1, то не вносим в график
                    if (current($itemParameter)->getValue() == -1 || $itemParamQ['value'] == -1) {
                        $n++;
                        continue;
                    } else {
                        $Y[] = (double)current($itemParameter)->getValue();
                        $X[] = (double)$itemParamQ['value'];

                    }

                }
            }
        }

        // Если массивы сформированы
        if (!empty($X) && !empty($Y)) {

            // Сортируем X по возрастанию, сохраняя ключи
            asort($X);

            // Теперь конвертируем в лог-шкалу для X
            $logX = array_map('log', $X);

            // Находим логарифмическую линию тренда
            $n = count($X);

            function square ($x) {
                return pow($x,2);
            }

            function multiply ($x, $y) {
                return $x * $y;
            }

            $squaredLogX = array();
            $xMultiplyY = array();
            foreach ($logX as $keyLogX => $itemLogX) {
                $squaredLogX[] = square($itemLogX);
                $xMultiplyY[] = multiply($itemLogX, $Y[$keyLogX]);
            }

            $x_squared = array_sum($squaredLogX);
            $xy = array_sum($xMultiplyY);

            $bFit = ($n * $xy - array_sum($Y) * array_sum($logX)) /
                ($n * $x_squared - pow(array_sum($logX), 2));

            $aFit = (array_sum($Y) - $bFit * array_sum($logX)) / $n;

            foreach($X as $x) {
                $Yfit[] = $aFit + $bFit * log($x);
            }

        }


        return $this->render('@AndyDiffuseRiver/ParameterValue/show.html.twig', array(
            'arParameter' => $arParameter,
            'arParamQ' => $arParamQ,
            'parameter' => $parameter,
            'point' => $point,
            'project' => $project,
            'diffuseMethod' => DiffuseMethods::typeMethods, // Получение методов рассчета
            'results' => $arResults,
            'X' => $X,
            'Y' => $Y,
            'Yfit' => $Yfit
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

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($param_value);
            $em->flush();
        }

        $parameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->find($param_value->getParameterId());

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'POST') {
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
    public function deleteAllAction(Request $request, Project $project, Point $point, Parameter $parameter)
    {

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();

            // Выводим значения параметров текущей точки
            $delParameter = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
                ->getValueFromPointAndParameter($point, $parameter);


            if (!empty($delParameter)) {

                foreach ($delParameter as $itemDel) {
                    // Делаем проверку на отсавшееся количество значений параметров
                    $paramDateValue = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
                        ->findBy(
                            array(
                                'paramDateId' => $itemDel['id']
                            )
                        );
                    // Если остался один параметр, то удаляем и ParamDate
                    if (count($paramDateValue) == 1) {
                        $paramDate = $em->getRepository('AndyDiffuseRiverBundle:ParamDate')
                            ->find($itemDel['id']);
                        $em->remove($paramDate);
                    }

                    $em->remove($itemDel[0]);
                }

                $arResults = $em->getRepository('AndyDiffuseRiverBundle:Result')->findBy(array(
                   'projectId'      => $project->getId(),
                   'pointId'        => $point->getId(),
                   'parameterId'    => $parameter->getId()
                ));

                foreach ($arResults as $itemResult) {
                    $em->remove($itemResult);
                }

                $em->flush();
            }
        }

        return $this->redirectToRoute('point_show',
            array(
                'project' => $project->getId(),
                'id' => $point->getId()
            )
        );
    }

    public function deleteResultAction (Request $request, Project $project, Point $point, Parameter $parameter, Result $result) {

        // Удалить по клику в общем списке
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($result);
            $em->flush();
        }


        return $this->redirectToRoute('parameter_value_show', array(
            'id' => $point->getId(),
            'project' => $project->getId(),
            'parameter' => $parameter->getId()));
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
