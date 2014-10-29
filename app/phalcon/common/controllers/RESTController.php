<?php
namespace Webird\Controllers;

use Phalcon\Mvc\Controller,
    Phalcon\Http\Response as Response,
    Webird\Controllers\BaseController;

class RESTController extends BaseController
{
    protected $statusCode;
    protected $headers;
    protected $payload;
    protected $format;

    /**
     * Getters
     */

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return !empty($this->format) ? $this->format : $this->config->app_format;
    }

    /**
     * Setters
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    public function setHeaders($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    protected function getModelFromController()
    {
        $parts = explode('\\', get_called_class());
        $controllerName = end($parts);

        $matches = preg_split('/(?=[A-Z])/', $controllerName);
        $modelName = $matches[1];
        $modelFullName = "\\Webird\\Models\\$modelName";

        return $modelFullName;
    }

    // Override this
    protected function resultsToArray($modelArr)
    {
        $results = [];
        foreach ($modelArr as $model) {
            $results[] = [
                'id' => $model->id
            ];
        }

        return $results;
    }

    protected function getResultset()
    {
        $class = $this->getModelFromController();
        $data = $class::find();
        return $this->resultsToArray($data);
    }

    protected function initResponse()
    {
        $this->statusCode = 200;
        $this->format     = 'json';
        // $this->format     = 'msgpack';
        $this->headers    = [];
        $this->payload    = '';
    }

    protected function render()
    {
        $format      = $this->getFormat();
        $payload     = $this->getPayload();
        $status      = $this->getStatusCode();
        $description = $this->getResponseDescription($status);
        $headers     = $this->getHeaders();

        switch ($format)
        {
            case 'json':
                $contentType = 'application/json';
                $content     = json_encode($payload);
                break;
            case 'msgpack':
                $contentType = 'application/x-msgpack';
                $content     = msgpack_pack($payload);
                break;
            default:
                $contentType = 'text/plain';
                $content     = $payload;
                break;
        }

        $response = new Response();

        $response->setStatusCode($status, $description);
        $response->setContentType($contentType, 'UTF-8');
        // $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setContent($content);

        // Set the additional headers
        foreach ($headers as $key => $value) {
            $response->setHeader($key, $value);
        }

        $this->view->disable();

        return $response;
    }


    public function listAction()
    {
        $results = $this->getResultset();

        $this->initResponse();
        $this->setPayload($results);
        return $this->render();
    }


    public function countAction()
    {
        $modelName = $this->getModelFromController();

        $results = ['count' => $modelName::count()];
        $this->initResponse();
        $this->setPayload($results);

        return $this->render();
    }

    protected function getResponseDescription($code)
    {
        $codes = [

            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',

            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',

            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        ];

        $result = (isset($codes[$code])) ?
            $codes[$code]          :
            'Unknown Status Code';

        return $result;
    }




    // public function getAction($field, $value)
    // {
    //     // implement some functionality to get the class name
    //     // and the method from the get_called_class()
    //     $class  = controllerToModel(get_called_class());
    //     $node   = controllerToClass(get_called_class());
    //     $method = 'fetchBy' . ucfirst($field);
    //     $node   = strtolower($node);
    //
    //     $data = $class::$method($value);
    //
    //     return $data;
    // }

    // public function postAction()
    // {
    //     if ($this->request->isPost()) {
    //
    //         $payload = $_POST;
    //         $class   = controllerToModel(get_called_class());
    //
    //         $object = new $class();
    //
    //         foreach ($payload as $field => $value) {
    //             $object->$field = $value;
    //         }
    //
    //         $status = $object->save();
    //
    //         $data           = [];
    //         $data['status'] = $status;
    //
    //         if (!$status) {
    //
    //             $data['errno'] = -1;
    //
    //             foreach ($object->getMessages() as $message) {
    //                 $data['error'][] = $message;
    //             }
    //         }
    //
    //         // @todo Check for errors here
    //         return $data;
    //     }
    // }

    // public function putAction()
    // {
    //     if ($this->request->isPost()) {
    //
    //         $payload = $_POST;
    //         $class   = controllerToModel(get_called_class());
    //
    //         $object = new $class();
    //
    //         foreach ($payload as $field => $value) {
    //             $object->$field = $value;
    //         }
    //
    //         $status = $object->save();
    //
    //         $data           = [];
    //         $data['status'] = $status;
    //
    //         if (!$status) {
    //
    //             $data['errno'] = -1;
    //
    //             foreach ($object->getMessages() as $message) {
    //                 $data['error'][] = $message;
    //             }
    //         }
    //
    //         // @todo Check for errors here
    //         return $data;
    //     }
    // }


}
