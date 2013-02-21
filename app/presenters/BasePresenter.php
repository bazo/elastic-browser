<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected function flash($message, $type = 'info')
	{
		parent::flashMessage($message, $type);
		$this->invalidateControl('flashes');
	}
}
