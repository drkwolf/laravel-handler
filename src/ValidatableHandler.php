<?php namespace \drkwolf\Package;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */

abstract class ValidatableHandler {

    protected $validator;
    // protected $response = [];
    protected $data = [];
    // protected $response_status = 200;

    /**
     * validation rules
     * @return array
     */
    abstract public function rules($action = null, $params = []);

    public function validate($action = null, $params = []) {
        $this->validator =  \Validator::make($this->data, $this->rules($action, $params));
        return $this->validator;
    }

    public function isInvalid() {
        if (!$this->validator) {
            $this->validate();
        }
        return $this->validator->fails();
    }

    public function errors() {
        if (!$this->validator) {
            $this->validate();
        }
        return $this->validator->errors()->messages();
    }
}
