<?php

require_once 'Module.php';

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
     * @var ColoCrossing_Client
     */
    protected $api;

    /**
     * Initialize References to Module and API Client
     */
    public function __construct() {
        $this->module = ColoCrossing_Module::getInstance();
        $this->api = $this->module->getAPIClient();
    }

    /**
     * Dispatches Request to Action and Renders The Actions View
     * Throws Exaception if Action is not Found.
     *
     * @param  string $action
     * @param  array  $params
     */
    public function dispatch($action, array $params) {
        $action = lcfirst(implode('', array_map(function ($word) {
            return ucfirst($word);
        }, explode('-', $action))));

        if(!method_exists($this, $action)) {
            throw new Exception('Action not found.');
        }

        if($this->$action($params) !== false) {
            $template_path = $this->getControllerActionTemplatePath($action);
            $this->renderTemplate($template_path, $params);
        }
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

        $path = $this->getViewDirectory() . $path;

        require $path;
    }

    /**
     * Get the Path of The Action Template Relative to the view directory
     * @param  string $action   The Action
     * @return string           The Path
     */
    protected function getControllerActionTemplatePath($action) {
        $class = get_class($this);

        //Convert to Dashed Name from Class Name
        $type = preg_replace('/Controller$/', '', preg_replace('/^ColoCrossing_/', '', $class));
        $type = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $type));

        return implode(DIRECTORY_SEPARATOR, array('', $type, $action . '.phtml'));
    }

    /**
     * Retrieves the View Directory Path
     *
     * @return string The Path
     */
    protected function getViewDirectory() {
        return implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'views'));
    }

    /**
     * Redirect to the Specified URL
     * @param  string $url The Destination URL
     */
    protected function redirectTo($url) {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect to the Specified Module URL
     * @param  string $module_url   The Module URL
     * @param  array  $params       The Query String Params
     */
    protected function redirectToModule($module_url, array $params = array()) {
        $this->redirectTo($module_url . '&' . http_build_query($params));
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
