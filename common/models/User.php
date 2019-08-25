<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * User model
 * @property string $password_reset_token
 *
 * @property integer $person_fk
 * @property integer $character_fk
 * @property integer $gitlab_user_fk
 * @property boolean $active
 * @property integer $id
 * @property string $username
 * @property integer $ufs_enrollment
 * @property string $password_hash
 * @property string $auth_key
 * @property string $glab_private_token
 * @property Character $characterFk
 * @property Person $personFk
 */


class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = "TRUE";
    private $person = null;
    private $character = null;

    /**
      constantes que representam os characters
     */
    const ROLE_ADMIN = "admin";
    const ROLE_TEACHER = "teacher";
    const ROLE_STUDENT = "student";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
     /*
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_fk', 'character_fk', 'gitlab_user_fk', 'username', 'ufs_enrollment', 'password_hash'], 'required',
              'message'=>'Please enter a value for {attribute}.'],
            [['person_fk', 'character_fk', 'gitlab_user_fk', 'ufs_enrollment'], 'integer'],
            [['active'], 'boolean'],
            [['username'], 'string', 'max' => 50],
            [['password_hash', 'auth_key'], 'string', 'max' => 255],
            [['character_fk'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\Character::className(), 'targetAttribute' => ['character_fk' => 'id']],
            [['person_fk'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\Person::className(), 'targetAttribute' => ['person_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'active' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'active' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'active' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterFk()
    {
        return $this->hasOne(\frontend\models\Character::className(), ['id' => 'character_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonFk()
    {
        return $this->hasOne(\frontend\models\Person::className(), ['id' => 'person_fk']);
    }

    /**
     * @return \frontend\models\Person
     */
    public function getPerson(){
      if(!ISSET($this->person)){
          $this->person =  \frontend\models\Person::findOne($this->person_fk);
      }
      return $this->person;
    }

    /**
     * @return \frontend\models\Character
     */
    public function getCharacter(){
      if(!ISSET($this->character)){
          $this->character =  \frontend\models\Character::findOne($this->character_fk);
      }
      return $this->character;
    }

}
