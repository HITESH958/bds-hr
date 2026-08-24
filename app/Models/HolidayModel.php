<?php

namespace App\Models;

use CodeIgniter\Model;

class HolidayModel extends Model
{
    protected $table            = 'holidays';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['holiday_date', 'name', 'is_recurring'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'holiday_date' => 'required|valid_date|is_unique[holidays.holiday_date,id,{id}]',
        'name'         => 'required|max_length[150]',
    ];

    public function forYear(int $year): array
    {
        return $this->where('YEAR(holiday_date)', $year)
            ->orderBy('holiday_date', 'ASC')
            ->findAll();
    }

    /**
     * Holidays in a given month, keyed by day number. Includes both
     * exact-year holidays and recurring ones (matched by month/day only,
     * regardless of what year they were originally entered under) --
     * so a fixed-date holiday like Independence Day only needs entering once.
     */
    public function forMonth(int $year, int $month): array
    {
        $exact = $this->where('YEAR(holiday_date)', $year)
            ->where('MONTH(holiday_date)', $month)
            ->findAll();

        $recurring = $this->where('is_recurring', 1)
            ->where('MONTH(holiday_date)', $month)
            ->findAll();

        $byDay = [];
        foreach (array_merge($exact, $recurring) as $row) {
            $day         = (int) date('j', strtotime($row['holiday_date']));
            $byDay[$day] = $row['name'];
        }

        return $byDay;
    }

    public function isHoliday(string $date): ?string
    {
        $exact = $this->where('holiday_date', $date)->first();
        if ($exact) {
            return $exact['name'];
        }

        $month     = (int) date('n', strtotime($date));
        $day       = (int) date('j', strtotime($date));
        $recurring = $this->where('is_recurring', 1)
            ->where('MONTH(holiday_date)', $month)
            ->where('DAY(holiday_date)', $day)
            ->first();

        return $recurring ? $recurring['name'] : null;
    }
}
