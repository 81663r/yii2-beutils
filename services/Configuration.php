<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\services;


/**
 * This class handles configuration functionality for services.
 *
 * 
 * 
 *
 * @package yii\beutils\services
 */
abstract class Configuration 
{
	/**
	 * Service type
	 */
	private $type = null;

	/**
	 * Service type branch
	 */
	private $branch = null;

	/**
	 * Provider
	 */
	private $provider = null;
	

	final protected function getConf($provider, $type, $branch){

		// Get configuration from db
		$sql = "
			SELECT
				A.id,
				A.stability,
				A.type,
				A.status,
				A.key,
				A.value,
				A.datetime
			FROM
				service_conf A
			LEFT JOIN 
				service_branch B ON B.id = A.branch_id
			LEFT JOIN
				service_provider C ON C.id = A.provider_id
			LEFT JOIN
				service_type D ON D.id = B.type_id
			WHERE
				B.name = :bname AND
				C.name = :pname AND
				D.name = :tname AND
				A.stability = :stability AND
				A.status = 'enabled'
		";

        if (!($conf = (\Yii::$app->db->createCommand($sql, [':bname' => $branch, ':pname' => $provider, ':tname' => $type, ':stability' => strtolower(YII_ENV)])->queryAll())))
			return null;
		
		return $conf;
	}
}
