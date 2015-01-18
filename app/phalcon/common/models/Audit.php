<?php
namespace Webird\Models;

use Webird\Mvc\Model;

class Audit extends Model
{

    public function initialize()
    {
        $this->belongsTo('usersId', 'Webird\Models\Users', 'id', [
            'alias' => 'user'
        ]);

        $this->hasMany('id', 'Webird\Models\AuditDetail', 'audit_id', [
            'alias' => 'details'
        ]);
    }
}
