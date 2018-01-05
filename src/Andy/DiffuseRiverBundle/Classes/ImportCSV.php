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
        $i = 1; // Счетчик строк
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
                    $this->addFlash('error', "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k);
                    $arrErrors["coding"][] = "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k;
                }

                /**
                 *  Проверка значений
                 */

                if (empty($itemData)) {
                    switch ($k) {
                        case 0:
                            $arrErrors["values"][] = "Предупреждение! Пустое значение - в строке №: " . $i . " в ячейке №: " . ($k+1) . ". Необходимо ввести название!";
                            break;
                        case 1:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . $i . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести уникальный код. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                        case 2:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . $i . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести единицы измерения. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                    }
                }


                /**
                 * Перебор значений
                 */
                if ($itemData != '') {
                    if ($i == 0) {
                        $arMD["head"][] = mb_convert_encoding($itemData, 'UTF-8', $coding );
                    }
                }
                if ($itemData != '') {
                    if ($i > 0) {
                        if ($k == 0) {
                            $arMD[$i-1]["parameter_name"] = mb_convert_encoding($itemData, 'UTF-8', $coding );
                        }
                        if ($k == 1) {
                            $arMD[$i-1]["parameter_code"]= mb_convert_encoding($itemData, 'UTF-8', $coding );
                        }

                        if ($k == 2) {
                            $arMD[$i-1]["parameter_ed_izm"] = mb_convert_encoding($itemData, 'UTF-8', $coding );
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

    public function parseCSV ($file) {
        $openFile = fopen($file, "r"); // Прочитать файл
        $arMD = array();  // Объявить массив значений
        $arrErrors = array(); // Объявить массив ошибок
        $i = 0; // Счетчик строк
        $arCoding = array("UTF-8", "ASCII", "Windows-1251", false);
        while ($data = fgetcsv($openFile, 10000, ";")) {
            if (count($data) > 4) {
                array_splice($data, 4);
            }
            foreach ($data as $k => $itemData) {
                /**
                 * Проверка на кодировку
                 */
                $coding = mb_detect_encoding($itemData);
                if (!in_array($coding, $arCoding)) {
                    $this->addFlash('error', "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k);
                    $arrErrors["coding"][] = "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k;
                }

                /**
                 *  Проверка значений
                 */

/*
                if (empty($itemData)) {
                    switch ($k) {
                        case 0:
                            $arrErrors["values"][] = "Предупреждение! Пустое значение - в строке №: " . $i . " в ячейке №: " . ($k+1) . ". Необходимо ввести название!";
                            break;
                        case 1:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . $i . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести значение года. Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                        case 2:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . $i . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести значение года.Если оно неизвестно, то система автоматически проставит значение "-1", и параметры данного объекта не будет учавствовать в расчетах!';
                            $itemData = -1;
                            break;
                        case 3:
                            $arrErrors["values"][] = 'Предупреждение! Пустое значение - в строке №: ' . $i . ' в ячейке №: ' . ($k+1) . '. Необходимо ввести тип объекта. "Муниципальный район" или "Городской округ"!';
                            break;

                    }
                }*/


                /**
                 * Перебор значений
                 */
                if ($itemData != '') {
                    if ($i == 0) {
                        $arMD["head_year"][] = mb_convert_encoding($itemData, 'UTF-8', 'Windows-1251' );
                    }
                }
                if ($itemData != '') {
                    if ($i > 0) {
                        if ($k == 0) {
                            $arMD[$i-1]["district_name"] = mb_convert_encoding($itemData, 'UTF-8', 'Windows-1251' );
                        }
                        if ($k > 0 && $k < 3) {
                            $arMD[$i-1]["year"][] = mb_convert_encoding($arMD["head_year"][$k], 'UTF-8', 'Windows-1251' );
                            $arMD[$i-1]["parameter_value"][$arMD["head_year"][$k]] = floatval(str_replace(',', '.', str_replace(' ', '', strval(mb_convert_encoding( $itemData, 'UTF-8', 'Windows-1251' )))));
                        }

                        if ($k == 3) {
                            $arMD[$i-1]["type"] = mb_convert_encoding($itemData, 'UTF-8', 'Windows-1251' );
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


}

