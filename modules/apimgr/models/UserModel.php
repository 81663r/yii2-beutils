<?php
namespace yii\beutils\modules\apimgr\models;

use yii\base\Model;

class UserModel extends Model{

    public $email;
    public $password;
    public $domain;

    public function rules()
    {
        return [
            [['email', 'password', 'domain'], 'required'],
        ];
    }
}