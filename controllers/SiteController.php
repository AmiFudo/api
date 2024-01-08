<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\SignupForm;
use app\models\ContactForm;
use app\models\Blacklist;
use app\models\Country;
use app\models\Source;
use app\models\Apisend;
use app\models\Apiurl;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout','api','addcountry','addsource','deletecountry','deletesource','autoapi','listautoapi'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    
    // Api
    public function actionApi()
    {
        $country = new Country();
        $country_list = Country::find()->all();

        $model = new Blacklist();
        $data = Blacklist::find()->all();

        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->render('api', [
            'data' => $data,
            'country' => $country_list,
        ]);
    }

    // AutoApiIndex
    public function actionAutoapi()
    {
        
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $apiurl = new Apiurl();

        $country = new Country();
        $country_list = Country::find()->all();

        $source = new Source();
        $source_list = Source::find()->all();

        return $this->render('auto', [
            'country' => $country_list,
            'source' => $source_list,
        ]);
    }

    // AddCountry
    public function actionAddcountry()
    {
        $country_code = $_GET['country_code'];

        $model = new Country();

        $model->country_code = $country_code;

        $model->save();
    }

    // AddSource
    public function actionAddsource()
    {
        $utm_source = $_GET['utm_source'];

        $model = new Source();

        $model->utm_source = $utm_source;

        $model->save();
    }

    // DeleteCountry
    public function actionDeletecountry()
    {
        $id = $_GET['id'];
        $model = new Country();
        $data = Country::find()->where(['id'=>$id])->one();
        $data->delete();
    }

    // DeleteSource
    public function actionDeletesource()
    {
        $id = $_GET['id'];
        $source = $_GET['source'];

        $model = new Source();
        $data = Source::find()->where(['id'=>$id])->one();

        $models = new Apiurl();
        $data_url = Apiurl::deleteAll(['utm_source'=>$source]);

        $data->delete();
    }

    // AutoApiSend
    public function actionAutosend()
    {

        $model = new Apiurl();
        $model = Apiurl::find()->all();

        return $this->render('autosend', [
            'model' => $model,
        ]);
        
    }

    // AddApiSend
    public function actionAddapisend()
    {
        $country = $_GET['country'];
        $visits_count = $_GET['visits_count'];
        $ctr = $_GET['ctr'];
        $source = $_GET['source'];
        $utm_term = $_GET['utm_term'];

        if($visits_count > 30 && $ctr < 10){

            $model = new Apisend();

            $model->utm_source = $source;
            $model->utm_term = $utm_term;
            $model->ctr = $ctr;
            $model->visits_count = $visits_count;
            $model->country = $country;
            $model->status = 1;
    
            $model->save();
        }
    }

    // Вывод данных Api
    public function actionListautoapi()
    {   

        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Apisend();
        $model_list = Apisend::find()->all();

        $country = new Country();
        $country_list = Country::find()->all();

        $blacklist = new Blacklist();
        $blacklist = Blacklist::find()->all();
        
        return $this->render('listautoapi', [
            'model' => $model_list,
            'country' =>  $country_list,
            'blacklist' => $blacklist,
        ]);

    }

    // Фильтрация данных
    public function actionListapifilter()
    {
        $country = $_GET['country'];
        $utm_source = $_GET['source'];
        $vs_int = $_GET['vs_int'];
        $ctr_int = $_GET['ctr_int'];

        $data = array();

        $data_list = array();

        $model = new Apisend();

        $send = "";

        $vs_int_send = ['>', 'visits_count', (int)$vs_int];

        if($ctr_int == 0){
            $ctr_int_send = ['>', 'ctr', 0];
        } else {
            $ctr_int_send = ['<', 'ctr', (int)$ctr_int];
        }

        if($utm_source == 0){
            $send = ['country'=>$country];
        } else {
            $send = ['country'=>$country, 'utm_source'=>$utm_source];
        }

        $model = Apisend::find()->where($send)->andWhere($vs_int_send)->andWhere($ctr_int_send)->all();

        // print_r($model);
        // exit;

        foreach($model as $patch){
            // print_r($patch->country);
          
            $data['utm_source'] = $patch->utm_source;
            $data['country'] = $patch->country;
            $data['visits_count'] = $patch->visits_count;
            $data['ctr'] = $patch->ctr;
            $data['utm_term'] = $patch->utm_term;

            array_push($data_list, $data);
            $key = json_encode($data_list);
            
        }

        print_r($key);

        exit;
    }

    // Чистка базы данных
    public function actionDeleteallapi()
    {

        $token = $_GET['token'];

        if($token = "sadkjlnnv34jlkasjd98@3laskdjmhg"){

            $model = new Apisend();

            $data = Apisend::deleteAll(['status'=>1]);

        }
    }

    public function actionUrlapi()
    {
        $source = $_GET['source'];

        $model = new Apiurl();
        $model = Apiurl::find()->where(['utm_source'=>$source])->all();

        $data = array();

        foreach($model as $patch){

            array_push($data, $patch->country_code);

            $key = json_encode($data);

        }

        print_r($key);


    }

    public function actionUrlapiadd()
    {

        $country = $_GET['country'];
        $source = $_GET['source'];

        $model = new Apiurl();

        $model->country_code = $country;
        $model->utm_source = $source;

        $model->save();

    }

    public function actionUrlapidelete()
    {
        $country = $_GET['country'];
        $source = $_GET['source'];

        $model = new Apiurl();

        $model = Apiurl::find()->where(['country_code'=>$country, 'utm_source'=>$source])->one();
        $model->delete();
    }

    public function actionAutoapisend(){

        set_time_limit(1200);

        $models = new Apisend();
        $data = Apisend::deleteAll(['status'=>1]);

        $model = new Apiurl();
        $model = Apiurl::find()->all();

        $date_from = "";

        $Date = new \DateTime(date('Y-m-d'));
        $shift = -1;

        $day = $Date->format('d');
        $Date->modify('first day of this month')->modify(($shift > 0 ? '+':'') . $shift . ' months');
        $day = $day > $Date->format('t') ? $Date->format('t') : $day;
        $date_from =  $Date->modify('+' . $day-1 . ' days')->format('Y-m-d');

        // Формировния запроса

        $date_from = $date_from;
    
        $date_to = date('Y-m-d');

        $data_post = [];
        
        $data_list = [];
    
        foreach($model as $patch){

            $country = $patch->country_code;
            $source = $patch->utm_source;

            $data = "";
           
            $page = 1;

            if($country == "RU"){
                $filter_country = "RUB";
            } else {
              $filter_country = "USD";
            }

            while($page < 100){

                $loader = new ApiLoader();

                $loader->country_code = $country;
                $loader->utm_source = $source;
                $loader->page = $page;
                $loader->status = 1;
    
                $loader->save();

                $post = "";

                if($page > 0){
                    if($source){
                        $post = "https://api.luckyfeed.pro/v5/stats/full?groups[]=utm_source&groups[]=utm_term&count=50&filters[country_code]=".$country."&filters[wallet_currency]=".$filter_country."&filters[utm_source]=".$source."&filters[date_from]=".$date_from."&filters[date_to]=".$date_to."&page=".$page."";
                    } else {
                        $post = "https://api.luckyfeed.pro/v5/stats/full?groups[]=utm_source&groups[]=utm_term&count=50&filters[country_code]=".$country."&filters[wallet_currency]=".$filter_country."&filters[date_from]=".$date_from."&filters[date_to]=".$date_to."&page=".$page."";
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
            
                // $data = json_encode($data);

                if($data){
                    array_push($data_list, $data);
                }                
            
                // Получение данных

                sleep(2);

                $page++;

            }

        }  

        foreach($data_list as $patch){
          
            foreach($patch['data'] as $value){

                $models = new Apisend();

                $country = $value['country_code'];
                $visits_count = $value['visits_count'];
                $ctr = $value['ctr'];
                $source = $value['utm_source'];
                $utm_term = $value['utm_term'];

                if($visits_count >= 30 && $ctr <= 10)
        
                $models->utm_source = $source;
                $models->utm_term = $utm_term;
                $models->ctr = (string)$ctr;
                $models->visits_count = (string)$visits_count;
                $models->country = $country;
                $models->status = 1;
        
                $models->save();
            }
        }
        
    }
}
