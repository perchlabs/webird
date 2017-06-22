<?php
namespace Webird\Models;

use Webird\Mvc\Model;
use Webird\Models\Audit;

/**
 *
 */
class AuditDetail extends Model
{

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('audit_id', Audit::class, 'id');
    }
}
