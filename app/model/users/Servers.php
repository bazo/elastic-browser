<?php

namespace EB\Servers;

use Nette\Config\Adapters\NeonAdapter;
use Nette\Security;

/**
 * Users
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Servers
{
	private
		/** @var NeonAdapter */
		$neon,

		$configFile
	;


	function __construct($configFile)
	{
		$this->configFile = $configFile;
		$this->neon = new NeonAdapter;
	}

	public function create($hostname, $label, $name, $version)
	{

		$settings = $this->neon->load($this->configFile);
		$settings['servers'][ $label] = [
			'hostname' => $hostname,
			'name' => $name,
			'version' => $version
		];

		$handle = fopen($this->configFile, 'w');
		fwrite($handle, $this->neon->dump($settings));
		fclose($handle);

	}

	public function find($label)
	{
		$settings = $this->neon->load($this->configFile);

		if(isset($settings['servers'][$label]))
		{
			return $settings['servers'][$label];
		}

		return null;
	}

	public function findAll()
	{
		$settings = $this->neon->load($this->configFile);

		return $settings['servers'];
	}
}

