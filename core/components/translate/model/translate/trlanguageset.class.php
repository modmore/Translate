<?php
/**
 * Translate
 *
 * Copyright 2013 by Mark Hamstra <hello@markhamstra.com>
 *
 * This file is part of Translate
 *
 * Translate is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Translate is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Translate; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package translate
 * @sub-package model
 */
class trLanguageSet extends xPDOSimpleObject
{
    /**
     * @return bool
     */
    public function generate()
    {
        $sourceValues = $this->getSourceValues();
        if (!$sourceValues) return false;

        $existingValues = $this->getExistingValues();

        $entries = array();
        foreach ($sourceValues as $key => $original) {
            /** @var trEntry $o */
            $o = $this->xpdo->getObject('trEntry', array(
                'languageset' => $this->get('id'),
                'key' => $key,
            ));

            if (!$o) {
                $o = $this->xpdo->newObject('trEntry');
            }

            $o->fromArray(array(
                'languageset' => $this->get('id'),
                'key' => $key,
            ));

            if ($o->get('translation') == '' && isset($existingValues[$key])) {
                $o->set('translation', $existingValues[$key]);
                $o->set('translated', true);
                $o->set('translatedon', time());
                $o->set('translatedby', 0);
            }
            $entries[] = $o;
        }
        $this->addMany($entries, 'Entries');

        if ($this->save()) return true;
        return false;
    }

    /**
     * @return array|bool
     */
    public function getSourceValues()
    {
        $ns = $this->getOne('Namespace');
        $topic = $this->getOne('Topic');

        $source = $ns->get('source_path') . 'en/' . $topic->get('topic') . '.inc.php';
        if (!file_exists($source)) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Source for language set does not exist: ' . $source);
            return false;
        }

        $_lang = array();
        try {
            include $source;
        }
        catch (Exception $e) {}

        if (!empty($_lang)) {
            return $_lang;
        }
        return false;
    }

    /**
     * @return array
     */
    private function getExistingValues()
    {
        $ns = $this->getOne('Namespace');
        $topic = $this->getOne('Topic');
        $language = $this->getOne('Language');

        $source = $ns->get('source_path') . $language->get('code') . '/' . $topic->get('topic') . '.inc.php';
        if (!file_exists($source)) {
            return array();
        }

        $_lang = array();
        try {
            include $source;
        }
        catch (Exception $e) {}

        if (!empty($_lang)) {
            return $_lang;
        }
        return array();
    }
}
