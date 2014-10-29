<?php
namespace Webird\Api\Controllers;

use Webird\Controllers\RESTController;

/**
 * The user API.
 */
class UsersController extends RESTController
{
    /**
     * Default action.
     */
    public function initialize()
    {
        parent::initialize();
    }

    protected function resultsToArray($modelArr)
    {
        $results = [];
        foreach ($modelArr as $model) {
            $results[] = [
                'id'    => $model->id,
                'email' => $model->email,
                'name'  => $model->name,
                'role'  => $model->role->name
            ];
        }

        return $results;
    }

}
