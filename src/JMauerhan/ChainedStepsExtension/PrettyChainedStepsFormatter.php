<?php

namespace JMauerhan\ChainedStepsExtension;

use Behat\Behat\Formatter\PrettyFormatter;
use Behat\Behat\Event\StepEvent;
use Behat\Gherkin\Node\StepNode;

class PrettyChainedStepsFormatter extends PrettyFormatter
{

    var $chainedSteps = [];
    var $inChain = false;
    var $indent = '  ';

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

        if ((!$this->inBackground && $this->inOutlineExample))
        {
            $this->delayedStepEvents[] = $event;
            return;
        }

        if ($this->inChain)
        {
            $this->chainedSteps[] = $event;
            return;
        }

        $this->printStep(
                $event->getStep(), $event->getResult(), $event->getDefinition(), $event->getSnippet(), $event->getException()
        );

        if (isset($this->chainedSteps) && count($this->chainedSteps) && isset($isStepChainParent) && $isStepChainParent)
        {
            foreach ($this->chainedSteps as $event)
            {
                $this->write($this->indent);
                $this->printStep(
                        $event->getStep(), $event->getResult(), $event->getDefinition(), $event->getSnippet(), $event->getException()
                );
            }
            $this->chainedSteps = [];
        }
    }

}

