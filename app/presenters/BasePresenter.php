<?php

use Nette\Application\UI\Form;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	protected
		/** @var Elastica\Client */
		$elastica,

		/** @var \Guzzle\Http\Client */
		$httpClient,

		/** @var EB\Servers\Servers */
		$servers,

		$server
	;

	protected function startup()
	{
		parent::startup();
		$this->server = (object)$this->servers->find($this->getSession('server')->server);
	}

	public function injectServers(EB\Servers\Servers $servers)
	{
		$this->servers = $servers;
	}

	public function injectElastica(Elastica\Client $elastica)
	{
		$this->elastica = $elastica;
	}

	public function injectHttpClient(\Guzzle\Http\Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	protected function flash($message, $type = 'info')
	{
		parent::flashMessage($message, $type);
		$this->invalidateControl('flashes');
	}

	protected function createComponentFormSelectServer()
	{
		$servers = $this->servers->findAll();

		$values = [];
		foreach($servers as $label => $server)
		{
			$values[$label] = $label;
		}

		$form = new Form;

		$form->addSelect('server', 'Server', $values);
		$form->addSubmit('btnSubmit', 'Select');

		$form->onSuccess[] = $this->formSelectServerSubmitted;

		return $form;
	}

	public function formSelectServerSubmitted(Form $form)
	{
		$values = $form->getValues();

		$server = $values->server;

		$this->getSession('server')->server = $server;

		$this->redirect('this');
	}

	protected function beforeRender()
	{
		parent::beforeRender();

		$this->template->currentServer = $this->server;

		$this->template->health = $this->httpClient->get($this->server->hostname.'/_cluster/health')->send()->json();
	}
}
