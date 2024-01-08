<div class="tables_api" style="display:block">
    <table>
        <tr>
            <th>Источник</th>
            <th>Площадка</th>
            <th>Пробив</th>
            <th>Количество кликов</th>
            <th>Страна</th>
        </tr>
        <?php
            foreach($model as $patch){
        ?>
            <tr id="data">
                <td id="utm_source"><?=$patch->utm_source?></td>
                <td id="utm_term"><?=$patch->utm_term?></td>
                <td id="ctr"><?=$patch->ctr?></td>
                <td id="visits_count"><?=$patch->visits_count?></td>
                <td id="country"><?=$patch->country?></td>
            </tr>
        <?php
            }
        ?>
    </table>
</div>