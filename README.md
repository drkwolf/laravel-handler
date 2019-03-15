# Introduction 

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

```

## hanlder Example

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