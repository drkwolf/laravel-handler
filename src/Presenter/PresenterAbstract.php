<?php namespace App\Packages\Package\Presenter;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

/**
 * Use case Helper

 * @author Mehdi Ab <drkwolf@gmail.com>
 * @date 12/5/18
 * @time 3:47 PM
 */
abstract class PresenterAbstract extends JsonResource {
    use PresenterTrait;

    /** @var Collection */
    public $actionsCollect;


    public function __construct($resource = null, Validator $validator = null) {
        parent::__construct($resource);
        $this->assignArrayResource();
        $this->actionsCollect = collect();
    }

    public function resetActions() {
        $this->actionsCollect = collect();
    }
}
