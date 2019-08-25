<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property integer $id
 * @property integer $pessoa_id
 * @property string $usuario
 * @property string $senha
 *
 * @property Mensagem[] $mensagems
 * @property Mensagem[] $mensagems0
 * @property Pessoa $pessoa
 * @property UsuarioAmigo[] $usuarioAmigos
 * @property UsuarioAmigo[] $usuarioAmigos0
 */
class Usuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pessoa_id', 'usuario', 'senha'], 'required'],
            [['pessoa_id'], 'integer'],
            [['usuario', 'senha'], 'string', 'max' => 30],
            [['usuario'], 'unique'],
            [['pessoa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pessoa::className(), 'targetAttribute' => ['pessoa_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pessoa_id' => Yii::t('app', 'Pessoa ID'),
            'usuario' => Yii::t('app', 'Usuario'),
            'senha' => Yii::t('app', 'Senha'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMensagems()
    {
        return $this->hasMany(Mensagem::className(), ['remetente_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMensagems0()
    {
        return $this->hasMany(Mensagem::className(), ['receptor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPessoa()
    {
        return $this->hasOne(Pessoa::className(), ['id' => 'pessoa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioAmigos()
    {
        return $this->hasMany(UsuarioAmigo::className(), ['usuario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioAmigos0()
    {
        return $this->hasMany(UsuarioAmigo::className(), ['amigo_id' => 'id']);
    }
}
