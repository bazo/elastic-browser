<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

}
