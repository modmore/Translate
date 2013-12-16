<?php

/**
 * Class TranslateRestUpdateController
 */
class TranslateRestUpdateController extends TranslateRestController {
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
            return $this->modx->toJSON(array('error' => true, 'message' => 'Unknown language set'));
        }
        if ($languageSet->get('id') != $this->getRequest('languageset')) {
            return $this->modx->toJSON(array('error' => true, 'message' => 'Invalid language set'));
        }

        $entry = $this->modx->getObject('trEntry', array(
            'languageset' => $languageSet->get('id'),
            'id' => $this->getRequest('id'),
            'key' => $this->getRequest('key'),
        ));
        if ($entry instanceof trEntry) {
            $translation = $this->getRequest('translation');
            if (!empty($translation)) {
                $entryArray = $entry->toArray();

                $entry->set('translation', $translation);
                $entry->set('translated', true);
                $entry->set('translatedon', time());
                $entry->set('translatedby', $this->user->get('id'));

                // Add points to the user
                if (empty($entryArray['translation']) || $entryArray['translatedby'] != $this->user->get('id')) {
                    $entry->addUserPoints($this->user->get('id'));
                }
            }


            $flagged = $this->getRequest('flagged', false, false);
            if ($flagged == 'true') {
                $entry->set('flagged', true);
                $entry->set('flaggedby', $this->user->get('id'));
                $entry->set('flaggedon', time());
            }
            else {
                $entry->set('flagged', false);
                $entry->set('flaggedby', 0);
                $entry->set('flaggedon', 0);
            }


            $skipped = $this->getRequest('skipped', false, false);
            if ($skipped == 'true') {
                $entry->set('skipped', true);
                $entry->set('skippedby', $this->user->get('id'));
                $entry->set('skippedon', time());
            }
            else {
                $entry->set('skipped', false);
                $entry->set('skippedby', 0);
                $entry->set('skippedon', 0);
            }


            if ($entry->save()) {
                $a = $entry->toArray();
                $a['points'] = $this->translate->getPointsTotal(array('user' => $this->user->get('id')));
                return $this->modx->toJSON($a);
            }
            return $this->modx->toJSON(array(
                'error' => true,
                'message' => 'Error saving entry'
            ));
        }

        return $this->modx->toJSON(array(
            'error' => true,
            'message' => 'Invalid entry'
        ));
    }
}
