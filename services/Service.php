<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\services;


/**
 *
 * 
 * 
 *
 * @package yii\beutils\services
 */
abstract class Service extends Configuration
{
	abstract public function apply();

	final public function run(){
		return $this->apply();
	}

	final public function input(){
	}

	final public function output(){
	}

	private function resolveInput(){
	}

	private function resolveOutput(){
	}
	
	private function build($provider, $type, $branch){
	}
}