<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Andy\DiffuseRiverBundle\Controller\PointController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CalculateController extends Controller
{
    public function indexAction(Request $request)
    {
        $result['success'] = array();
        $result['error'] = array();
       /* $data = array(
            'start' => '4',
            'end'   => '4'
        );*/
        //$data = '{"start":3,"end":4}';
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
           // $data = json_decode($data, true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        try {


            if (empty($data))
                throw new \Exception('Не выбраны точки расчета!');
            if (empty($data['start']))
                throw new \Exception('Не выбрана начальная точка расчета!');
            if (empty($data['end']))
                throw new \Exception('Не выбрана конечная точка расчета!');



            $em = $this->getDoctrine()->getManager();
            // Делаем запорос в БД, на входе: точки из запроса
            $points = $em->getRepository('AndyDiffuseRiverBundle:Point')->findBy(array(
                'id' =>  $data
            ));

            if (empty($points[0]))
                throw new \Exception("Точка с id: " . $data['start'] ." не существет в базе!");
            else
                $startPoint = $points[0];

            if (empty($points[1]))
                throw new \Exception("Точка с id: " . $data['end'] ." не существет в базе!");
            else
                $endPoint = $points[1];

            if (!$paramStartPoint = $this->getValueParameter($startPoint))
                throw new \Exception("Данные для точки с id: " . $data['end'] ." не существуют в базе!");
            if (!$paramEndPoint = $this->getValueParameter($endPoint))
                throw new \Exception("Данные для точки с id " . $data['end'] ." не существуют в базе!");




            $result['success'] = [1];

        } catch (\Exception $e) {
            $result['error'][] = $e->getMessage();
            return $this->json(json_encode($result, JSON_UNESCAPED_UNICODE));
        }



//        foreach ($paramStartPoint as $key => $itemParamStartPoint) {
//
//        }
//
//        die();
//
//
//
//
//
//
//
//
//
//
//
//        if (count($paramStartPoint) != count($paramEndPoint)) {
//            dump('error');
//            dump($paramStartPoint);
//            dump(count($paramStartPoint));
//            dump(count($paramEndPoint));
//            die();
//        }
//
//        $arResult = array();
//        foreach ($paramStartPoint as $key => $itemParamStartPoint) {
//
//            // Получем значения точек
//            if (!empty($itemParamStartPoint['value']) && $itemParamStartPoint['value'] != 0) {
//                $startVal = (float)$itemParamStartPoint['value'];
//            } else {
//                $startVal = (float)-1;
//            }
//
//            if (!empty($paramEndPoint[$key]['value']) && $paramEndPoint[$key]['value'] != 0) {
//                $endVal = (float)$paramEndPoint[$key]['value'];
//            } else {
//                $endVal = (float)-1;
//            }
//
//
//
//            $arResult[] = $startVal / $endVal;
//        }
//        dump($arResult);
//
//        //die();
//        //dump($paramStartPoint);
//        echo '<pre>';
//        dump($arResult);
//        dump($points);
//        dump($paramStartPoint);
//        dump($paramEndPoint);
//        echo '</pre>';
//
//
//        die();
//        //$paramEndPoint = $this->renderParameters($endPoint);
//
//       /* foreach ($paramStartPoint as $k => $itemParam) {
//            $arItemParam = $this->getValueParameter($itemParam);
//
//            dump($itemParam);
//            dump($arItemParam);
//            die();
//        }*/
//
//        dump($points);
//        dump($paramStartPoint);
//        dump($paramEndPoint);
//        die($data);
        return $this->json(json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param Point $point
     * @return mixed
     */

    public function getValueParameter(Point $point)
    {
        $em = $this->getDoctrine()->getManager();


        // Запрос объединяет ParamValue и ParamDate, выводит только массив parameterId текущей точки
        $query = $em->createQuery(
            'SELECT pv.id, pv.parameterId, pv.value, pd.date
                FROM AndyDiffuseRiverBundle:ParamValue pv
                JOIN AndyDiffuseRiverBundle:ParamDate pd
                WHERE pv.paramDateId = pd.id 
                AND pd.pointId = :point
                ORDER BY pv.parameterId'
        )->setParameter('point', $point);


        return $query->getResult();
    }

}


