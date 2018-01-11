<?php

namespace Andy\DiffuseRiverBundle\Classes;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ImportCSV {


    public function checkFile($file, $typeFile, $maxSize) {
        $errors = '';
        /**
         * Проверка файла перед парсингом
         */
        try {
            if (empty($file)) // Проверка на наличие файла
                throw new Exception('Файл не выбран!');

            if ($file->getSize() > $maxSize) // Максимальный размер 2Мб
                throw new Exception('Файл не должен превышать ' . $maxSize/100000 . 'Мб.');

            $arName =  explode('.', $file->getClientOriginalName()); // Получение название файла в виде массива
            if (strtolower(end($arName)) != $typeFile)  // Проверка на расширение
                throw new Exception('Файл должен быть расширения ".' . $typeFile . '".');

        } catch (\Exception $exception) { // Вывод исключений
            $errors = $exception->getMessage();
        }

        return ($errors)? $errors : true;
    }


    public function parseParamCSV ($file) {
        $openFile = fopen($file, "r"); // Прочитать файл
        $arMD = array();  // Объявить массив значений
        $arrErrors = array(); // Объявить массив ошибок
        $i = 0; // Счетчик строк
        $arCoding = array("UTF-8", "ASCII", "Windows-1251", false);
        while ($data = fgetcsv($openFile, 10000, ";")) {
            if (count($data) > 3) {
                array_splice($data, 3);
            }
            foreach ($data as $k => $itemData) {
                /**
                 * Проверка на кодировку
                 */

                $coding = mb_detect_encoding($itemData);
                if (!in_array($coding, $arCoding)) {
                    $arrErrors["values"][] = "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . ($i+1) . " в ячейке №: " . ($k+1);
                } else {
                    $itemData = mb_convert_encoding($itemData, 'UTF-8', $coding);
                }

                /**
                 *  Проверка значений
                 */

                if (empty($itemData)) {
                    switch ($k) {
                        case 0:
                            $arrErrors["values"][] = "Предупреждение! Пустое значение - в строке №: " . ($i+1) . " в ячейке №: " . ($k+1) . ". Необходимо ввести название!";
                            break;
                        case 1:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . ($i+1) . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести уникальный код. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                        case 2:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . ($i+1) . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести единицы измерения. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                    }
                }


                /**
                 * Перебор значений
                 */
                if ($itemData != '') {
                    if ($i == 0) {
                        $arMD["head"][] = $itemData;
                    }
                }
                if ($itemData != '') {
                    if ($i > 0) {
                        if ($k == 0) {
                            $arMD[$i-1]["parameter_name"] = $itemData;
                        }
                        if ($k == 1) {
                            $arMD[$i-1]["parameter_code"]= $itemData;
                        }

                        if ($k == 2) {
                            $arMD[$i-1]["parameter_ed_izm"] = $itemData;
                        }

                    }
                }

            }

            $i++;

            /**
             * Прекращаем работу, если хоть одна ячейка не соответсвует кодировки!
             */

            if (!empty($arrErrors["coding"])) {
                echo "Ошибка кодировки!";
                var_dump($arrErrors);
                $arMD = array();
                break;
            }

        }

        array_splice($arMD, 0, 1);
        $arResult["result"] = $arMD;

        if ($arrErrors) {
            $arResult["errors"] = $arrErrors;
        }

        return $arResult;

    }

    public function parseCSV ($file, $arParameter) {
        $openFile = fopen($file, "r"); // Прочитать файл
        $arMD = array();  // Объявить массив значений
        $arrErrors = array(); // Объявить массив ошибок
        $i = 0; // Счетчик строк
        $arCoding = array("UTF-8", "ASCII", "Windows-1251", false);
        while ($data = fgetcsv($openFile, 10000, ";")) {
            foreach ($data as $k => $itemData) {


                /**
                 * Проверка на кодировку
                 */

                $coding = mb_detect_encoding($itemData);
                if (!in_array($coding, $arCoding)) {
                    $arrErrors["values"][] =  "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . ($i+1) . " в ячейке №: " . ($k+1);
                    continue;
                } else {
                    $itemData = mb_convert_encoding($itemData, 'UTF-8', $coding);
                }


                /**
                 *  Проверка значений
                 */

                if (empty($itemData) && $itemData != 0) {
                    $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . ($i+1) . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести значение. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                    $itemData = -1;
                    continue;
                }


                /**
                 * Перебор значений
                 */


                if ($i == 0) { // Берем первую строку файла (шапка)
                    foreach ($arParameter as $itemParam) { // Перебераем параметры, которые есть в базе
                        if ($itemParam->getCode() == $itemData) { // Проверяем с код параметры из базы с импортируемым
                            $arMD["code"][] = $itemData; // Если есть совпадение пишем код параметра в массив
                            $arMD["id"][] = $itemParam->getId(); // Если есть совпадение пишем id параметра в массив
                        }
                    }
                }



                // Смотрим остальные строки
                if ($i > 0) {
                    if ($k == 0) { // Первая ячейка, тут должна быть дата (DAT)
                        $arMD['date'][$i-1]["date"] = $itemData;
                    } else {
                        if (!empty($arMD["code"][$k])) {
                            $arMD['value'][$i-1][$arMD["code"][$k]] = $itemData;
                        } else {
                            $arrErrors["values"][] = 'Ошибка! Не найден индекс: ' . $k . ', с для значением : ' . $itemData . '.';
                        }
                    }
                }
            }

            $i++;
        }


        if (empty($arMD["code"])) {
            $arrErrors["values"][] = 'Ошибка! В шапке таблицы не найден код параметра, перейдите в раздел "Параметры" и заполните форму!';
        }

        if (!empty($arMD['code']) || !empty($arMD['id'])) {
            array_splice($arMD['code'], 0, 1);
            array_splice($arMD['id'], 0, 1);
        }


        $arResult["result"] = $arMD;

        if ($arrErrors) {
            $arResult["errors"] = $arrErrors;
        }

        return $arResult;

    }


}

