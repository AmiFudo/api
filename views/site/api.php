<?php

$this->title = 'Запрос к API';

// Настройка даты на месяц назад

$date_from = "";

$Date = new DateTime(date('Y-m-d'));
$shift = -1;

$day = $Date->format('d');
$Date->modify('first day of this month')->modify(($shift > 0 ? '+':'') . $shift . ' months');
$day = $day > $Date->format('t') ? $Date->format('t') : $day;
$date_from =  $Date->modify('+' . $day-1 . ' days')->format('Y-m-d');

// Настройка даты на месяц назад

?>

<p class="title">Запрос к API</p>

<!-- Фильтры -->

<div class="form_api">
    <input type="text" id="utm_source" placeholder="Источник">
    <input type="text" id="vs_int" placeholder="Количество кликов">
    <input type="text" id="ctr_int" placeholder="Пробив">
    <select class="country">
        <?php
            foreach($country as $value){
        ?>
            <option value="<?=$value->country_code?>"><?=$value->country_code?></option>
        <?php
            }
        ?>
    </select>
    <input type="date" id="date_from" value="<?=$date_from?>" name="trip-start"/>
    <input type="date" id="date_to" value="<?=date('Y-m-d')?>" name="trip-start"/>
    <div class="btns">
        <button id="send" onclick="GetApi()">Запрос</button>
        <button id="send_copy" onclick="Copy()">Скопировать в буфер</button>
        <button id="send_table" onclick="BlackListTable()">Добавить в чёрный список</button>
    </div>

</div>

<!-- Фильтры -->

<!-- Таблица в которой будут содержаться данные -->

<div class="tables_api">
    <table>
        <tr>
            <th>Источник</th>
            <th>Площадка</th>
            <th>Пробив</th>
            <th>Количество кликов</th>
            <th>Страна</th>
        </tr>
    </table>
</div>

<!-- Таблица в которой будут содержаться данные -->

<script>
    status = 0;
    page = 1;

    function Copy(){

        const externalDataRetrievedFromServer = [];

        var input = 0;
        $('table  tr').each(function (index, element) {
            externalDataRetrievedFromServer.push($(element).find("#utm_term").text() + "\r\n");
        });

        strt = externalDataRetrievedFromServer.join('');
        strt = strt.replace(/^\s*[\r\n]/gm, '');
        input = $('<textarea>').val(strt).appendTo('body').select();
        document.execCommand('copy');
        alert("Текст успешно скопирован в буфер обмена!");

        $('textarea').remove();

    }

    function BlackListTable(){
        $('table  tr').each(function (index, element) {
            $.ajax({
                url: '/basic/web/blacklist/addblacklist',
                method: 'get',
                dataType: 'html',
                data: {country: $(element).find("#country").text(), utm_source: $(element).find("#utm_source p").text(), utm_term: $(element).find("#utm_term").text(), ctr: $(element).find("#ctr").text(), visits_count: $(element).find("#visits_count").text(), status: 0},
                success: function(data) {
                    $('.tables_api table tbody tr.key_'+index+'').empty()
                }
            });
        });
    }
    
    function GetApi(){
        $('.tables_api').css('display','none');
        $('.tables_api table tr#data').empty();
        interval = setInterval(ApiUrl,6000);
    }

    function ApiUrl(){
        blocks = '';
        i = 0;
        d = 0;
        key_class = 0;
        ctr_int = 0;
        vs_int = 0;
        country = $('.country').val();
        date_to = $('input#date_to').val();
        date_from = $('input#date_from').val();
        utm_source = $('input#utm_source').val();

        if($('input#vs_int').val()){
            vs_int = $('input#vs_int').val();
        }

        if($('input#vs_int').val()){
            ctr_int = $('input#ctr_int').val();
        }

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

                // Проверка страницы
                if(api_data.length == 0){
                    clearInterval(interval);
                }
                // Проверка страницы
               
                // Получение данных и вывод
                $.each(api_data, function(key, value) {
                    if(isEmpty(utm_source)){                         
                        utm_source = value['utm_source'];
                    } else {
                        utm_source = value['utm_source'];
                    }
                    $.ajax({
                        url: '/basic/web/blacklist/checkblacklist',
                        method: 'get',
                        dataType: 'html',
                        data: {utm_term: value['utm_term'], status: 0},
                        success: function(data_in) {
                            
                            random = getRandomInt(10000);
                            
                            if(key == 0){
                                key_class = 1;
                            } else {
                                key_class = key;
                            }
                           
                            
                            if(data_in == 0){
                                if(value['visits_count'] >= vs_int && value['ctr'] <= ctr_int){                                    
                                    blocks = '<tr id="data" class="key_'+key+''+random+'"><td id="utm_source"><p>'+value['utm_source']+'</p> <a class="black_list" onclick="BlackList('+key+''+random+')">Добавить в чёрный список</a></td><td id="utm_term">'+value['utm_term']+'</td><td id="ctr">'+value['ctr']+'</td><td id="visits_count">'+value['visits_count']+'</td><td id="country">'+value['country_code']+'</td></tr>';
                                    $('.tables_api table tbody').append(blocks);
                                }
                            }

                            
                        }
                    });
                    
                } );
                // Получение данных и вывод
                
                
                $('.tables_api').css('display','block');
            }
        });
        $('button#send').text('Обновить');
        page++;
    }

    function getRandomInt(max) {
        return Math.floor(Math.random() * max);
    }

    function isEmpty(str) {
        if (str.trim() == '') 
            return true;
            
        return false;
    }


    // Добавление в черный список
    
    function BlackList(key){
        console.log(key);
        utm_source = $('tr.key_'+key+' td#utm_source p').text();
        utm_term = $('tr.key_'+key+' td#utm_term').text();
        ctr = $('tr.key_'+key+' td#ctr').text();
        visits_count = $('tr.key_'+key+' td#visits_count').text();
        country = $('.country').val();
        $.ajax({
            url: '/basic/web/blacklist/addblacklist',
            method: 'get',
            dataType: 'html',
            data: {country: country, utm_source: utm_source, utm_term: utm_term, ctr: ctr, visits_count: visits_count, status: 0},
            success: function(data) {
                $('.tables_api table tbody tr.key_'+key+'').empty()
            }
        });
    }

    // Добавление в черный список

</script>
