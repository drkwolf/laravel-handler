<?php  namespace drkwolf\Package\tests;

use Illuminate\Http\Request;

Trait ControllerTestTrait {

    public function buildRequest($data, $method = null)
    {
        $_SERVER['__received'] = false;
        return Request::create('/', $method,
            $parameters = [],
            $cookies = [],
            $files = [],
            $server = ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    /**
     * @param $data
     * @return Request
     */
    public function postRequest($data) {
        return $this->buildRequest($data, 'POST');
    }

    /**
     * @return array response data
     */
    public function runControllerAction($controller, $action, $data, $method = 'POST', $status = 200) {
       $post = $this->buildRequest($data, $method);
       $controller = new $controller();
       $httpRes = $controller->{$action}($post);
       $this->assertEquals($httpRes->getStatusCode(), $status);
       return $httpRes->getData(true);
    }
}