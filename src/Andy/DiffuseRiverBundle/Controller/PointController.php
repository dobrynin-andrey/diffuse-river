<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\AndyDiffuseRiverBundle;
use Andy\DiffuseRiverBundle\Classes\ImportCSV;
use Andy\DiffuseRiverBundle\Entity\ImportForm;
use Andy\DiffuseRiverBundle\Entity\ParamDate;
use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\ParamValue;
use Andy\DiffuseRiverBundle\Entity\Point;
use Andy\DiffuseRiverBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Point controller.
 *
 * Class PointController
 * @package Andy\DiffuseRiverBundle\Controller
 */

class PointController extends Controller
{
    /**
     * Вывод параметров, которые пренадлежат данной точке
     *
     * @param Point $point
     * @return Parameter[]|array|bool
     */

    public function renderParameters(Point $point)
    {
        $em = $this->getDoctrine()->getManager();

        // Проверка на наличие в базе данных ParamDate
        $paramDate = $em->getRepository('AndyDiffuseRiverBundle:ParamDate')->findBy(
            array(
                'pointId' => $point->getId()
            )
        );

        if ($paramDate) {

            // Запрос обеденяет ParamValue и ParamValue, вовдит только массив parameterId тукущей точки
            $query = $em->createQuery(
                'SELECT DISTINCT(pv.parameterId)
                FROM AndyDiffuseRiverBundle:ParamValue pv
                JOIN AndyDiffuseRiverBundle:ParamDate pd
                WHERE pd.pointId = :point
                AND pv.paramDateId = pd.id'
            )->setParameter('point', $point);

            // Убирвем лишнюю вложенность массива из запроса
            $arParameterId = array_column($query->getResult(), '1');

            // Ноходим список параметров данной точки
            $parameters = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findBy(
                array('id' => $arParameterId),
                array('name' => 'asc')
            );

            return $parameters;
        } else {
            return false;
        }
    }


    /**
     * Finds and displays a point entity.
     *
     * @param Request $request
     * @param Project $project
     * @param Point $point
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Project $project, Point $point)
    {

        // Форма импорта новых параметров
        $importForm = new ImportForm();
        $form_import = $this->createForm('Andy\DiffuseRiverBundle\Form\ImportFormType', $importForm);
        $form_import->handleRequest($request);


        // Вызовем доктрину для работы с данными
        $em = $this->getDoctrine()->getManager();
        // Получим все параметры из базы, для дальшей работы с ними
        $allParameters = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findAll();


        /**
         *  При отправке формы импорта
         */

        if ($form_import->isSubmitted() && $form_import->isValid()) {

            // Получим файл из ранее вызванной формы, котороый отправили при сабмите
            $file = $importForm->getFile();
            // Вызовем класс парсинга csv
            $importCSV = new ImportCSV();
            $checkFile = $importCSV->checkFile($file, 'csv', '2000000');

            // Счетчик импортированных данных
            $d = 1;
            if ($checkFile) { // Если файл корректен

                $importResult['success'] = array();
                $importResult['error'] = array();

                // Получить путь к файлу
                $pathFile = $file->getRealPath();

                // Парсинг файла - получение массива
                $arImportData = $importCSV->parseCSV($pathFile, $allParameters);

                // Выводим ошибки парсинга


                if (!empty($arImportData['errors'])) {

                    foreach ($arImportData['errors']['values'] as $dataError) {
                        $this->addFlash('error', $dataError);
                    }

                } else {

                    // Перебираем дату из полученнго массива
                    foreach ($arImportData['result']['date'] as $keyDate => $date) {

                        //Создаем экземпляр модели для таблицы ParamDate
                        $pramDate = new ParamDate();

                        // Сохраняем год и дату данных
                        $dateStr = '';
                        // Делаем дополнительную проверку на тот случай если дата записана с разделением ','
                        if ($dateStr = str_replace(',', '.', $date['date'])) {
                            $date['date'] = $dateStr;
                        }

                        $pramDate->setDate(new \DateTime ($date['date']));
                        // Привязываем к текущей точке
                        $paramDateId = $pramDate->setPointId($point->getId());
                        $em->persist($pramDate); // "Коммитим" изменения перед отпоавкой в БД
                        $em->flush();


                        $id = 0; // Счетчик для id
                        foreach ($arImportData['result']['value'][$keyDate] as $keyValue => $value) {
                            //Создаем экземпляр модели для таблицы ParamValue
                            $pramValue = new ParamValue();
                            $pramValue->setParamDateId($paramDateId->getId()); // Берем id даты параметра из ранее привязанной точки
                            $pramValue->setParameterId($arImportData['result']['id'][$id]); // Берем id параметра из парсинга
                            $pramValue->setValue(
                                is_double($value) ? $value : doubleval(str_replace(',', '.', $value))
                            );
                            $em->persist($pramValue); // "Коммитим" изменения перед отпоавкой в БД
                            $em->flush();
                            $id++;
                            $d++;
                        }

                    }

                    // В конце импорта выводим соовбщение
                    $this->addFlash('success', 'Импорт завершен! Всего данных загружено: '. $d);
                }

            }

            // Вызов функции по выводу параметров из базы
            $parameters = $this->renderParameters($point);

            // Форма очистки данных
            $cleanValueForm = $this->createCleanValueForm($project, $point);

            // Ответ при импорте
            return $this->render('@AndyDiffuseRiver/Point/show.html.twig', array(
                'point' => $point,
                'project' => $project,
                'parameters' => $parameters,
                'form_import' => $form_import->createView(),
                'import_success' => $importResult['success'],
                'import_error' => $importResult['error'],
                'clean_value_form' => $cleanValueForm->createView()
            ));
        } // Конец - При отправке формы импорта

