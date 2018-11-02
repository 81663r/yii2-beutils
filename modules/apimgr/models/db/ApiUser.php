<?php
namespace yii\beutils\modules\apimgr\models\db;

use Yii;

/**
 * This is the model class for table "api_user".
 *
 * @property int $id
 * @property string $domain
 * @property string $username
 * @property string $password
 * @property string $status
 * @property string $creation_time
 * @property string $creation_date
 * @property string $timestamp
 *
 * @property ApiKey[] $apiKeys
 */
class ApiUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain', 'username', 'password', 'creation_time', 'creation_date'], 'required'],
            [['status'], 'string'],
            [['creation_time', 'creation_date', 'timestamp'], 'safe'],
            [['domain', 'username'], 'string', 'max' => 350],
            [['password'], 'string', 'max' => 64],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => 'Domain',
            'username' => 'Username',
            'password' => 'Password',
            'status' => 'Status',
            'creation_time' => 'Creation Time',
            'creation_date' => 'Creation Date',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApiKeys()
    {
        return $this->hasMany(ApiKey::className(), ['api_user_id' => 'id']);
    }
}
