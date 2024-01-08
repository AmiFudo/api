<?php

$this->title = 'Чёрный список';

?>
<p class="title">Чёрный список</p>
<?php if($data) { ?>
<div class="tables_api blacklist">
    <table>
        <tbody>
            <tr>
                <th>Источник</th>
                <th>Список площадок</th>
                <th>Страна</th>
                <th>Чёрный список</th>
            </tr>
            <?php

            $patch_if = [];
            $patch_if_country = [];
            $i = 0;
        
            foreach($data as $patch){
                if(in_array($patch->utm_source, $patch_if) && in_array($patch->country, $patch_if_country))
                {

                } else {
                    ?>
                        <tr id="data" class="id_<?=$patch->utm_source?>">
                            <td><?=$patch->utm_source?></td>
                            <td><a onclick="BlackListTerm('<?=$patch->utm_source?>', '<?=$patch->country?>')" class="list_black">Показать</a></td>
                            <td><?=$patch->country?></td>
                            <td><a class="black_list" onclick="BlackListDelete('<?=$patch->utm_source?>')">Убрать из списка</a></td>
                        </tr>
                    <?php
                }
                array_push($patch_if, $patch->utm_source);
                array_push($patch_if_country, $patch->country);
            }

            ?>

        </tbody>
    </table>
</div>

<?php } else { ?>
    <p>Список пуст</p>
<?php } ?>

<div class="add_block">
    <div class="add_block_close" onclick="CloseBlockAdd()"></div>
    <div class="add_block__list">
        <p class="title"></p>
        <span id="utm_source" style="display:none"></span>
        <span id="country" style="display:none"></span>
        <textarea id="term" rows="10">
        </textarea>
        <textarea id="term_full" rows="10" style="display:none">
        </textarea>
        <button class="save_black" id="send" onclick="BlackListSave()">Сохранить</button>
    </div>
</div>

<p class="title">Добавление в чёрный список</p>

<div class="form_api">
    <input type="text" id="utm_source" placeholder="Источник">
    <input type="text" id="utm_term" placeholder="Площадка">
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
    <button id="send" onclick="BlackList()">Добавить</button>
</div>

<script>

    function BlackListSave(){

        const result = document.getElementById('term').value.split('\n');
        const result_full = document.getElementById('term_full').value.split('\n');

        console.log(result_full)

        $.each(result_full, function(key, value) {
            if(!isEmpty(value)){
                $.ajax({
                    url: '/basic/web/blacklist/deleteblacklistutm',
                    method: 'get',
                    dataType: 'html',
                    data: {utm_term: value},
                    success: function(data) {
                        console.log('Удалено');
                    }
                });       
            }
        });

        $.each(result, function(key, value) {
            if(!isEmpty(value)){
                $.ajax({
                    url: '/basic/web/blacklist/checkblacklist',
                    method: 'get',
                    dataType: 'html',
                    data: {utm_term: value, status: 1},
                    success: function(data_in) {
                        if(data_in == 0){
                            $.ajax({
                                url: '/basic/web/blacklist/addblacklist',
                                method: 'get',
                                dataType: 'html',
                                data: {country: $('span#country').text(), utm_source: $('span#utm_source').text(), utm_term: value, ctr: 0, visits_count: 0, status: 1},
                                success: function(data) {
                                }
                            });
                        }
                    }
                });        
            }
        });
        CloseBlockAdd();
    }


    function BlackList(){
        utm_source = $('input#utm_source').val();
        utm_term = $('input#utm_term').val();
        ctr = $('input#ctr_int').val();
        visits_count = $('input#vs_int').val();
        country = $('.country').val();

        if(isEmpty(ctr)){
            ctr = 0;
        }
        if(isEmpty(visits_count)){
            visits_count = 0;
        }

        if(!isEmpty(utm_source) || !isEmpty(utm_term)){
            $.ajax({
                url: '/basic/web/blacklist/addblacklist',
                method: 'get',
                dataType: 'html',
                data: {country: country, utm_source: utm_source, utm_term: utm_term, ctr: ctr, visits_count: visits_count, status: 0},
                success: function(data) {
                    alert("Успешно добавлено");
                    $('input').val('');
                }
            });
        } else {
            alert("Проверьте данные");
        }

    }

    function BlackListDelete(utm_source){
        $.ajax({
            url: '/basic/web/blacklist/deleteblacklist',
            method: 'get',
            dataType: 'html',
            data: {utm_source: utm_source},
            success: function(data) {
                $('.blacklist table tbody tr.id_'+utm_source+'').empty()
            }
        });
    }

    function BlackListDeleteUtm(key){  
        utm_term = $('tr.key_'+key+' td#utm_term').text();        
        $.ajax({
            url: '/basic/web/blacklist/deleteblacklistutm',
            method: 'get',
            dataType: 'html',
            data: {utm_term: utm_term},
            success: function(data) {
                $('tr.key_'+key+'').empty()
            }
        });
    }

    function BlackListTerm(utm_source, country){
        const externalDataRetrievedFromServer = [];
        countrys = "";
        term = "";
        $.ajax({
            url: '/basic/web/blacklist/blacklistterm',
            method: 'get',
            dataType: 'html',
            data: {utm_source: utm_source, country: country},
            success: function(data) {
                console.log(data);
                $.each(JSON.parse(data), function(key, value) {
                    countrys = value['country'];
                    term = value['utm_source'];
                    $('.add_block').css('width','100%');
                    externalDataRetrievedFromServer.push(value['utm_term'] + "\r\n");
                });

                strt = externalDataRetrievedFromServer.join('')
                $('textarea#term').val(strt);
                $('textarea#term_full').val(strt);
                $('.add_block p.title').text(utm_source+' '+countrys);
                $('span#utm_source').text(utm_source);
                $('span#country').text(country);
            }
        });
    }

    function CloseBlockAdd(){
        $('.add_block').css('width','0%');
        $('.add_block .add_block__list table tbody tr#data').remove();
    }

    function isEmpty(str) {
        if (str.trim() == '') 
            return true;
            
        return false;
    }
</script>