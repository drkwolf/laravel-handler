<?php namespace drkwolf\Package;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */
abstract class HandlerAbstract extends ValidatableHandler {
    private $data = [];
    protected $filteredData = [];
    protected $overrideData = [];
    protected $object = 'undefined';
    protected $action;

    /** @var PresenterAbstract */
    protected $presenter;

    public function __construct($presenter, $data = [], $overrideData = []) {
        $this->presenter = $presenter;
        $this->overrideData = $overrideData;
        $this->setData($data);
    }

    public static function resolve($action, $params, ...$args) {
        $handler = new static(...$args);
        return $handler->execute($action, $params);
    }

   /**
    * set data and apply the filter
    */
    public function setData($data, $action = null) {
        $this->data = array_merge($data, $this->overrideData);
        $this->filterData($action);
    }

    /**
     * validate the data and execute the action
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function execute($action, $params = []) {
        $this->action = $action;
        $actionName = $action . 'Action';

        if (method_exists($this, $actionName)) {
            try {
                $this->filterData();
                $fails = $this->validate($action, $params)->fails();
                if ($fails) {
                    // $errors = $this->validator->errors()->messages();
                    $this->presenter->setResource($this->validator, $action, false);
                } else {
                    $res = $this->{$actionName}($params);
                    $this->presenter->setResource($res, $action, true);
                }
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \InvalidArgumentException("handle method $action missing");
        }

        return $this->presenter->response();
    }

    /**
     * allowed data fields, same syntax as for rules
     * @param null $action
     * @return array
     */
    protected function dataFields($action = null) {
        return [];
    }

    private function filterData($action = null) {
        $action = $action ? $action : $this->action;

        $fields = $this->dataFields($action);
        $this->filteredData = count($fields) == 0
            ? $this->data
            : $this->arrayDotOnly($this->data, $fields);

        // lad($this->data, $this->filteredData);

        return $this;
    }

    private function arrayDotOnly($array, $keys) {
        $newArray = [];
        foreach ((array) $keys as $key) {
            Arr::set($newArray, $key, Arr::get($array, $key));
        }
        return $newArray;
    }

    /*
     |-------------------------------------------------
     | Data filter
     |-------------------------------------------------
     */

    /**
     * return object from data
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null) {
        return Arr::get($this->data, $name, $default);
    }

    /**
     * data except elements
     * @param $name mixed
     * @return array
     */
    public function except($name) {
        return Arr::except($this->data, $name) ;
    }

    public function castDate($date, $type, $default = null) {
        if ($date) {
            if (! $date instanceof Carbon) $date = Carbon::parse($date);

            if ($type == 'date') return $date->toDateString();
            if ($type == 'datetime') return $date->toDateTimeString();
            if ($type == 'time') return $date->toTimeString();

        } else {
            return $default;
        }
    }


   /** 
    *  @params $model Number | Model
    */
    protected function getModelId($model) {
        return $model instanceof Model? $model->getKey() : $model;
    }

    /**
     * extract model from attribute
     * @param $object
     * @param $model
     * @param string $method: use FirstOrNew/ firstOrFail when Object is Key,
     *        or other Eleoquent search function followed by get() other firstOrNew ..
     * @return mixed
     * @example
     * $this->getModel('player', Role::class, 'whereName')->firstOrFail()
     * $this->getModel(1, Role::class, 'firstOrNew')
     */
    protected function getModel($object, $model, $method = 'findOrFail') {
        return $object instanceof $model
            ? $object
            : call_user_func_array([$model, $method], [$object]);
    }
}
