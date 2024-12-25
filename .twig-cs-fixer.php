<?php

$ruleset = new TwigCsFixer\Ruleset\Ruleset();
$ruleset->addStandard(new TwigCsFixer\Standard\Symfony());

$config = new TwigCsFixer\Config\Config();
$config->setCacheFile(__DIR__.'/var/twig-cs-fixer');
$config->setRuleset($ruleset);

return $config;
