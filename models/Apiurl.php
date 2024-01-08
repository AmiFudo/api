<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_url".
 *
 * @property int $id
 * @property string $country_code
 * @property string $utm_source
 */
class Apiurl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_code', 'utm_source'], 'required'],
            [['country_code', 'utm_source'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_code' => 'Country Code',
            'utm_source' => 'Utm Source',
        ];
    }
}
?>