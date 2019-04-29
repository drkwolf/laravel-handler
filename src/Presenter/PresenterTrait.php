<?php namespace drkwolf\Package\Presenter;

use drkwolf\Package\FailureTypes;
use Illuminate\Validation\ValidationException;

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

    public function failureResponse($asException = false, $type = null) {
        $response = [
            'type' => $this->when($type, $type),
            'errors'    => $this->resource->errors()->messages()
        ];
        if ($asException) {
            throw new ValidationException(
                $this->resource,
                response()->json( $response, 422)
            );
        } else {
            return $response;
        }
    }

    public function responseOrFail($asException = false) {
        if ($this->isValid) {
            return $this->successResponse();
        } else {
            return $this->failureResponse($asException, FailureTypes::VALIDATION);
        }
    }

    public function toArray($request) {
        if ($this->isValid) {
            return $this->successResponse();
        } else {
            return $this->failureResponse(false, FailureTypes::VALIDATION);
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

    public static function collection($collection)
    {
        return $collection->map(function ($item) {
            return (new static($item))->getData();
        });
    }

}
