<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_send".
 *
 * @property int $id
 * @property string $utm_source
 * @property string $utm_term
 * @property string $ctr
 * @property string $visits_count
 * @property string $country
 * @property string $status
 */
class Apisend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_send';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utm_source', 'utm_term', 'ctr', 'visits_count', 'country'], 'required'],
            [['utm_source', 'utm_term', 'ctr', 'visits_count', 'country'], 'string'],
            [['status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'utm_source' => 'Utm Source',
            'utm_term' => 'Utm Term',
            'ctr' => 'Ctr',
            'visits_count' => 'Visits Count',
            'country' => 'Country',
        ];
    }
}

?>