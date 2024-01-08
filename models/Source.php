<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "source".
 *
 * @property int $id
 * @property string $utm_source
 */
class Source extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utm_source'], 'required'],
            [['utm_source'], 'string'],
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
        ];
    }
}

?>