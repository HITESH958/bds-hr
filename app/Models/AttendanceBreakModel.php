<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceBreakModel extends Model
{
    protected $table            = 'attendance_breaks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['attendance_id', 'break_start', 'break_end'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function forAttendance(int $attendanceId): array
    {
        return $this->where('attendance_id', $attendanceId)
            ->orderBy('break_start', 'ASC')
            ->findAll();
    }

    /**
     * The currently open break for this attendance record, if any
     * (break_end still null means the employee hasn't clicked "End Break").
     */
    public function openBreak(int $attendanceId): ?array
    {
        return $this->where('attendance_id', $attendanceId)
            ->where('break_end', null)
            ->first();
    }

    public function startBreak(int $attendanceId): array
    {
        $id = $this->insert([
            'attendance_id' => $attendanceId,
            'break_start'   => date('Y-m-d H:i:s'),
        ], true);

        return $this->find($id);
    }

    public function endBreak(int $breakId): array
    {
        $this->update($breakId, ['break_end' => date('Y-m-d H:i:s')]);

        return $this->find($breakId);
    }

    /**
     * Total break seconds for an attendance record. An open (unfinished)
     * break counts up to "now" so a live dashboard shows a running total.
     */
    public function totalBreakSeconds(int $attendanceId): int
    {
        $breaks = $this->forAttendance($attendanceId);
        $total  = 0;

        foreach ($breaks as $break) {
            $start = strtotime($break['break_start']);
            $end   = $break['break_end'] ? strtotime($break['break_end']) : time();
            $total += max(0, $end - $start);
        }

        return $total;
    }
}
