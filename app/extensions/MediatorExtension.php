<?php

namespace Extensions;

use Nette\Framework;

/**
 * Console service.
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class MediatorExtension extends \Nette\Config\CompilerExtension {

    public function afterCompile(\Nette\PhpGenerator\ClassType $class) {

        $container = $this->getContainerBuilder();
        $initialize = $class->methods['initialize'];

        $mediators = $container->findByTag('mediator');
        $subscribers = $container->findByTag('subscriber');

        foreach ($mediators as $mediatorName => $mediator) {

            foreach ($subscribers as $subscriberName => $subscriber) {
                $initialize->addBody('$this->getService(?)->addSubscriber($this->getService(?));', [$mediatorName, $subscriberName]);
            }
        }
    }
}

