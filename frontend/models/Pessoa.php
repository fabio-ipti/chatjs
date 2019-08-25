<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "pessoa".
 *
 * @property integer $id
 * @property string $nome
 * @property string $data_nasc
 * @property string $email
 * @property string $codigo
 *
 * @property Usuario[] $usuarios
 */
class Pessoa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pessoa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'data_nasc', 'email', 'codigo'], 'required'],
            [['nome'], 'string', 'max' => 50],
            [['data_nasc'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 80],
            [['codigo'], 'string', 'max' => 13],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'data_nasc' => Yii::t('app', 'Data Nasc'),
            'email' => Yii::t('app', 'Email'),
            'codigo' => Yii::t('app', 'Codigo'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['pessoa_id' => 'id']);
    }
}
