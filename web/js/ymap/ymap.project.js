
$(document).ready(function () {

    /**
     * Глобальные переменные
     */
    var editPoint = window.$editPoint; // Флаг страницы редактирования точки, задется в шаблоне Project/show.html.twig, либо Point/edit.html.twig
    var coordsPoint = window.$points.coords; // Координаты точек из базы, задется там же
    var namePoint = window.$points.name; // Имена точе из базы, задется там же

    ymaps.ready(init);
    var myMap;

    function init() {
        myMap = new ymaps.Map("map", {
            center: [63.025362, 87.686597],
            zoom: 4,
            //behaviors: ['ruler'],
            controls: ['zoomControl', 'searchControl']
        });
        myMap.behaviors.disable('scrollZoom');
        myMap.controls.add('zoomControl');


        // Вывод добавленных ранее данных из базы

        var pointsGeoObjects = [];

        var linePoints = {
            id: [],
            coord : []
        };

        var n = 0; // счетчик предыдущей точки


        var arrPoints = {
            type: 'FeatureCollection',
            features: []

        };

        for (var i = 0; i < coordsPoint.length; i++) {

            var arSplitCoords = coordsPoint[i].split(",").map(Number); // Преобразуем строку в массив

            feature = {
                type: 'Feature',
                properties: {
                    hintContent: namePoint[i],
                    balloonContent: "<p><strong>Точка:</strong><br>" + namePoint[i] + "</p><p><strong>Координаты:</strong><br>" + arSplitCoords[0] +", " + arSplitCoords[1] +"</p>"
                },
                geometry: {
                    type: 'Point',
                    coordinates: arSplitCoords
                }
            };

            // Для массива с поиском ближащей точки
            arrPoints.features.push(feature);


            linePoints.coord.push(arSplitCoords);

        }

        // Описания точек можно хранить в формате JSON, а потом генерировать
        // из описания геообъекты с помощью ymaps.geoQuery.
        points = ymaps.geoQuery(arrPoints).addToMap(myMap);


        myMap.geoObjects.add(points.clusterize()); // Добавить кластер на карту

        //console.log(points._objects);

        var lastIndex = points._objects.length-1;
        //console.log(lastIndex);

        /*points._objects.forEach(function (item, key) {
            startPont = item;
            endPoint = points._objects[key+1];
            //console.log(startPont);
            //console.log(endPoint);
            if (endPoint !== undefined) {
                res = findClosestObjects(startPont, endPoint);
               // console.log(res);
            }*/

            /*if (lastIndex !== key) {
                findClosestObjects(startPont, item);
                console.log(item);
                console.log(key);
            }*/

        /*});*/


        /**
         *
         * @param startPoint
         * @param endPoint
         * @returns {ymaps.Polyline}
         */

        function findClosestObjects (startPoint, endPoint) {

            arr = [];
            arr.push(startPoint.geometry._coordinates);

           // console.log('END:');
            //console.log(endPoint.geometry);
            //console.log("res");
            res = points.getClosestTo(startPoint.geometry);

            if (res.geometry._coordinates != endPoint.geometry._coordinates) {
                arr.push(res.geometry._coordinates);
            } else {
                arr.push(endPoint.geometry._coordinates);
            }

           // console.log(arr);

            // Создаем ломаную с помощью вспомогательного класса Polyline.
            var myPolyline = new ymaps.Polyline(
                // Указываем координаты вершин ломаной.
                arr
                , {
                    // Описываем свойства геообъекта.
                    // Содержимое балуна.
                    balloonContent: "",
                    hintContent:""

                }, {
                    // Задаем опции геообъекта.
                    // Отключаем кнопку закрытия балуна.
                    balloonCloseButton: false,
                    // Цвет линии.
                    strokeColor: "#2f323e",
                    // Ширина линии.
                    strokeWidth: 4,
                    // Коэффициент прозрачности.
                    strokeOpacity: 0.7
                });

            // Добавляем линии на карту.
            myMap.geoObjects
                .add(myPolyline);

            return myPolyline;

        }

      /*  // Будем открывать балун кафе, который ближе всего к месту клика
        myMap.events.add('click', function (event) {
            cafe.getClosestTo(event.get('coordPosition')).balloon.open();
        });

        findClosestObjects();*/
/*
        // Описания точек можно хранить в формате JSON, а потом генерировать
        // из описания геообъекты с помощью ymaps.geoQuery.
        point = ymaps.geoQuery({
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                properties: {
                    balloonContent: 'Кофейня "Дарт Вейдер" - у нас есть печеньки!'
                },
                geometry: {
                    type: 'Point',
                    coordinates: [55.724166, 37.545849]
                }
            }, {
                type: 'Feature',
                properties: {
                    balloonContent: 'Кафе "Горлум" - пирожные прелесть.'
                },
                geometry: {
                    type: 'Point',
                    coordinates: [55.717495, 37.567886]
                }
            }, {
                type: 'Feature',
                properties: {
                    balloonContent: 'Кафе "Кирпич" - крепкий кофе для крепких парней.'
                },
                geometry: {
                    type: 'Point',
                    coordinates: [55.7210180,37.631057]
                }
            }
            ]
            // Сразу добавим точки на карту.
        }).addToMap(myMap);

        // Координаты станции метро получим через геокодер.
        metro = ymaps.geoQuery(ymaps.geocode('Москва, Кропоткинская', {kind: 'metro'}))
        // Нужно дождаться ответа от сервера и только потом обрабатывать полученные результаты.
            .then(findClosestObjects);


*/












    // Расстояние
      /*  alert(ymaps.formatter.distance(
            ymaps.coordSystem.geo.solveInverseProblem(coordsPoint[0].split(",").map(Number), coordsPoint[1].split(",").map(Number))
        ));*/


      /*  // Создаем ломаную с помощью вспомогательного класса Polyline.
        var myPolyline = new ymaps.Polyline(
            // Указываем координаты вершин ломаной.
            linePoints.coord
        , {
            // Описываем свойства геообъекта.
            // Содержимое балуна.
            balloonContent: "Ломаная линия"
        }, {
            // Задаем опции геообъекта.
            // Отключаем кнопку закрытия балуна.
            balloonCloseButton: false,
            // Цвет линии.
            strokeColor: "#2f323e",
            // Ширина линии.
            strokeWidth: 4,
            // Коэффициент прозрачности.
            strokeOpacity: 0.7
        });

        // Добавляем линии на карту.
        myMap.geoObjects
            .add(myPolyline);
*/



        // Если страница редактирования то меняем центр и зум
        if (editPoint) {
            myMap.setZoom(10);
            myMap.setCenter(coordsPoint[0].split(",").map(Number));
        }


        /**
         * Обработчик клика по карты с добавлением нового и заплонении формы
          */

        myMap.events.add('click', function (e) {
            // Получение координат щелчка
            var coords = e.get('coords');

            var inputCoords = document.getElementById(window.$inputCoords); // Находим глобальню переменную, которую задали в шаблоне show.html.twig
            $(inputCoords).val(coords); // Помещаем в input формы

            // Удаляем прерыдущий объект после первого клика
            if (typeof myGeoObject !== 'undefined')  {
                myMap.geoObjects.remove(myGeoObject);

            }

            // Если мы на странице редактирования то удаляем текущую точку
            if (editPoint) {
                myMap.geoObjects.remove(myClusterer);
            }

            // Задаем объект на карте
            myGeoObject = new ymaps.GeoObject({
                geometry: {
                    type: "Point", // тип геометрии - точка
                    coordinates: coords // координаты точки
                }
            });

            myMap.geoObjects.add(myGeoObject); // Размещение геообъекта на карте.

        });



        // Создание кнопки и добавление ее на карту.
        var buttonCreateCalculation = new ymaps.control.Button({
            data: {
                // Зададим иконку для кнопки
                // Текст на кнопке.
                content: 'Создать расчет',
                // Текст всплывающей подсказки.
                title: 'Нажмите для создания расчета',
            },
            options: {
                // Зададим опции для кнопки.
                selectOnClick: true,
                position: {top: 10, right: 10},
                maxWidth: 130
            }
        });


        buttonCreateCalculation.events.add('click', function (e) {
            console.log(e);
            alert('test');
        });
        myMap.controls.add(buttonCreateCalculation);







    }

});