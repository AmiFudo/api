<?php 

$this->title = 'Автоматизация API';

$token = "sadkjlnnv34jlkasjd98@3laskdjmhg";

?>

<p class="title">Автоматизация API</p>

<!-- Блок страны и источника -->

<div class="from_auto">
    <div class="from_auto_input">
        <span>Источник:</span>
        <select class="source">
            <?php
                foreach($source as $value){
            ?>
                <option value="<?=$value->utm_source?>" data-id="<?=$value->id?>"><?=$value->utm_source?></option>
            <?php
                }
            ?>
        </select>
        <button id="add_setting" onclick="OpenBlockSetting()" data-text="Источник">Настройка</button>
        <button id="add_source" onclick="OpenBlockAdd('Источник', 2)" data-text="Источник">Добавить</button>
        <button id="delete_source" onclick="DeleteInfo(2)">Удалить</button>
    </div>
</div>

<!-- Блок страны и источника -->

<a href="/basic/web/site/listautoapi" class="link">Показать данные</a>
<a href="/basic/web/site/autosend?token=<?=$token?>" class="link">Обновить данные</a>

<p class="title">Настройка стран</p>
<div class="from_auto">
    <div class="from_auto_input">
        <span>Страна:</span>
        <select class="country">
            <?php
                foreach($country as $value){
            ?>
                <option value="<?=$value->country_code?>" data-id="<?=$value->id?>"><?=$value->country_code?></option>
            <?php
                }
            ?>
        </select>
        <select class="country_list" style="display: none">
            <?php
                foreach($country as $value){
            ?>
                <option value="<?=$value->country_code?>" data-id="<?=$value->id?>"><?=$value->country_code?></option>
            <?php
                }
            ?>
        </select>
        <button id="add_country" onclick="OpenBlockAdd('Страна', 1)" data-text="Страна">Добавить</button>
        <button id="delete_country" onclick="DeleteInfo(1)">Удалить</button>
    </div>
</div>

<!-- Блок добавления -->

<div class="add_block">
    <div class="add_block_close" onclick="CloseBlockAdd()"></div>
    <div class="add_block__list">
        <p class="title"></p>
    </div>
</div>

<!-- Блок добавления -->

<!-- Блок настройки -->

<div class="setting_block">
    <div class="add_block_close" onclick="CloseBlockAdd()"></div>
    <div class="add_block__list">
        <p class="title"></p>
        <select class="country_setting">

        </select>
    </div>
</div>

<!-- Блок настройки -->

<script>

    // Блок настройки 

    function OpenBlockSetting(){

        $('.setting_block .add_block__list select.country_setting').empty();
        $('.setting_block').css('width','100%');
       
        source = $('select.source').val();
        block = '';

        $('.setting_block .add_block__list select.country_setting').append('<option value="Данных нет">Данных нет</option>');

        $.ajax({
            url: '/basic/web/site/urlapi',
            method: 'get',
            dataType: 'html',
            data: {source: source},
            success: function(data){
                $('.setting_block .add_block__list select.country_setting').empty();
                $.each(JSON.parse(data), function(key, value) {
                    console.log(value);
                    $('.setting_block .add_block__list select.country_setting').append('<option value="'+value+'">'+value+'</option>');
                });
            }
        });

        $('.setting_block .add_block__list p.title').text('Настройка источника');
        $('.setting_block .add_block__list').append('<button id="add_country" onclick="OpenBlockAddCountry()">Добавить</button>');
        $('.setting_block .add_block__list').append('<button id="add_country" onclick="DeleteUrlSetting()">Удалить</button>');
      
    }

    // Блок настройки 

    function OpenBlockAddCountry(){

        CloseBlockAdd();

        country = $('select.country_list');

        $('select.country_list').css('display','inline');

        $('.add_block').css('width','100%');
        $('.add_block .add_block__list p.title').text('Добавление страны в источник');
        $('.add_block .add_block__list').append(country);
        $('.add_block .add_block__list').append('<button id="add_country" onclick="AddUrlApi()">Добавить</button>')
    }

    // Закрытие блока добавления

    function AddUrlApi(){

        country = $('select.country_list').val();

        source = $('select.source').val();

        $.ajax({
            url: '/basic/web/site/urlapiadd',
            method: 'get',
            dataType: 'html',
            data: {country: country, source: source},
            success: function(data){

                CloseBlockAdd();

                OpenBlockSetting();

            }
        });
    }

    function DeleteUrlSetting(){
        country = $('select.country_setting').val();

        source = $('select.source').val();

        $.ajax({
            url: '/basic/web/site/urlapidelete',
            method: 'get',
            dataType: 'html',
            data: {country: country, source: source},
            success: function(data){

                CloseBlockAdd();

                OpenBlockSetting();

            }
        });
    }

    function CloseBlockAdd(){
        $('.add_block').css('width','0%');
        $('.add_block .add_block__list button').remove();
        setTimeout(() => {
            $('.add_block .add_block__list input').remove();
        }, 400);

        $('.setting_block').css('width','0%');
        $('.setting_block .add_block__list button').remove();
        setTimeout(() => {
            $('.setting_block .add_block__list input').remove();
        }, 400);

        $('select.country_list').css('display','none')
    }

    // Закрытие блока добавления


    // Открытие блока добавления

    function OpenBlockAdd(text, number){
        $('.add_block').css('width','100%');
        $('.add_block .add_block__list p.title').text(text);
        $('.add_block .add_block__list').append('<div class="inputs"><input type="text" id="add_text" placeholder="Введите данные"><button id="add" onclick="AddInfo('+number+')">Добавить</button></div>');
    }

    // Открытие блока добавления


    // Добавление данных

    function AddInfo(number){

        data = $('.add_block .add_block__list input#add_text').val();

        if(number == 1){
            $.ajax({
                url: '/basic/web/site/addcountry',
                method: 'get',
                dataType: 'html',
                data: {country_code: data},
                success: function(data){
                    $('.add_block .add_block__list').empty();
                    $('.add_block .add_block__list').append('<p class="title">Успешно добавлено</p>');
                }
            });
        }

        if(number == 2){
            $.ajax({
                url: '/basic/web/site/addsource',
                method: 'get',
                dataType: 'html',
                data: {utm_source: data},
                success: function(data){
                    $('.add_block .add_block__list').empty();
                    $('.add_block .add_block__list').append('<p class="title">Успешно добавлено</p>'); 
                }
            });
        }

    }

    // Добавление данных


    // Удаление данных

    function DeleteInfo(number){
    
        if(number == 1){
            country = $('.country option:selected').data('id');
            $.ajax({
                url: '/basic/web/site/deletecountry',
                method: 'get',
                dataType: 'html',
                data: {id: country},
                success: function(data){
                    $('.add_block').css('width','100%');
                    $('.add_block .add_block__list').empty();
                    $('.add_block .add_block__list').append('<p class="title">Успешно удалено</p>');
                }
            });
        }

        if(number == 2){
            source = $('.source option:selected').data('id');
            source_val = $('.source option:selected').val();
            $.ajax({
                url: '/basic/web/site/deletesource',
                method: 'get',
                dataType: 'html',
                data: {id: source, source:source_val},
                success: function(data){
                    $('.add_block').css('width','100%');
                    $('.add_block .add_block__list').empty();
                    $('.add_block .add_block__list').append('<p class="title">Успешно удалено</p>'); 
                }
            });
        }

    }

    // Удаление данных

</script>