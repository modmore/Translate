<?php
require_once dirname(__FILE__) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

$corePath = $modx->getOption('translate.core_path',null,$modx->getOption('core_path').'components/translate/');
$Translate = $modx->getService('translate', 'Translate', $corePath . 'model/translate/');
if (!($Translate instanceof Translate)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to load Translate service from ' . $corePath);
    return 'Translate not found';
}

$debug = true;
if ($debug) {
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

require_once $corePath . 'controllers/rest/index.class.php';

$rest = new TranslateRestController($modx, array());

$controller = $rest->getController();

if ($controller instanceof TranslateRestController) {
    header('Content-Type: application/json');
    echo $controller->process();
} else {
    error_log('Controller error for: ' . print_r($_REQUEST, true) . ' | Error: ' . $controller);
    echo $rest->returnError($controller);
}
