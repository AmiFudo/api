<?php

$this->title = 'Данные API';

$black = array();

foreach($blacklist as $list){
    array_push($black, $list->utm_term);
}

?>

<p class="title">Данные API</p>

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
    <div class="btns">
        <button id="send" onclick="GetApi()">Запрос</button>
        <button id="send" onclick="Copy()">Скопировать в буфер</button>
        <button id="send" onclick="BlackListTable()">Добавить в чёрный список</button>
    </div>
</div>



<!-- Фильтры -->

<!-- Таблица -->

<div class="tables_api" style="display:block">
    <table>
        <tr>
            <th>Источник</th>
            <th>Площадка</th>
            <th>Пробив</th>
            <th>Количество кликов</th>
            <th>Страна</th>
            <th>Чёрный список</th>
        </tr>
        <?php
            $i = 0;
            foreach($model as $patch){
                if(in_array($patch->utm_term, $black)){
                    
                } else {
        ?>  
            <tr id="data" class="key_<?=$i?>">
                <td id="utm_source"><?=$patch->utm_source?></td>
                <td id="utm_term" class="utm_term"><?=$patch->utm_term?></td>
                <td id="ctr"><?=$patch->ctr?></td>
                <td id="visits_count"><?=$patch->visits_count?></td>
                <td id="country"><?=$patch->country?></td>
                <td id="list"><a class="black_list" onclick="BlackList(<?=$i?>)">Добавить в черный список</a></td>
            </tr>
        <?php
                }
            $i++;
            }
        ?>
    </table>
</div>

<!-- Таблица -->

<script>

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
                data: {country: $(element).find("#country").text(), utm_source: $(element).find("#utm_source").text(), utm_term: $(element).find("#utm_term").text(), ctr: $(element).find("#ctr").text(), visits_count: $(element).find("#visits_count").text(), status: 0},
                success: function(data) {
                    $('.tables_api table tbody tr.key_'+index+'').empty()
                }
            });
        });
    }

    function GetApi(){

        country = $('.country').val();
        utm_source = $('input#utm_source').val();
        vs_int = $('input#vs_int').val();
        ctr_int = $('input#ctr_int').val();

        if(isEmpty(vs_int)){
            vs_int = 0;
        }

        if(isEmpty(ctr_int)){
            ctr_int = 0;
        }

        if(isEmpty(utm_source)){
            utm_source = 0;
        }

        if(isEmpty(country)){
            alert('Проверьте данные');
        } else {
            console.log(vs_int)
            console.log(ctr_int)
            console.log(utm_source)
            console.log(country)
            $.ajax({
            url: '/basic/web/site/listapifilter',
            method: 'get',
            dataType: 'html',
            data: {country: country, source: utm_source, vs_int: vs_int, ctr_int: ctr_int},
            success: function(data){

                $('.tables_api table tr#data').remove();
                console.log(data);
                $.each(JSON.parse(data), function(key, value) {
                    $.ajax({
                        url: '/basic/web/blacklist/checkblacklist',
                        method: 'get',
                        dataType: 'html',
                        data: {utm_term: value['utm_term'], status: 0},
                        success: function(data_in) {
                            if(data_in == 0){
                                blocks = '<tr id="data" class="key_'+key+'"><td id="utm_source">'+value['utm_source']+'</td><td id="utm_term">'+value['utm_term']+'</td><td id="ctr">'+value['ctr']+'</td><td id="visits_count">'+value['visits_count']+'</td><td id="country">'+value['country']+'</td><td id="list"><a class="black_list" onclick="BlackList('+key+')">Добавить в чёрный список</a></td></tr>';
                                $('.tables_api table tbody').append(blocks);
                            }
                        }
                    });
                 
                });

            },
            error: function (error) {
                alert('Данные не найдены');
            }
            });
        }


    }

    function BlackList(key){
        
        utm_source = $('.tables_api tr.key_'+key+' td#utm_source').text();
        utm_term = $('.tables_api tr.key_'+key+' td#utm_term').text();
        ctr = $('.tables_api tr.key_'+key+' td#ctr').text();
        visits_count = $('.tables_api tr.key_'+key+' td#visits_count').text();
        country = $('.tables_api tr.key_'+key+' td#country').text();

        console.log(utm_source);
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

    function isEmpty(str) {
        if (str.trim() == '') 
            return true;
            
        return false;
    }
</script>