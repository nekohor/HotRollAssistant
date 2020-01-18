<?php

namespace App\Backend\Models;

use App\Backend\Permission\Urp;
use Illuminate\Database\Query\Builder;

class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * To exclude permission management from the list
     *
     * @param $query
     * @return Builder
     */
    public function scopeAllowed($query)
    {
        return $query->where('name', '!=', Urp::PERMISSION_PERMISSION_MANAGE);
    }
}
