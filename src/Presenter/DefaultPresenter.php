<?php namespace drkwolf\Package\Presenter;

class DefaultPresenter extends PresenterAbstract {

    public function successResponse($params = []) {
        return $this->resource;
    }
}
