<?php

namespace JMauerhan\ChainedStepsExtension;

use Behat\Behat\Formatter\PrettyFormatter;
use Behat\Behat\Event\StepEvent;
use Behat\Gherkin\Node\StepNode;

class PrettyChainedStepsFormatter extends PrettyFormatter
{

    var $chainedSteps = [];
    var $inChain = false;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        $events = array(
            'beforeSuite', 'afterSuite', 'beforeFeature', 'afterFeature', 'beforeScenario',
            'afterScenario', 'beforeBackground', 'afterBackground', 'beforeOutline', 'afterOutline',
            'beforeOutlineExample', 'afterOutlineExample', 'beforeStep', 'afterStep'
        );

        return array_combine($events, $events);
    }

    public function isStepChainParent(StepNode $step)
    {
        $stepHash = spl_object_hash($step);
        foreach ($step->getParent()->getSteps() AS $s)
        {
            if (spl_object_hash($s) == $stepHash)
            {
                return true;
            }
        }
    }

    /**
     * Listens to "step.before" event.
     *
     * @param StepEvent $event
     *
     * @uses printStep()
     */
    public function beforeStep(StepEvent $event)
    {
        if ($this->isStepChainParent($event->getStep()))
        {
            $this->inChain = true;
        }
    }

    /**
     * Listens to "step.after" event.
     *
     * @param StepEvent $event
     *
     * @uses printStep()
     */
    public function afterStep(StepEvent $event)
    {
        if ($this->inBackground && $this->isBackgroundPrinted)
        {
            return;
        }

        if ($this->isStepChainParent($event->getStep()))
        {
            $isStepChainParent = true;
            $this->inChain = false;
        }

        if ((!$this->inBackground && $this->inOutlineExample) || $this->inChain)
        {
            $this->delayedStepEvents[] = $event;

            return;
        }

        $this->printStep(
                $event->getStep(), $event->getResult(), $event->getDefinition(), $event->getSnippet(), $event->getException()
        );

        if (isset($this->delayedStepEvents) && count($this->delayedStepEvents) && isset($isStepChainParent) && $isStepChainParent)
        {
            foreach ($this->delayedStepEvents as $event)
            {
                $this->printStep(
                        $event->getStep(), $event->getResult(), $event->getDefinition(), $event->getSnippet(), $event->getException()
                );
            }
            unset($this->delayedStepEvents);
        }
    }

}

