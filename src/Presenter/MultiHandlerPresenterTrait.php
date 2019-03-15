<?php namespace drkwolf\Package\Presenter;

trait MultiHandlerPresenterTrait {
    /**
     * add new player to a team
     * @param Request $request
     * @param $club_id
     */
    public function mergeHandlersResponseOrFail($responses) {
        $actions = collect();
        $errors = collect();

        foreach ($responses as $response) {
            if (isset($response['errors'])) {
                $errors = $errors->concat($response['errors']);
            } else {
                if (isset($response['actions'])) {
                    $actions = $actions->concat($response['actions']);
                } else {
                    throw new \Exception('handler response missing actions field');
                }
            }
        }

        if ($errors->count()) {
            return response()->json([ 'errors' => $errors->toArray() ], 422);
        } else {
            return response()->json([ 'actions' => $actions->toArray() ], 200);
        }
    }
}
