<?php namespace drkwolf\Package\Presenter;

use Illuminate\Validation\Validator;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */
trait PresenterTrait {

    protected $isValid;
    protected $action;

    public function setResource($resource, $action, $isValid = true) {
        $this->resource = $resource;
        $this->isValid = $isValid;
        $this->action = $action;

        $this->assignArrayResource();
    }

    protected function assignArrayResource() {
        if (!is_array($this->resource)) {
            return;
        }
        foreach ($this->resource as $key => $value) {
            $this->{$key}  = $value;
        }
    }

    abstract public function successResponse($params = []);

    public function failureResponse($type = '', $object = '') :array {
        return [
            'type'      => $this->when($type, $type),
            'object'    => $this->when($object, $object),
            'errors'    => $this->resource
        ];
    }

    public function toArray($request) {
        if ($this->isValid) {
            return $this->successResponse();
        } else {
            return $this->failureResponse(FailureTypes::VALIDATION);
        }
    }

    /**
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Http\JsonResponse
     * @override
     */
    public function response($request = null) {
        return parent::response($request)
            ->setStatusCode(
                $this->isValid? 200 : 422
            );
    }
}
