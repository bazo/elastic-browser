<?php

/**
 * Homepage presenter.
 */
class OverviewPresenter extends SecuredPresenter
{
	public function renderDefault()
	{

		$nodes = array_reverse($this->httpClient->get($this->server->hostname.'/_cluster/nodes')->send()->json()['nodes']);
		$state = $this->httpClient->get($this->server->hostname.'/_cluster/state')->send()->json();
		$cluster = $this->httpClient->get($this->server->hostname.'/_cluster/nodes/stats?all=true')->send()->json();

		$this->template->state = $state;
		$this->template->nodes = $nodes;
		$this->template->cluster = $cluster;
	}

}
