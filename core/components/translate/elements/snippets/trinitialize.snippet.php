<?php
/**
 * Initializes the translator engine thingy
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$corePath = $modx->getOption('translate.core_path',null,$modx->getOption('core_path').'components/translate/');
$Translate = $modx->getService('translate', 'Translate', $corePath . 'model/translate/');
if (!($Translate instanceof Translate)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to load Translate service from ' . $corePath);
    return 'Error loading Translate';
}

$namespace = $modx->getOption('namespace', $_GET);
$topic = $modx->getOption('topic', $_GET);
$language = $modx->getOption('language', $_GET);

if (empty($namespace) || empty($topic) || empty($language)) {
    $modx->sendRedirect($modx->makeUrl($modx->resource->get('parent')));
}

$namespaceObj = $modx->getObject('trNamespace', array('name' => $namespace));
$topicObj = $modx->getObject('trTopic', array('topic' => $topic));
$languageObj = $modx->getObject('trLanguage', array('code' => $language));

if (!$namespaceObj || !$topicObj || !$languageObj) {
    $modx->sendRedirect($modx->makeUrl($modx->resource->get('parent')));
}
$phs = array_merge(
    $namespaceObj->toArray('namespace.'),
    $topicObj->toArray('topic.'),
    $languageObj->toArray('language.')
);

//var_dump($phs);
$modx->setPlaceholders($phs);