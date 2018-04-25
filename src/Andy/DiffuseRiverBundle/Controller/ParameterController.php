<?php

namespace Andy\DiffuseRiverBundle\Controller;

use Andy\DiffuseRiverBundle\Classes\ImportCSV;
use Andy\DiffuseRiverBundle\Entity\ImportForm;
use Andy\DiffuseRiverBundle\Entity\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
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

        $formDeleteAll = $this->createDeleteAllForm();

        return $this->render('@AndyDiffuseRiver/Parameter/index.html.twig', array(
            'parameters' => $parameters,
            'delete_all' => $formDeleteAll->createView()
        ));
    }

    /**
     * Creates a new parameter entity.
     *
     */
    public function newAction(Request $request)
    {

        // Форма ввода нового параметра вручную
        $parameter = new Parameter();
        $form = $this->createForm('Andy\DiffuseRiverBundle\Form\ParameterType', $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('parameter_index');
        }

        // Форма импорта новых параметров
        $importForm = new ImportForm();
        $form_import = $this->createForm('Andy\DiffuseRiverBundle\Form\ImportFormType', $importForm);
        $form_import->handleRequest($request);

        if ($form_import->isSubmitted() && $form_import->isValid()) {

            $file = $importForm->getFile();

            $importCSV = new ImportCSV();
            $checkFile = $importCSV->checkFile($file, 'csv', '2000000');

            if ($checkFile) { // Если файл корректен

                $importResult['success'] = array();
                $importResult['error'] = array();

                // Получить путь к файлу
                $pathFile = $file->getRealPath();

                // Парсинг файла - получение массива
                $arImportData = $importCSV->parseParamCSV($pathFile);

                // Выводим ошибки парсинга
                if (!empty($arImportData['errors'])) {
                    foreach ($arImportData['errors']['values'] as $dataError) {
                        $this->addFlash('error', $dataError);
                    }

                }

                //Получаем менеджер БД - Entity Manager
                $em = $this->getDoctrine()->getManager();

                foreach ($arImportData['result'] as $data) {

                    $courses = $em->getRepository('AndyDiffuseRiverBundle:Parameter')->findBy(array(
                        'code' => $data['parameter_code']
                    ));
                    if (empty($courses)) {
                        //Создаем экземпляр модели
                        $parameter = new Parameter;
                        //Задаем значение полей
                        $parameter->setName($data['parameter_name']);
                        $parameter->setCode($data['parameter_code']);
                        $parameter->setEdIzm($data['parameter_ed_izm']);
                        //Передаем менеджеру объект модели
                        $em->persist($parameter);
                        //Добавляем запись в таблицу
                        $em->flush();

                        $importResult['success'][] = $data['parameter_name'];
                        $this->addFlash('success', 'Параметр: ' . $data['parameter_name'] . ' успешно импортирован.');

                    } else {

                        $importResult['error'][] = $data['parameter_name'];
                        $this->addFlash('error', 'Параметр: ' . $data['parameter_name'] . ' уже есть в базе с кодом: ' . $data['parameter_code'] . '.');

                    }
                }



            }

            return $this->render('@AndyDiffuseRiver/Parameter/new.html.twig', array(
                'parameter' => $parameter,
                'form' => $form->createView(),
                'form_import' => $form_import->createView(),
                'import_success' => $importResult['success'],
                'import_error' => $importResult['error']
            ));
        }



        return $this->render('@AndyDiffuseRiver/Parameter/new.html.twig', array(
            'parameter' => $parameter,
            'form' => $form->createView(),
            'form_import' => $form_import->createView(),
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
     * Deletes all parameters entity.
     *
     */
    public function delete_allAction(Request $request)
    {

        $formDeleteAll = $this->createDeleteAllForm();
        $formDeleteAll->handleRequest($request);

        // Удалить по клику в общем списке
        if ($formDeleteAll->isSubmitted() && $formDeleteAll->isValid()) {
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

    /**
     * Creates a form to deleteAll value a parameter entity.
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     */
    private function createDeleteAllForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parameter_delete_all'))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
