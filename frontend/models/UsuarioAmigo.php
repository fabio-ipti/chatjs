<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "usuario_amigo".
 *
 * @property integer $id
 * @property integer $usuario_id
 * @property integer $amigo_id
 *
 * @property Usuario $usuario
 * @property Usuario $amigo
 */
class UsuarioAmigo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuario_amigo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_id', 'amigo_id'], 'required'],
            [['usuario_id', 'amigo_id'], 'integer'],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_id' => 'id']],
            [['amigo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['amigo_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'amigo_id' => Yii::t('app', 'Amigo ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'usuario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmigo()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'amigo_id']);
    }
}
