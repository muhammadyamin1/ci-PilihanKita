<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminActivityLogModel extends Model
{
    protected $table            = 'admin_activity_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Log aktivitas admin
     */
    public function logActivity($adminId, $action, $targetType, $targetId, $description, $oldValue = null, $newValue = null)
    {
        $this->insert([
            'admin_id'    => $adminId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => $description,
            'old_value'   => $oldValue ? json_encode($oldValue) : null,
            'new_value'   => $newValue ? json_encode($newValue) : null,
            'ip_address'  => service('request')->getIPAddress(),
            'user_agent'  => service('request')->getUserAgent()->getAgentString()
        ]);
    }

    /**
     * Ambil log aktivitas untuk admin tertentu
     */
    public function getLogsByAdmin($adminId, $limit = 100)
    {
        return $this->where('admin_id', $adminId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ambil log aktivitas berdasarkan target
     */
    public function getLogsByTarget($targetType, $targetId)
    {
        return $this->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}