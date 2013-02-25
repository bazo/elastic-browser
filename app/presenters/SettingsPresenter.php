<?php

use Nette\Application\UI\Form;
use EB\Servers\Servers;
/**
 * Homepage presenter.
 */
class SettingsPresenter extends SecuredPresenter
{
	public function renderDefault()
	{
	}

	protected function createComponentFormAddServer()
	{
		$form = new Form;

		$form->addText('label', 'Label');
		$form->addText('hostname', 'Hostname')->setRequired('Please fill in %label');
		$form->addText('port', 'Port')->addRule(Form::INTEGER, 'Please enter correct port');

		$form->addSubmit('btnSubmit', 'Add server');

		$form->onSuccess[] = $this->formAddServerSubmitted;

		return $form;
	}

	public function formAddServerSubmitted(Form $form)
	{
		$values = $form->getValues();

		$port = $values->port !== '' ? $values->port : 9200;

		$uri = sprintf('%s:%s', $values->hostname, $port);

		try
		{
			$info = (object)$this->httpClient->get($uri)->send()->json();

			if($info->status === 200)
			{
				$name = $info->name;
				$version = $info->version['number'];
				$label = $values->label;

				$this->servers->create($uri, $label, $name, $version);
				$message = sprintf('Server %s, version %s on %s added as %s', $name, $version, $uri, $label);
				$this->flash($message, $type = 'success');
			}
		}
		catch (\Guzzle\Http\Exception\CurlException $e)
		{
			//$form->addError($e->getMessage());
			$this->flash($e->getMessage(), 'error');
		}
		$this->redirect('this');
	}

	public function renderServers()
	{
		$this->template->servers = $this->servers->findAll();
	}

}
