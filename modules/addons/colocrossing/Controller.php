<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require 'admins/Controller.php';
require 'clients/Controller.php';

/**
 * ColoCrossing Controller for WHMCS Module.
 */
abstract class ColoCrossing_Controller {

    /**
     * The Module
     * @var ColoCrossing_Module
     */
    protected $module;

    /**
     * The API Client
     * @var ColoCrossing_API
     */
    protected $api;

    /**
     * The Base Url
     * @var string
     */
    protected $base_url;

    /**
     * Determines Whether or Not To Render Default View Template
     * @var boolean
     */
    protected $render;

    /**
     * Initialize References to Module and API Client
     */
    public function __construct() {
        $this->module = ColoCrossing_Module::getInstance();
        $this->api = ColoCrossing_API::getInstance();
        $this->base_url = $this->getBaseUrl();

        $this->render = true;
    }

    /**
     * @return string The Base Url
     */
    protected abstract function getBaseUrl();

    /**
     * Logs Message to Events
     * @param  string $description
     */
    protected abstract function log($description = '');

    /**
     * Sets the Provided Http Header
     * @param string $key
     * @param string $value
     */
    protected function setHeader($key, $value) {
        header($key . ': ' . $value);
    }

    /**
     * Sets the HTTP Response Code
     * @param integer $code
     */
    protected function setResponseCode($code = 200) {
        http_response_code($code);
    }

    /**
     * Enables the controller to render the action's view after the action is called
     */
    protected function enableRendering() {
        $this->render = true;
    }

    /**
     * Disables the controller from rendering the action's view
     */
    protected function disableRendering() {
        $this->render = false;
    }

    /**
     * Determines if the the controller should render the action's view
     * @return boolean True if it should render
     */
    protected function isRenderingEnabled() {
        return $this->render;
    }

    /**
     * Dispatches Request to Action and Renders The Actions View
     * Throws Exaception if Action is not Found.
     *
     * @param  string $action
     * @param  array  $params
     * @return mixed|null The Data Returned By the Action
     */
    public function dispatch($action, array $params) {
        $action = lcfirst(implode('', array_map(function ($word) {
            return ucfirst($word);
        }, explode('-', $action))));

        if(!method_exists($this, $action)) {
            throw new Exception('Action not found.');
        }

        $result = $this->$action($params);

        if($this->isRenderingEnabled()) {
            $path = $this->getControllerActionTemplatePath($action);
            $this->renderTemplate($path, $params);
        }

        return $result;
    }

    /**
     * Renders a Template
     *
     * @param  string $path   The path to template relative to the views directory i.e. /devices/view.phtml
     * @param  array  $params Params to pass to template
     */
    protected function renderTemplate($path, array $params = null) {
        if(isset($params)) {
            extract($params);

            if(is_null($params['params'])) {
                unset($params);
            }
        }

        $path = $this->getViewDirectoryPath() . $path;

        require $path;
    }

    /**
     * Renders JSON
     *
     * @param  array    $data   The data to render in JSON
     */
    protected function renderJSON(array $data = null) {
        ob_clean();
        ob_start();

        $this->setHeader('Content-Type', 'application/json');

        echo json_encode($data);

        ob_end_flush();
        exit;
    }

    /**
     * Renders a PNG Image
     *
     * @param  image    $image   The image
     */
    protected function renderImage($image) {
        ob_clean();
        ob_start();

        $this->setHeader('Content-Type', 'image/png');
        $this->setResponseCode(imagepng($image) ? 200 : 500);

        imagedestroy($image);

        ob_end_flush();
        exit;
    }

    /**
     * Get the Path of The Action Template Relative to the view directory
     * @param  string $action   The Action
     * @return string           The Path
     */
    protected function getControllerActionTemplatePath($action) {
        $class = get_class($this);

        //Convert to Dashed Name from Class Name
        $type = preg_replace('/Controller$/', '', preg_replace('/^ColoCrossing_(Admins|Clients)_/', '', $class));
        $type = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $type));

        return implode(DIRECTORY_SEPARATOR, array('', $type, $action . '.phtml'));
    }

    /**
     * Retrieves the View Directory Path
     *
     * @return string The Path
     */
    protected abstract function getViewDirectoryPath();

    /**
     * Redirect to the Specified URL
     * @param  string $url The Destination URL
     */
    protected function redirectToUrl($url) {
        $this->setHeader('Location', $url);
        exit;
    }

    /**
     * Redirect to the Specified Controller and Action
     *
     * @param  string $controller   The Controller
     * @param  string $action       The Action
     * @param  array  $params       The Query String Params
     */
    protected function redirectTo($controller, $action, array $params = array(), $hash = null) {
        $params['controller'] = $controller;
        $params['action'] = $action;

        $url = ColoCrossing_Utilities::buildUrl($this->base_url, $params);

        if(isset($hash)) {
            $url .= '#' . $hash;
        }

        $this->redirectToUrl($url);
    }

    /**
     * Renders a Flash Message if one Exists
     * @return  boolean True if Message is rendered
     */
    protected function renderFlashMessage() {
        $flash_message = $this->getFlashMessage();

        $this->clearFlashMessage();

        if(empty($flash_message) || empty($flash_message['type']) || empty($flash_message['content'])) {
            return false;
        }

        switch ($flash_message['type']) {
            case 'success':
                $this->renderTemplate('/flash-messages/success.phtml', $flash_message);
                break;
            case 'error':
                $this->renderTemplate('/flash-messages/error.phtml', $flash_message);
                break;
            default:
                $this->renderTemplate('/flash-messages/info.phtml', $flash_message);
                break;
        }

        return true;
    }

    /**
     * Retrieves the Flash Message
     * @return string The Message
     */
    protected function getFlashMessage() {
        return isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
    }

    /**
     * Sets the Flash Message.
     * @param string $content The Message Content
     * @param string $type    The Message Type. (info,success,error)
     */
    protected function setFlashMessage($content, $type = 'info') {
        $_SESSION['flash_message'] = array(
            'content' => $content,
            'type' => $type
        );
    }

    /**
     * Clears the Flash Message
     */
    protected function clearFlashMessage() {
        unset($_SESSION['flash_message']);
    }

}
