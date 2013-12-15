<?php
/* Get the core config */
if (!file_exists(dirname(dirname(__FILE__)).'/config.core.php')) {
    die('ERROR: missing '.dirname(dirname(__FILE__)).'/config.core.php file defining the MODX core path.');
}

echo "<pre>";
/* Boot up MODX */
echo "Loading modX...\n";
require_once dirname(dirname(__FILE__)).'/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
echo "Initializing manager...\n";
$modx->initialize('mgr');
$modx->getService('error','error.modError', '', '');

$componentPath = dirname(dirname(__FILE__));

$Translate = $modx->getService('translate','Translate', $componentPath.'/core/components/translate/model/translate/', array(
    'translate.core_path' => $componentPath.'/core/components/translate/',
));


/* Namespace */
if (!createObject('modNamespace',array(
    'name' => 'translate',
    'path' => $componentPath.'/core/components/translate/',
    'assets_path' => $componentPath.'/assets/components/translate/',
),'name', false)) {
    echo "Error creating namespace translate.\n";
}

/* Path settings */
if (!createObject('modSystemSetting', array(
    'key' => 'translate.core_path',
    'value' => $componentPath.'/core/components/translate/',
    'xtype' => 'textfield',
    'namespace' => 'translate',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating translate.core_path setting.\n";
}

if (!createObject('modSystemSetting', array(
    'key' => 'translate.assets_path',
    'value' => $componentPath.'/assets/components/translate/',
    'xtype' => 'textfield',
    'namespace' => 'translate',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating translate.assets_path setting.\n";
}

/* Fetch assets url */
$url = 'http';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
    $url .= 's';
}
$url .= '://'.$_SERVER["SERVER_NAME"];
if ($_SERVER['SERVER_PORT'] != '80') {
    $url .= ':'.$_SERVER['SERVER_PORT'];
}
$requestUri = $_SERVER['REQUEST_URI'];
$bootstrapPos = strpos($requestUri, '_bootstrap/');
$requestUri = rtrim(substr($requestUri, 0, $bootstrapPos), '/').'/';
$assetsUrl = "{$url}{$requestUri}assets/components/translate/";

if (!createObject('modSystemSetting', array(
    'key' => 'translate.assets_url',
    'value' => $assetsUrl,
    'xtype' => 'textfield',
    'namespace' => 'translate',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating translate.assets_url setting.\n";
}

/*if (!createObject('modAction', array(
    'namespace' => 'translate',
    'parent' => '0',
    'controller' => 'index',
    'haslayout' => '1',
    'lang_topics' => 'translate:default',
), 'namespace', false)) {
    echo "Error creating action.\n";
}
$action = $modx->getObject('modAction', array(
    'namespace' => 'translate'
));

if ($action) {
    if (!createObject('modMenu', array(
        'text' => 'translate',
        'parent' => 'components',
        'description' => 'translate.desc',
        'icon' => 'images/icons/plugin.gif',
        'menuindex' => '0',
        'action' => $action->get('id')
    ), 'text', false)) {
        echo "Error creating menu.\n";
    }
}*/

$manager = $modx->getManager();


/* Create the tables */
$objectContainers = array(
    'trEntry',
    'trLanguage',
    'trLanguageSet',
    'trMaintainer',
    'trNamespace',
    'trTopic',
);
echo "Creating tables...\n";

foreach ($objectContainers as $oC) {
    $manager->createObjectContainer($oC);
}
$level = $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

//db changes here

$modx->setLogLevel($level);

if (!createObject('modCategory', array(
    'category' => 'Translate',
    'parent' => 0,
), 'category', false)) {
    echo "Error creating Category.\n";
}

$categoryId = 0;
$category = $modx->getObject('modCategory', array('category' => 'Translate'));
if ($category) {
    $categoryId = $category->get('id');
}


/**
 * Creates an object.
 *
 * @param string $className
 * @param array $data
 * @param string $primaryField
 * @param bool $update
 * @return bool
 */
function createObject ($className = '', array $data = array(), $primaryField = '', $update = true) {
    global $modx;
    /* @var xPDOObject $object */
    $object = null;

    /* Attempt to get the existing object */
    if (!empty($primaryField)) {
        $condition = array($primaryField => $data[$primaryField]);
        if (is_array($primaryField)) {
            $condition = array();
            foreach ($primaryField as $key) {
                $condition[$key] = $data[$key];
            }
        }
        $object = $modx->getObject($className, $condition);
        if ($object instanceof $className) {
            if ($update) {
                $object->fromArray($data);
                return $object->save();
            } else {
                $condition = $modx->toJSON($condition);
                echo "Skipping {$className} {$condition}: already exists.\n";
                return true;
            }
        }
    }

    /* Create new object if it doesn't exist */
    if (!$object) {
        $object = $modx->newObject($className);
        $object->fromArray($data, '', true);
        return $object->save();
    }

    return false;
}
