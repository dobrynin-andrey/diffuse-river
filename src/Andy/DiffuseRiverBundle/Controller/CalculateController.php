<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Classes\DiffuseMethods;
use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Andy\DiffuseRiverBundle\Controller\PointController;

use Andy\DiffuseRiverBundle\Entity\Result;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CalculateController
 * @package Andy\DiffuseRiverBundle\Controller
 */

class CalculateController extends Controller
{
    public function indexAction(Request $request)
    {

        if ($request->isMethod('post')) {

            $method = $request->get('method');

            $em = $this->getDoctrine()->getManager();

            $project = $point = $parameter = '';

            if (!empty($request->get('project'))) {
                $project = $em->getRepository('AndyDiffuseRiverBundle:Project')
                    ->find($request->get('project'));
            }

            if (!empty($request->get('point'))) {
                $point = $em->getRepository('AndyDiffuseRiverBundle:Point')
                    ->find($request->get('point'));
            }

            if (!empty($request->get('parameter'))) {
                $parameter = $em->getRepository('AndyDiffuseRiverBundle:Parameter')
                    ->find($request->get('parameter'));
            }

            try {

                if (empty($method) || $method == '0')
                    throw new Exception('Не выбран алгоритм расчета!');

                if (!in_array($method, DiffuseMethods::typeMethods))
                    throw new Exception('Выбранный метод отсутствует!');

                // Выводим значения параметров текущей точки
                $arParameter = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
                    ->getValueFromPointAndParameter($point, $parameter);

                // Получаем значения Расхода поды (Q)
                $arParamQ = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')
                    ->getValueByCodeParameter($point, 'Q');


                switch ($method) {
                    case 'A':

                        /**
                         *  Вычисление L0 и Kd по формулам Случай A
                         */

                        // Вводим переменные для вычислений
                        $sumXmultiplyK = 0;
                        $sumX = 0;
                        $sumK = 0;
                        $sumSquareX = 0;
                        $n = 0;


                        // находим m (количество элементов)
                        $mParameter = count($arParameter);

                        // Перебираем полученные параметра
                        foreach ($arParameter as $keyParameter => $itemParameter) {
                            // Перебираем данные параметра Q
                            foreach ($arParamQ as $keyParamQ => $itemParamQ) {
                                // Если даты совпадают, вычисляем, иначе выводим ошибку
                                if ($itemParameter['date'] == $itemParamQ['date']) {

                                    /*
                                     *  X = 1/Q
                                     */

                                    if (isset($X))
                                        unset($X);

                                    if (isset($K))
                                        unset($K);


                                    if (current($itemParameter)->getValue() == -1) {
                                        $n++;
                                        continue;
                                    } else {
                                        $K = (double)current($itemParameter)->getValue();
                                        $X = (double)1/$itemParamQ['value'];
                                    }

                                    $sumXmultiplyK +=  $X * $K;

                                    $sumX += $X;

                                    $sumK += $K;

                                    $sumSquareX += $X * $X;

                                }
                            }
                        }

                        if (isset($L0))
                            unset($L0);

                        $L0 =
                            ($mParameter * $sumXmultiplyK - $sumX * $sumK)
                                /
                            ($mParameter * $sumSquareX - $sumX * $sumX);


                        if (isset($Kd))
                            unset($Kd);

                        $Kd = 1 / $mParameter * ($sumK - $L0 * $sumX);

                        $result = new Result();

                        $result->setProjectId($project->getId());
                        $result->setPointId($point->getId());
                        $result->setParameterId($parameter->getId());

                        if (isset($resultValue))
                            unset($resultValue);

                        $resultValue = json_encode(array(
                            'Kd' => $Kd,
                            //'L0' => $L0 Пока оставим только Kd
                        ));


                        if (!empty($resultValue))
                            $result->setValue($resultValue);

                        $em->persist($result); // "Коммитим" изменения перед отпоавкой в БД
                        $em->flush();

                        $this->addFlash('success', "Данные случая $method успешно рассчитаны!");
                        $this->addFlash('error', "Параметры с данными '-1', не участвуют в рассчетах, таких данных: $n");
                        return $this->redirectToRoute('parameter_value_show', array(
                            'project' => $project->getId(),
                            'id' => $point->getId(),
                            'parameter' => $parameter->getId(),
                        ));

                        break;
                }

                $this->addFlash('error', "Внимание! Случай $method, на данный момент не имеет алгоритма расчета!");
                return $this->redirectToRoute('parameter_value_show', array(
                    'project' => $project->getId(),
                    'id' => $point->getId(),
                    'parameter' => $parameter->getId(),
                ));

            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('parameter_value_show', array(
                    'project' => $project->getId(),
                    'id' => $point->getId(),
                    'parameter' => $parameter->getId(),
                ));
            }

        } else {
            $this->addFlash('error', 'Форма не отправлена, повторите еще раз!');
            return $this->redirectToRoute('andy_diffuse_river_homepage');
        }

    }

}


