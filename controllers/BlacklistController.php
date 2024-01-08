<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Blacklist;
use app\models\Country;

class BlacklistController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['index','addblacklist','deleteblacklist','checkblacklist'],
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

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Blacklist();
        $data = Blacklist::find()->all();

        $country = new Country();
        $country_list = Country::find()->all();

        return $this->render('index',[
            'data' => $data,
            'country' => $country_list,
        ]);
    }

    public function actionAddblacklist()
    {
        $utm_source = $_GET['utm_source'];
        $utm_term = $_GET['utm_term'];
        $ctr = $_GET['ctr'];
        $visits_count = $_GET['visits_count'];
        $country = $_GET['country'];
        $status = $_GET['status'];

        $model = new Blacklist();

        $model->utm_term = $utm_term;
        $model->ctr = $ctr;
        $model->visits_count = $visits_count;
        $model->utm_source = $utm_source;
        $model->country = $country;

        $model->save();

 
    }

    public function actionDeleteblacklist()
    {
        $utm_source = $_GET['utm_source'];
        $model = new Blacklist();
        $data = Blacklist::deleteAll(['utm_source'=>$utm_source]);
    }
    
    public function actionDeleteblacklistutm()
    {
        $utm_term = $_GET['utm_term'];
        $model = new Blacklist();
        $data = Blacklist::deleteAll(['utm_term'=>$utm_term]);
    }

    public function actionCheckblacklist()
    {
        $utm_term = $_GET['utm_term'];
        $status = $_GET['status'];

        $model = new Blacklist();
        $data = Blacklist::find()->where(['utm_term'=>$utm_term])->one();
        if($data){
            return 1;
        } else {
            return 0;
        }

    }

    public function actionBlacklistterm()
    {
        $utm_source = $_GET['utm_source'];

        $country = $_GET['country'];

        $data = array();

        $data_list = array();
    
        $model = new Blacklist();
        $model = Blacklist::find()->where(['utm_source'=>$utm_source, 'country'=>$country])->all();

        foreach($model as $patch){
            $data['id'] = $patch->id;
            $data['utm_source'] = $patch->utm_source;
            $data['utm_term'] = $patch->utm_term;
            $data['ctr'] = $patch->ctr;
            $data['visits_count'] = $patch->visits_count;
            $data['country'] = $patch->country;

            array_push($data_list, $data);
            $key = json_encode($data_list);
        }

        print_r($key);

        exit;
    }

}
