<?php

spl_autoload_register('apiAutoload');
function apiAutoload($classname)
{
    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        @include __DIR__ . '/controllers/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        @include __DIR__ . '/models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
        @include __DIR__ . '/views/' . $classname . '.php';
        return true;
    }
}

class Request {
    public $url_elements;
    public $verb;
    public $parameters;
 
    public function __construct() {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        $this->url_elements = (isset($_SERVER['PATH_INFO'])) ? explode('/', $_SERVER['PATH_INFO']) : array();
        $this->parseIncomingParams();
        // initialise json as default format
        $this->format = 'json';
        if(isset($this->parameters['format'])) {
            $this->format = $this->parameters['format'];
        }
        $this->route();
        return true;
    }
 
    public function parseIncomingParams() {
        $parameters = array();
 
        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        }
 
        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents("php://input");
        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }
        switch($content_type) {
            case "application/json":
                $body_params = json_decode($body);
                if($body_params) {
                    foreach($body_params as $param_name => $param_value) {
                        $parameters[$param_name] = $param_value;
                    }
                }
                $this->format = "json";
                break;
            case "application/x-www-form-urlencoded":
                parse_str($body, $postvars);
                foreach($postvars as $field => $value) {
                    $parameters[$field] = $value;
 
                }
                $this->format = "html";
                break;
            default:
                // we could parse other supported formats here
                break;
        }
        $this->parameters = $parameters;
    }

    public function route()
    {
        if(!isset($this->url_elements[1]))
            return $this->error(400,'What do you want?');
        // route the request to the right place
        $controller_name = ucfirst($this->url_elements[1]) . 'Controller';
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            $action_name = strtolower($this->verb) . 'Action';
            $result = $controller->$action_name(&$this);

            $view_name = ucfirst($this->format) . 'View';
            if(class_exists($view_name)) {
                $view = new $view_name();
                $view->render($result);
            }
        }
        else
            return $this->error(404,'This controller doesn\'t exist.');
    }

    public function error($code = 500,$error = '')
    {
        switch($code) {
            case 400: $err = 'Bad Request'; break;
            case 403: $err = 'Forbidden'; break;
            case 404: $err = 'Not Found'; break;
            case 500: $err = 'Internal Server Error'; break;
        }
        header('HTTP/1.1 '.$code.' '.$err);
        $view = new JsonView();
        $view->render(array('code' => $code, 'error' => $error));
        exit;
    }
}

new Request();