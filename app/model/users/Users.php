<?php

namespace EB\Users;

use Nette\Config\Adapters\NeonAdapter;
use Nette\Security;

/**
 * Users
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Users implements Security\IAuthenticator
{
	private
		/** @var Phpass\Hash */
		$hasher,

		/** @var NeonAdapter */
		$neon,

		$configFile
	;


	function __construct($configFile, \Phpass\Hash $hasher)
	{
		$this->configFile = $configFile;
		$this->hasher = $hasher;
		$this->neon = new NeonAdapter;
	}

	public function create($login, $password)
	{
		$hash = $this->hasher->hashPassword($password);

		$settings = $this->neon->load($this->configFile);
		$settings['users'][ $login] = $hash;

		$handle = fopen($this->configFile, 'w');
		fwrite($handle, $this->neon->dump($settings));
		fclose($handle);

	}

	public function find($login)
	{
		$settings = $this->neon->load($this->configFile);

		if(isset($settings['users'][$login]))
		{
			return ['login' => $login, 'password' => $settings['users'][$login]];
		}

		return null;
	}

	public function authenticate(array $credentials)
	{
		list($login, $password) = $credentials;
		$user = $this->find($login);

		if($user === null or !$this->hasher->checkPassword($password, $user['password']))
		{
			throw new Security\AuthenticationException('Please enter correct credentials.', self::INVALID_CREDENTIAL);
		}

		unset($user['password']);
		return new Security\Identity($user['login'], 'admin', $user);
	}

}

