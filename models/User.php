<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends Account implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        // $id di sini adalah primary key (biasanya id)
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        // PERBAIKAN: Cari berdasarkan kolom username, bukan primary key langsung
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        // PERBAIKAN: Kembalikan primary key (biasanya $this->id), bukan username
        return $this->username; 
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        // Gunakan security component untuk memverifikasi hash Bcrypt
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}