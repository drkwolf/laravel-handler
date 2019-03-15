<?php namespace App\Packages\Package\Presenter;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\Validator;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */
abstract class PresenterCollectionAbstract extends ResourceCollection {

    use PresenterTrait;

    public function __construct($resource = [], Validator $validator = null) {
        parent::__construct(collect($resource));
        $this->validator = $validator ? $validator : \Validator::make([], []) ;
    }

    public function setResource($resource, $action, $isValid = true) {
        $this->collection = $this->collectResource($resource);
        $this->isValid = $isValid;
        $this->action = $action;
    }
}
