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

$output = array();
/** @var trNamespace $namespace */
foreach ($modx->getIterator('trNamespace') as $namespace) {
    $nsPhs = $namespace->toArray();
    // Get language sets
    $c = $modx->newQuery('trLanguageSet');
    $c->where(array(
        'namespace' => $namespace->get('id')
    ));
    $c->innerJoin('trTopic', 'Topic');
    $c->innerJoin('trLanguage', 'Language');

    $c->select($modx->getSelectColumns('trLanguageSet', 'trLanguageSet'));
    $c->select($modx->getSelectColumns('trTopic', 'Topic', 'topic_'));
    $c->select($modx->getSelectColumns('trLanguage', 'Language', 'language_'));

    $c->sortby('Topic.topic', 'ASC');
    $c->sortby('Language.name', 'ASC');

    $tmp = array();
    /** @var trLanguageSet $langSet */
    foreach ($modx->getIterator('trLanguageSet', $c) as $langSet) {
        $phs = $langSet->toArray('', false, true);

        $total = $modx->getCount('trEntry', array('languageset' => $phs['id']));
        $translated = $modx->getCount('trEntry', array('languageset' => $phs['id'], 'translated' => true));
        $phs['percentage_complete'] = round($translated / $total * 100,0);
        
        $tmp[] = $Translate->getTpl('langsets/set', $phs);
    }
    $nsPhs['sets'] = implode('', $tmp);

    $output[] = $Translate->getTpl('langsets/namespace', $nsPhs);
}
return implode('', $output);
