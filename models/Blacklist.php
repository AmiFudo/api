<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blacklist".
 *
 * @property int $id
 * @property string $utm_term
 * @property int $ctr
 * @property int $visits_count
 */
class Blacklist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{blacklist}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utm_term', 'ctr', 'visits_count'], 'required'],
            [['utm_term', 'ctr','utm_source'], 'string'],
            [['visits_count'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'utm_term' => 'Utm Term',
            'ctr' => 'Ctr',
            'visits_count' => 'Visits Count',
            'utm_source' => 'Utm Source'
        ];
    }
}