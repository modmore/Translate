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
*/

class Translate {
    /**
     * @var modX|null $modx
     */
    public $modx = null;
    /**
     * @var array
     */
    public $config = array();
    /**
     * @var array
     */
    public $templates = array();
    /**
     * @var bool
     */
    public $debug = false;


    /**
     * @param \modX $modx
     * @param array $config
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('translate.core_path',$config,$this->modx->getOption('core_path').'components/translate/');
        $assetsUrl = $this->modx->getOption('translate.assets_url',$config,$this->modx->getOption('assets_url').'components/translate/');
        $assetsPath = $this->modx->getOption('translate.assets_path',$config,$this->modx->getOption('assets_path').'components/translate/');
        $this->config = array_merge(array(
            'basePath' => $corePath,
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'elementsPath' => $corePath.'elements/',
            'templatesPath' => $corePath.'templates/',
            'assetsPath' => $assetsPath,
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
        ),$config);

        $modelPath = $this->config['modelPath'];
        $this->modx->addPackage('translate',$modelPath);
        $this->modx->lexicon->load('translate:default');
        
        $this->debug = (bool)$this->modx->getOption('translate.debug',null,false);
    }



    /**
    * Gets a Chunk and caches it; also falls back to file-based templates
    * for easier debugging.
    *
    * @access public
    * @param string $name The name of the Chunk
    * @param array $properties The properties for the Chunk
    * @return string The processed content of the Chunk
    * @author Shaun "splittingred" McCormick
    */
    public function getTpl($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->templates[$name])) {
            $chunk = $this->_getTplChunk($name);
            if (empty($chunk)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
                if ($chunk == false) return false;
            }
            $this->templates[$name] = $chunk->getContent();
        } else {
            $o = $this->templates[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
    * Returns a modChunk object from a template file.
    *
    * @access private
    * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
    * @param string $postFix The postfix to append to the name
    * @return modChunk/boolean Returns the modChunk object if found, otherwise false.
    * @author Shaun "splittingred" McCormick
    */
    private function _getTplChunk($name,$postFix = '.tpl') {
        $chunk = false;
        $f = $this->config['templatesPath'].strtolower($name).$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /* @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     * Sends a message to HipChat.
     *
     * @param string $room
     * @param string $message
     * @param array $options
     */
    public function pingHipChat($room, $message, array $options = array()) {
        $parameters = array_merge(array(
            'room_id' => $room,
            'from' => 'modmore bot',
            'message' => $message,
            'notify' => 0,
            'color' => 'blue',
        ), $options);

        $token = $this->modx->getOption('modmore.hipchat_token');

        $url = "https://api.hipchat.com/v1/rooms/message?auth_token=$token&format=json";
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($c);
        $resultDecoded = $this->modx->fromJSON($result);
        if (!is_array($resultDecoded) || ($resultDecoded['status'] != 'sent')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error sending message to hipchat ' . $result . ' | Message: ' . print_r($parameters, true));
        }
    }

}

