<?php namespace drkwolf\Package\Presenter;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use drkwolf\Package\FailureTypes;
use Illuminate\Validation\ValidationException;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */
abstract class OrmPresenterAbstract extends JsonResource {
    use PresenterTrait;

    /** @var Collection */
    private $ormActions;


    public function __construct($resource = null, Validator $validator = null) {
        parent::__construct($resource);
        $this->assignArrayResource();
    }

    public function getOrmActions() {
        return $this->ormActions;
    }

    public function setResource($resource, $action, $isValid = true) {
        $this->setResource($resource, $action, $isValid);
        $this->resetActions();
    }

    private function resetActions() {
        $this->ormActions = collect();
    }

    public function responseOrFail() {
        if ($this->isValid) {
            $this->resetActions();
            return $this->successResponse();
        } else {
                // response()->json($this->failureResponse('validation'))
            throw new ValidationException(
                $this->resource,
                response()->json(
                    $this->failureResponse(),
                    422
                )
            );
        }
    }

    private function attachDetachAction($name, $entity, $relationship, $key, $data) {
       return $this->ormActions->push([
           'name'          => $name,
           'entity'        => $entity,
           'relationship'  => $relationship,
           'key'           => $key,
           'data'          => $data
       ]);
    }

    private function crudAction($name, $entity, $data) {
        return $this->ormActions->push([
            'name'    => $name,
            'entity'  => $entity,
            'data'    => $data
        ]);
    }

    public function updateAction($entity, $data) {
        return $this->crudAction('update', $entity, $data);
    }

    public function deleteAction($entity, $data) {
        return $this->crudAction('delete', $entity, $data);
    }

    public function insertAction($entity, $data) {
        return $this->crudAction('insert', $entity, $data);
    }

    protected function attachAction($entity, $relationship, $key, $data) {
        return $this->attachDetachAction('attach', $entity, $relationship, $key, $data);
    }

    protected function detachAction($entity, $relationship, $key, $data) {
        return $this->attachDetachAction('detach', $entity, $relationship, $key, $data);
    }


    /** 
     * @override
     */
    public function toArray($request) {
        if ($this->isValid) {
            $this->resetActions();
            return $this->successResponse();
        } else {
            return $this->failureResponse(FailureTypes::VALIDATION);
        }
    }
}
