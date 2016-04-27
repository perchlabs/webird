<?php
namespace Webird\Models;

use Webird\Mvc\Model;

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
        $this->belongsTo('audit_id', 'Webird\Models\Audit', 'id');
    }
}
