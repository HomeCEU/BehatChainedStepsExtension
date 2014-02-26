BehatChainedStepsExtension
==========================

This Behat Extension does two things:

* Trigger BeforeStep and AfterStep hooks when using Chained Steps
* Display pass/fail output in console for Chained Steps

# Installation
Install using composer:
```
"jmauerhan/chained-steps-extension": "dev-master@dev"
```

# Configuration
Configure the Extension in behat.yml
```
default:
  extensions:
    JMauerhan\ChainedStepsExtension\Extension:
      trigger_hooks: true
      show_chained_steps: true
  formatter:
    name: 'JMauerhan\ChainedStepsExtension\PrettyChainedStepsFormatter'
```



## Options/Defaults
```
trigger_hooks: true
show_chained_steps: true (If trigger_hooks is false, show_chained_steps must be false).
tester:
  step:
    class: JMauerhan\ChainedStepsExtension\StepTester
```
