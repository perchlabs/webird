<?php
namespace Webird\Http\ServerSent;

use JsonSerializable;

/**
 *
 */
class Event
{

    /**
     *
     */
    protected $name;

    /**
     *
     */
    protected $data;

    /**
     *
     */
    protected $id;

    /**
     *
     */
    protected $retry;

    /**
     *
     */
    protected $checkValidity;

    /**
     *
     */
    public function __construct($checkValidity = true)
    {
        $this->checkValidity = (bool) $checkValidity;
        $this->data = [];
    }

    /**
     *
     */
    public function __toString()
    {
        $output = '';
        if (isset($this->name)) {
            $output .= 'event:' . $this->name . "\n";
        }
        if (isset($this->id)) {
            $output .= 'id:' . $this->id . "\n";
        }
        if (isset($this->retry)) {
            $output .= 'retry:' . $this->retry . "\n";
        }

        if (!empty($this->data)) {
            $output .= implode("\n", $this->data) . "\n";
        }
        $output .= "\n";

        return $output;
    }

    /**
     *
     */
    public function setName($name)
    {
        // TODO: More checks
        if (!is_string($name)) {
            throw new \Exception('The event-source event name must be a string.');
        }

        if ($this->checkValidity && strpos($name, "\n") !== false) {
            throw new \Exception('The event-source event name must not contain a newline character.');
        }

        $this->name = $name;

        return $this;
    }

    /**
     *
     */
    public function setId($id)
    {
        $this->id = $this->convertStringField($id);

        return $this;
    }

    /**
     *
     */
    public function setRetry($retry)
    {
        if (!is_int($retry)) {
            throw new \Exception('The event-source retry must be an integer.');
        }
        $this->retry = $retry;

        return $this;
    }

    /**
     *
     */
    public function addData($data)
    {
        $this->data[] = 'data:' . $this->convertStringField($data);

        return $this;
    }

    /**
     *
     */
    protected function convertStringField($dataRaw)
    {
        switch (gettype($dataRaw)) {
            case 'array':
                $data = json_encode($dataRaw, JSON_UNESCAPED_UNICODE, 10);
                break;
            case 'object':
                if (!($dataRaw instanceof JsonSerializable)) {
                    throw new \Exception('The object could not be converted into JSON.');
                }
                $data = json_encode($dataRaw, JSON_UNESCAPED_UNICODE, 10);
                break;
            case 'string':
                if ($this->checkValidity && strpos($dataRaw, "\n") !== false) {
                    throw new \Exception('An event-source line must not contain a newline character until the end.');
                }
                $data = $dataRaw;
                break;
            default:
                throw new \Exception('Invalid data type.');
                break;
        }

        return $data;
    }
}
