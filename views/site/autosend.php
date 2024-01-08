<?php

$this->title = 'Выгрузка';

$token = $_GET['token'];

$country_list = [];
$source_list = [];

// Настройка даты на месяц назад

$date_from = "";

$Date = new DateTime(date('Y-m-d'));
$shift = -1;

$day = $Date->format('d');
$Date->modify('first day of this month')->modify(($shift > 0 ? '+':'') . $shift . ' months');
$day = $day > $Date->format('t') ? $Date->format('t') : $day;
$date_from =  $Date->modify('+' . $day-1 . ' days')->format('Y-m-d');

// Настройка даты на месяц назад

foreach($model as $patch)
{
    array_push($country_list, $patch->country_code);
    array_push($source_list, $patch->utm_source);
}

$country_list = json_encode($country_list, true);
$source_list = json_encode($source_list, true);
$date_from = json_encode($date_from, true);
$date_to = json_encode(date('Y-m-d'), true);

$token = json_encode($token, true);

?>

<script>
    
    status = 0;
    c = 0;
    s = 0;

    let interval;

    
    country__list = <?=$country_list?>;
    utm_source__list = <?=$source_list?>;

    document.addEventListener('DOMContentLoaded', function(){
        DeleteApi();
        GetApi();
    });

    // Очистка базы данных

    function DeleteApi(){

        token = <?=$token?>

        $.ajax({
            url: '/basic/web/site/deleteallapi',
            method: 'get',
            dataType: 'html',
            data: {token: token},
            success: function(data_in) {
                console.log("Успешно очищено");
            }
        });
    }

    // Очистка базы данных


    // Создание интервала
    
    function GetApi(){
        token = <?=$token?>

        if(token == "sadkjlnnv34jlkasjd98@3laskdjmhg"){
            page = 1;
            interval = setInterval(() => {
                i = 0;
                d = 0;
                ctr_int = 0;
                vs_int = 0;
                country = country__list[c];
                date_to = <?=$date_to?>;
                date_from = <?=$date_from?>;
                utm_source = utm_source__list[s];

                if(country__list.length < c || utm_source__list.length < s){
                    console.log("Остановка");
                    clearInterval(interval);
                } else {
                    $.ajax({
                        url: '/basic/api/api.php',
                        method: 'get',
                        dataType: 'html',
                        data: {page: page, country: country, date_to: date_to, date_from: date_from, source: utm_source},
                        success: function(data) {
                            
                            // Сортировка данных по переменным
                            $.each(JSON.parse(data), function(key, value) {
                                if(i == 0){
                                    api_data = value;
                                }
                                if(i == 1){
                                    api_full = value;
                                }
                                if(i == 2){
                                    api_account = value;
                                }
                                i++;
                            });
                            // Сортировка данных по переменным

                            console.log(data);
                            console.log(page);

                            // Проверка переменных
                            if(api_data.length == 0){
                                c++;
                                s++;
                                page = 0;
                                clearInterval(interval);
                                GetApi();
                            } else {
                                page++;
                            }
                            // Проверка переменных
                        
                            // Добавление данных в базу
                            $.each(api_data, function(key, value) {
                                $.ajax({
                                    url: '/basic/web/site/addapisend',
                                    method: 'get',
                                    dataType: 'html',
                                    data: {country: country, visits_count: value['visits_count'], ctr: value['ctr'], source: utm_source, utm_term: value['utm_term']},
                                    success: function(data_in) {
                                        console.log("Успешно добавлено");
                                    }
                                });
                                
                            } );
                            // Добавление данных в базу
                        }
                    });
                  
                }
            },6000);
        } else {
            console.log("Ошибка");
        }
    }

    // Создание интервала

    function getRandomInt(max) {
        return Math.floor(Math.random() * max);
    }

    function isEmpty(str) {
        if (str.trim() == '') 
            return true;
            
        return false;
    }

</script>
