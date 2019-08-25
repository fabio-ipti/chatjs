<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "mensagem".
 *
 * @property integer $id
 * @property integer $remetente_id
 * @property integer $receptor_id
 * @property string $mensagem
 * @property string $data
 *
 * @property Usuario $remetente
 * @property Usuario $receptor
 */
class Mensagem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mensagem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remetente_id', 'receptor_id', 'mensagem', 'data'], 'required'],
            [['remetente_id', 'receptor_id'], 'integer'],
            [['mensagem'], 'string'],
            [['data'], 'string', 'max' => 10],
            [['remetente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['remetente_id' => 'id']],
            [['receptor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['receptor_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'remetente_id' => Yii::t('app', 'Remetente ID'),
            'receptor_id' => Yii::t('app', 'Receptor ID'),
            'mensagem' => Yii::t('app', 'Mensagem'),
            'data' => Yii::t('app', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRemetente()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'remetente_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptor()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'receptor_id']);
    }
}
