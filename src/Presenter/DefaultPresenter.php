<?php namespace App\Packages\Package\Presenter;

use App\Packages\Package\Presenter\PresenterAbstract;

class DefaultPresenter extends PresenterAbstract {

    public function successResponse($params = []) {
        return $this->resource;
    }
}
