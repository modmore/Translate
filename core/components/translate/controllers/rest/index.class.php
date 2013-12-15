<?php
/**
 *
 */
class TranslateRestController {
    /**
     * @var modX
     */
    public $modx;
    /**
     * @var array
     */
    public $config;
    /**
     * @var
     */
    public $request;
    /**
     * @var Translate
     */
    public $translate;

    /** @var trNamespace */
    public $namespace;
    /** @var trTopic */
    public $topic;
    /** @var trLanguage */
    public $language;

    /** @var null|modUser */
    public $user = null;

    /**
     * Constructs a TranslateRestController object.
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;
        $this->translate =& $this->modx->translate;
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    public function initialize() {

    }

    /**
     * Return the default config here.
     * @return array
     */
    public function getDefaultConfig() {
        return array();
    }

    /**
     * @param $key
     * @param null $default
     * @param bool $ignoreEmpty
     *
     * @return null
     */
    public function getOption($key, $default = null, $ignoreEmpty = false) {
        if (!isset($this->config[$key]) || (empty($this->config[$key]) && $ignoreEmpty)) {
            return $default;
        }
        return $this->config[$key];
    }

    /**
     * Gets a request option
     *
     * @param $key
     * @param null $default
     * @param bool $checkEmpty
     *
     * @return null
     */
    public function getRequest($key, $default = null, $checkEmpty = true) {
        $value = $default;
        if (isset($_REQUEST[$key])) {
            if (
                ($checkEmpty && !empty($_REQUEST[$key]))
                || !$checkEmpty) {
                $value = $_REQUEST[$key];
            }
        }
        return $value;
    }


    /**
     * @return string
     */
    public function process() {
        return 'Not implemented.';
    }

    /**
     * @return TranslateRestController|null
     */
    public function getController() {
        $fullAction = (isset($_REQUEST['action'])) ? trim($_REQUEST['action'],'/') : 'home';

        $controller = false;
        $splitAction = explode('/', $fullAction);
        $options = $_REQUEST;

        $action = reset($splitAction);
        switch ($action) {
            case 'gettranslations':
            case 'update':
                $controller = $action;
                break;
        }

        //var_dump($fullAction, $controller);
        if (!empty($controller)) {
            return $this->_loadController($controller, $options);
        }
        return 'API Endpoint does not exist.';
    }

    /**
     * @param $controller
     * @param $options
     *
     * @return TranslateRestController|null
     */
    protected function _loadController($controller, $options) {
        $c = null;
        $className = 'TranslateRest';
        $classParts = explode('/', $controller);
        $classParts = array_map('ucfirst', $classParts);
        $classParts = implode('', $classParts);
        $className .= $classParts . 'Controller';

        $path = dirname(__FILE__) . "/{$controller}.class.php";
        if (file_exists($path)) {
            require_once $path;
        }

        if (class_exists($className)) {
            /** @var TranslateRestController $c */
            $c = new $className($this->modx, $options);
            if (!$c->authUser()) {
                return 'No active session found, please make sure you are logged in and then try again.';
            }

            if (!$c->getNamespace()) return 'Invalid namespace';
            if (!$c->getTopic()) return 'Invalid topic';
            if (!$c->getLanguage()) return 'Invalid language';

            $c->logRequest();
            $c->initialize();
        } else {
            return 'Invalid request.';
        }

        return $c;
    }

    /**
     * Returns the error template with your message.
     *
     * @param $message
     *
     * @return mixed
     */
    public function returnError($message) {
        return $this->modx->toJSON(array('error' => true, 'message' => $message));
    }

    /**
     * Gets a REST template for the current handler.
     *
     * @param $name
     * @param array $properties
     *
     * @return mixed
     */
    public function getTpl($name, array $properties = array()) {
        return $this->translate->getTpl("rest/{$name}", $properties);
    }

    /**
     * Checks for a valid auth.
     * @return bool
     */
    private function authUser() {
        $this->user = null;

        if ($this->modx->user && $this->modx->user->get('id') > 0) {
            $this->user =& $this->modx->user;
            return true;
        }
        return false;
    }

    /**
     * Shortcut to modX::log.
     *
     * @param $level
     * @param $message
     */
    public function log ($level, $message) {
        $this->modx->log($level, $message);
    }

    /**
     * @param string $endpoint The target
     * @param array $params
     *
     * @return string
     */
    public function makeUrl($endpoint, array $params = array()) {
        $base = $this->modx->getOption('translate.rest_base_url', null, 'http://modmore/Translate/api/');

        return "{$base}{$endpoint}?".http_build_query($params);
    }

    public function logRequest() {
        $level = $this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
        $target = $this->modx->setLogTarget(array(
            'target' => 'FILE',
            'options' => array(
                'filename' => 'translate_activity.txt',
            ),
        ));

        $msg = array();
        $msg[] = substr(get_class($this), strlen('TranslateRest'), -strlen('Controller'));

        $requestData = $_GET;
        $ignoreData = array('action');
        foreach ($ignoreData as $key) {
            if (isset($requestData[$key])) {
                unset($requestData[$key]);
            }
        }
        $msg[] = $this->modx->toJSON($requestData);

        $msg = implode(' | ', $msg);
        $this->modx->log(modX::LOG_LEVEL_INFO, $msg);
        $this->modx->setLogLevel($level);
        $this->modx->setLogTarget($target);
    }

    /**
     * @return bool
     */
    private function getNamespace()
    {
        $ns = $this->modx->getObject('trNamespace', array('name' => (string)$_REQUEST['namespace']));
        if (!$ns) return false;

        $this->namespace = $ns;
        return true;
    }

    /**
     * @return bool
     */
    private function getTopic()
    {
        $topic = $this->modx->getObject('trTopic', array('topic' => (string)$_REQUEST['topic'], 'namespace' => $this->namespace->get('id')));
        if (!$topic) return false;

        $this->topic = $topic;
        return true;
    }

    /**
     * @return bool
     */
    private function getLanguage()
    {
        $language = $this->modx->getObject('trLanguage', array('code' => (string)$_REQUEST['language']));
        if (!$language) return false;

        $this->language = $language;
        return true;
    }
}
