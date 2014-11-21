<?php
namespace Webird\Models;

use Phalcon\Mvc\Model;

class AuditDetail extends Model
{

    public function initialize()
    {
        $this->belongsTo('audit_id', 'Webird\Models\Audit', 'id');
    }
}
