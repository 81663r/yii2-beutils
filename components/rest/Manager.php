<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;

use yii\db\Exception as DbException;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\web\UnauthorizedHttpException;

/**
 * This class will handle all api administrative operations.
 *
 * @package yii\beutils\components\rest
 */
class Manager
{
    /**
     * Shared secret string block length
     */
    const SHARED_SECRET_BLOCK_LEN = 1024;

    /**
     * Hashing algorithm
     */
    const HASH_ALGO = 'sha256';

    const STATUS_ENABLED = 'enabled';

    const STATUS_DISABLED = 'disabled';

    /**
     * False entropy used to generate random string. This is not required to be very secure.
     */
    private $falseEntropy = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+;:<>.";


    /**
     * Authenticate api request.
     * This method will authenticate api request using
     * the api domain, username and password
     *
     * @param $domain The domain for the api user
     * @param $username The api username assigned to the user
     * @param $password The api password assigned to the user
     *
     * @return boolean True in case of successfull authorization
     * @throws \HttpException in case of unauthorized
     */
    public function authenticateApiRequest(string $domain, string $username, string $password){

        // Hash the password
        $hashedPassword = hash(self::HASH_ALGO, $password);

        $query = new Query();
        $query->select('id')
            ->from('api_user')
            ->where('domain=:d AND username=:u AND password=:p',[':d' => $domain, ':u' => $username, ':p' => $hashedPassword]);

        if (!($query->createCommand())->queryOne())
            \Yii::$app->rest->UNAUTHORIZED('unable to authenticate api request. incorrect username or password.');

        return true;
    }


    /**
     * Authorize api request.
     *
     * This method will authorize the api request by fetching
     * the api secret key assigned to the user and hasing the body
     * of the request.
     *
     * @param $request The api request
     *
     * @throws UnauthorizedHttpException in case authentication fails
     */
    public function authorizeApiRequest(Request $request){

        // Create request timeout window
        $diff = (time() - $request->timestamp);

        // Make sure request is within timeout window
        if (($diff > Rest::REQUEST_VALID_TIMEOUT) || ($diff < 0))
            \Yii::$app->rest->UNAUTHORIZED('unable to authorize api request. request has expired.');

        // Get shared key from db
        $sql = "
            SELECT 
                B.passkey
            FROM
                api_key B
            JOIN
                api_user A ON A.id = B.api_user_id
            JOIN
                api C ON C.id = B.api_id
            WHERE
                A.username=:u AND 
                B.stability=:s AND 
                C.name=:n
        ";

        if (!($sharedSecret = (\Yii::$app->db->createCommand($sql, [':u' => $request->username, ':s' => $request->stability, ':n' => $request->api])->queryOne()))){
            \Yii::$app->rest->UNAUTHORIZED('unable to authorize api request. api user not found.');
        }

        // Generate signature with shared secret
        $signature = hash_hmac(self::HASH_ALGO, $request->rawData, $sharedSecret['passkey']);

        // Compare signatures
        if (!($signature == $request->signature)){
            \Yii::$app->rest->UNAUTHORIZED('unable to authorize api request. signatures do not match.');
        }
    }


    /**
     * Generates a pseudo random string to be used with hashing methods.
     *
     * @param $size The length of the string to generate
     * @param $base64 Specifies if the generated text will be encoded into base64
     */
    public function generateStr(int $size, bool $base64 = false){

        $data = "";

        for($lp = 0; $lp < $size; $lp++){
            $data .= $this->falseEntropy[(rand(0, (strlen($this->falseEntropy) -1)))];
        }

        return ($base64 == true ? base64_encode($data) : $data);
    }


    /**
     * Create api shared secret.
     *
     * @param $userId The user id to use as part of the signature
     * @param $apiId The api id to use as part of the signature
     * @param $stability The api stability to use as part of the signature (dev | prod | test)
     */
    public function createApiSharedSecret(int $userId, int $apiId, string $stability){

        // Generate string
        $block = $this->generateStr(self::SHARED_SECRET_BLOCK_LEN);

        // Add distinctive text
        $block .= $userId.$apiId.$stability.@date('Y-m-d H:i:s').microtime();

        // Hash block of text
        return hash(self::HASH_ALGO, $block);
    }


