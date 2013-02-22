<?php

/**
 * Base presenter for all application presenters.
 */
abstract class SecuredPresenter extends \BasePresenter
{
	protected function startup()
	{
		parent::startup();
		if(!$this->user->isLoggedIn())
		{
			$this->redirect(':sign:in');
		}
	}
}
