<?php

/**
 * Homepage presenter.
 */
class BrowserPresenter extends SecuredPresenter
{
	public
		/** @persistent */
		$indices = [],

		/** @persistent */
		$types = []
	;

	public function handleAddIndex($index)
	{
		if(!in_array($index, $this->indices))
		{
			$this->indices[] = $index;
		}
		$this->redirect('this');
	}

	public function handleRemoveIndex($index)
	{
		if(in_array($index, $this->indices))
		{
			$key = array_search($index, $this->indices);
			unset($this->indices[$key]);
		}
		$this->redirect('this');
	}

	public function renderDefault()
	{
		$state = $this->httpClient->get($this->server->hostname.'/_cluster/state')->send()->json();

		$indices = $state['metadata']['indices'];
		$types = [];

		foreach($indices as $index => $data)
		{
			foreach($data['mappings'] as $type => $mapping)
			{
				$types[$type] = $type;
			}
		}

		$this->template->indices = $indices;
		$this->template->types = $types;

		$uri = $this->server->hostname;

		if(!empty($this->indices))
		{
			$uri .= '/'.implode(',', $this->indices);
		}

		if(!empty($this->types))
		{
			$uri .= '/'.implode(',', $this->types);
		}

		$uri .= '/_search';
		dump($uri);

		$this->template->results = $this->httpClient->post($uri, $headers = null, $postBody = null)->send()->json();
		$this->template->selectedIndices = $this->indices;
	}

}
