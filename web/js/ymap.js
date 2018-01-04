/*jslint browser: true*/
/*global $, jQuery, alert*/

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


        for (var i = 0; i < coordsPoint.length; i++) {

            var arSplitCoords = coordsPoint[i].split(",").map(Number); // Преобразуем строку в массив

            pointsGeoObjects[i] = new ymaps.GeoObject({
                geometry: {
                    type: "Point",
                    coordinates: arSplitCoords,
                },
                properties: {
                    hintContent: namePoint[i],
                    balloonContent: "<p><strong>Точка:</strong><br>" + namePoint[i] + "</p><p><strong>Координаты:</strong><br>" + arSplitCoords[0] +", " + arSplitCoords[1] +"</p>"
                }
            });
        }

        var myClusterer = new ymaps.Clusterer();
        myClusterer.add(pointsGeoObjects);
        myMap.geoObjects.add(myClusterer);


        // Если страница редактирования то меняем центр и зум
        if (editPoint) {
            myMap.setZoom(10);
            myMap.setCenter(coordsPoint[0].split(",").map(Number));
        }


        // Обработчик клика по карты с добавлением нового и заплонении формы

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


    }

});