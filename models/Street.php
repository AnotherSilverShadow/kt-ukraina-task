<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "streets".
 *
 * @property int $id
 * @property string $name
 * @property string $ref
 * @property string $city_ref
 *
 * @property City $cityRef
 */
class Street extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'streets';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'ref', 'city_ref'], 'required'],
            [['name', 'ref', 'city_ref'], 'string', 'max' => 255],
            [['ref'], 'unique'],
            [
                ['city_ref'],
                'exist',
                'skipOnError' => true,
                'targetClass' => City::className(),
                'targetAttribute' => ['city_ref' => 'ref']
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ref' => 'Ref',
            'city_ref' => 'City Ref',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityRef()
    {
        return $this->hasOne(City::className(), ['ref' => 'city_ref']);
    }
}