    /**
     * Create new api.
     *
     * @param $name The name of the api
     * @param $domain The domain (realm) of the api
     * @param $description A short description of that the API does.
     */
    public function createApi(string $name, string $domain, string $description){
        try{
            if ((\Yii::$app->db->createCommand()->insert('api',[
                'name' => $name,
                'domain' => $domain,
                'description' => $description,
                'status' => self::STATUS_ENABLED,
                'creation_time' => @date('H:i:s'),
                'creation_date' => @date('Y-m-d'),
                'timestamp' => @date('Y-m-d H:i:s')
            ]))->execute() == 0){
                throw new DbException('unable to add api');
            }
        }
        catch(IntegrityException $e){
            // @TODO: Log exception

            throw $e;
        }
        catch(DbException $e){
            // @TODO: Log exception

            throw $e;
        }
    }


    /**
     * Add API endpoint to existing API.
     *
     * @param $apiId The api id for which the endpoint will be added
     * @param $name The name of the endpoint
     * @param $description A short description
     */
    public function addApiEndpoint($apiId, $name, $description){
        try{
            if ((\Yii::$app->db->createCommand()->insert('api_endpoint',[
                'api_id' => $apiId,
                'name' => $name,
                'description' => $description,
                'creation_time' => @date('H:i:s'),
                'creation_date' => @date('Y-m-d'),
                'timestamp' => @date('Y-m-d H:i:s')
            ]))->execute() == 0){
                throw new DbException('unable to add api endpoint');
            }
        }
        catch(IntegrityException $e){
            // @TODO: Log exception

            throw $e;
        }
        catch(DbException $e){
            // @TODO: Log exception

            throw $e;
        }
    }


    /**
     * Create api user.
     *
     * @param $domain The domain of the API
     * @param $username The username, usually an email address
     * @param $password The password for the api user
     */
    public function createApiUser($domain, $username, $password){
        try{
            if ((\Yii::$app->db->createCommand()->insert('api_user',[
                    'domain' => $domain,
                    'username' => $username,
                    'password' => hash(self::HASH_ALGO, $password),
                    'status' => self::STATUS_ENABLED,
                    'creation_time' => @date('H:i:s'),
                    'creation_date' => @date('Y-m-d'),
                    'timestamp' => @date('Y-m-d H:i:s')
                ]))->execute() == 0){
                throw new DbException('unable to save new user');
            }
        }
        catch(IntegrityException $e){
            // @TODO: Log exception

            throw $e;
        }
        catch(DbException $e){
            // @TODO: Log exception

            throw $e;
        }
    }


    /**
     * Add user to api.
     *
     * @param $userId The user id to add to the api
     * @param $apiId The api id where the user will be added
     * @param $stability Determines if the stability of the api (dev | prod | test)
     */
    public function addApiUser($userId, $apiId, $stability){

        // Create api shared secret
        $apiSharedSecret = $this->createApiSharedSecret($userId, $apiId, $stability);

        try{
            if ((\Yii::$app->db->createCommand()->insert('api_key', [
                'api_id' => $apiId,
                'api_user_id' => $userId,
                'passkey' => $apiSharedSecret,
                'stability' => $stability,
                'status' => self::STATUS_ENABLED,
                'creation_time' => @date('H:i:s'),
                'creation_date' => @date('Y-m-d'),
                'timestamp' => @date('Y-m-d H:i:s')
            ]))->execute() == 0){
                throw new DbException('unable to add user to api');
            }
        }
        catch(IntegrityException $e){
            // @TODO: Log exception

            throw $e;
        }
        catch(DbException $e){
            // @TODO: Log exception

            throw $e;
        }

        return $apiSharedSecret;
    }
}