        // Вызов функции по выводу параметров из базы
        $parameters = $this->renderParameters($point);

        // Форма очистки данных
        $cleanValueForm = $this->createCleanValueForm($project, $point);

        // Ответ при простой загрузки страницы
        return $this->render('@AndyDiffuseRiver/Point/show.html.twig', array(
            'point' => $point,
            'project' => $project,
            'parameters' => $parameters,
            'form_import' => $form_import->createView(),
            'clean_value_form' => $cleanValueForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing point entity.
     *
     * @param Request $request
     * @param Project $project
     * @param Point $point
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * @param Request $request
     * @param Project $project
     * @param Point $point
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Project $project, Point $point)
    {
        $form = $this->createDeleteForm($project ,$point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Находим ParamDate
            $paramDateId = $em->getRepository('AndyDiffuseRiverBundle:ParamDate')->findBy(array(
                'pointId' => $point->getId()
            ));
            if (!empty($paramDateId)) {
                // Перебирем ParamDate и находим ParamValue
                foreach ($paramDateId as $itemParamDateId) {
                    $paramValue = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')->findBy(array(
                        'paramDateId' => $itemParamDateId
                    ));
                    // Перебирем ParamValue и добавляем на удаление
                    if (!empty($paramValue)) {
                        foreach ($paramValue as $itemParamValue) {
                            $em->remove($itemParamValue);
                        }
                    }
                    // Добавляем на удаление ParamDate
                    $em->remove($itemParamDateId);
                }
            }

            $arResults = $em->getRepository('AndyDiffuseRiverBundle:Result')->findBy(array(
                'projectId'      => $project->getId(),
                'pointId'        => $point->getId(),
            ));

            foreach ($arResults as $itemResult) {
                $em->remove($itemResult);
            }

            $em->remove($point);
            $em->flush();
        }

        return $this->redirectToRoute('project_show', array('id' => $project->getId()));
    }

    public function cleanPointValueAction(Project $project, Point $point) {
        $em = $this->getDoctrine()->getManager();
        // Находим ParamDate
        $paramDateId = $em->getRepository('AndyDiffuseRiverBundle:ParamDate')->findBy(array(
            'pointId' => $point->getId()
        ));
        if (!empty($paramDateId)) {
            // Перебирем ParamDate и находим ParamValue
            foreach ($paramDateId as $itemParamDateId) {
                $paramValue = $em->getRepository('AndyDiffuseRiverBundle:ParamValue')->findBy(array(
                    'paramDateId' => $itemParamDateId
                ));
                // Перебирем ParamValue и добавляем на удаление
                if (!empty($paramValue)) {
                    foreach ($paramValue as $itemParamValue) {
                        $em->remove($itemParamValue);
                    }
                }
                // Добавляем на удаление ParamDate
                $em->remove($itemParamDateId);
            }
        }

        $arResults = $em->getRepository('AndyDiffuseRiverBundle:Result')->findBy(array(
            'projectId'      => $project->getId(),
            'pointId'        => $point->getId(),
        ));

        foreach ($arResults as $itemResult) {
            $em->remove($itemResult);
        }

        $em->flush();

        return $this->redirectToRoute('point_show',
            array(
                'project'   => $project->getId(),
                'id'        => $point->getId()
            )
        );

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

    /**
     * Creates a form to delete a point entity.
     *
     * @param Point $point The point entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCleanValueForm(Project $project, Point $point)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('point_value_clean', array('project' => $project->getId(), 'id' => $point->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
