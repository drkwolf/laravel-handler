# Introduction 
Laravel handler enable you to structure you code as packages

- install :
``` 
composer require drkwolf/laravel-handler:dev-master
```

## structure 
- app
  - packages
    - package_name
      - Entities
      - Events
      - Presenters
      - Controllers
      - tests

```php

$presenter = new ServicePresenter();
return ServicerHandler::resolve('create', [], $presenter, ...)

```

the presenter transform the data $JsonResponse$ (Laravel Resource)

## Presenter example

```php
<?php namespace App\Packages\Package\Presenter;

use App\Packages\Package\Presenter\PresenterAbstract;

class DefaultPresenter extends PresenterAbstract {

    public function successResponse($params = []) {
        return $this->resource;
    }
}

class InvoicePresenter extends OrmPresenterAbstract {

    public function successResponse($params = []) {
        switch ($this->action) {
            case 'fetchAll':
                $this->insertAction('invoices', $this->getCollection());
                $this->insertAction('invoiceItems', new InvoiceItemCollection($this->items));
                break;
        }
        return [ 'actions' => $this->getOrmActions() ];
    }

    private function getData() {
        return  [
            'id' => $this->id,
        ];
    }
}


```

## hanlder example

```php
<?php namespace App\Packages\Service;

use App\Packages\Package\HandlerAbstract;
use App\Models\Service;
use App\Packages\Service\Events\ServiceMembershipAttachEvent;
use App\Packages\Service\Events\ServiceMembershipDetachEvent;

class ServiceMembershipHandler extends HandlerAbstract {
    public $model;

    public function __construct($presenter, array $data, $model) {
        parent::__construct($presenter, $data);

        $method = 'findOrFail' ;
        $this->service = $this->getModel($service, Service::class, $method);
    }

    private createAction($params = []) {
        // do something

        // return data for presenter
        return $this->model;
    }

    protected function dataFields($action = null) {
        return [ 'providersIds' ];
    }

    public function rules($action = null, $params = []) {
        return [
            'providersIds' => [
                'required', 'array|min:1',
                $this->validateProvidersIds
            ]
        ];
    }

}

```

## Merging hanlder example

```php
$actions = collect();
\DB::beginTransaction();
try {
    /*
    |---------------------------------------------------------------------
    | Detach from Office
    |---------------------------------------------------------------------
    */
    $presenter = new OfficeMembershipPresenter();
    $handler = new OfficeMembershipHandler(
        $presenter,
        $customers_ids,
        $office_id,
        RolesTypes::CUSTOMER
    );

    $handler->execute('detach');
    $action = Arr::get($presenter->responseOrFail(), 'actions');
    $actions = $actions->concat($action);

    \DB::commit();
} catch (\Illuminate\Validation\ValidationException $e) {
    \DB::rollback();
    throw $e;
}

return response()->json([ 'actions' => $actions ]);
```
