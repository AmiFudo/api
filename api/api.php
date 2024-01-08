<?php

    // Формировния запроса

    $data = "";

    $post = "";

    $country = $_GET['country'];

    $source = $_GET['source'];

    $date_from = $_GET['date_from'];

    $date_to = $_GET['date_to'];

    $page = $_GET['page'];

    if($page > 0){
        if($source){
            $post = "https://api.luckyfeed.pro/v5/stats/full?groups[]=utm_source&groups[]=utm_term&count=50&filters[country_code]=".$country."&filters[utm_source]=".$source."&filters[date_from]=".$date_from."&filters[date_to]=".$date_to."&page=".$page."";
        } else {
            $post = "https://api.luckyfeed.pro/v5/stats/full?groups[]=utm_source&groups[]=utm_term&count=50&filters[country_code]=".$country."&filters[date_from]=".$date_from."&filters[date_to]=".$date_to."&page=".$page."";
        }
    }

    // Формировния запроса

    // Запрос к API

    $fields = array( 'type' => 'buy');
    $headers = array();
    $headers[] = "Private-Token: 482de56f694d1260c31053fd70ae6b6534171a66669b85a4091e2d4f431351477fadfd01f26af2be";
    $state_ch = curl_init();
    curl_setopt($state_ch, CURLOPT_URL, $post);
    
    // Запрос к API

    // Получение данных

    curl_setopt($state_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($state_ch, CURLOPT_HTTPHEADER, $headers);
    $state_result = curl_exec($state_ch);

    curl_close($state_ch);
    
    $data = parse_str($state_result, $data);

    $data = json_decode($state_result, true);

    $data = json_encode($data);
    
    echo $data;

    // Получение данных

?>

