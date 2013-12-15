<?php

/**
 * Class TranslateRestGettranslationsController
 */
class TranslateRestGettranslationsController extends TranslateRestController {
    /**
     * @return string
     */
    public function process() {
        $languageSet = $this->modx->getObject('trLanguageSet', array(
            'namespace' => $this->namespace->get('id'),
            'topic' => $this->topic->get('id'),
            'language' => $this->language->get('id'),
        ));

        /** @var trLanguageSet $languageSet */
        if (!$languageSet) {
            $languageSet = $this->modx->newObject('trLanguageSet');
            $languageSet->fromArray(array(
                'namespace' => $this->namespace->get('id'),
                'topic' => $this->topic->get('id'),
                'language' => $this->language->get('id'),
            ));
            if (!$languageSet->save() || !$languageSet->generate()) {
                return $this->modx->toJSON(array('error' => true, 'message' => 'This package or topic has not yet been translated to that language, and generating the language set failed. :('));
            }
        }


        $output = array();

        $c = $this->modx->newQuery('trEntry');
        $c->where(array(
            'languageset' => $languageSet->get('id')
        ));

        $sourceValues = $languageSet->getSourceValues();
        /** @var trEntry $entry */
        foreach ($this->modx->getIterator('trEntry', $c) as $entry) {
            $a = $entry->toArray();
            $a['original'] = (isset($sourceValues[$a['key']])) ? $sourceValues[$a['key']] : $a['key'];
            $output[] = $a;
        }

        return $this->modx->toJSON($output);
    }
}
