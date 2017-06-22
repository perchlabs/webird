<?php
namespace Webird\Models;

use Webird\Mvc\Model;
use Webird\Models\Users;
use Webird\Models\AuditDetail;

/**
 *
 */
class Audit extends Model
{

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('usersId', Users::class, 'id', [
            'alias' => 'user',
        ]);

        $this->hasMany('id', AuditDetail::class, 'audit_id', [
            'alias' => 'details',
        ]);
    }
}
