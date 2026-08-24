<?php

namespace App\Controllers;

use App\Models\HolidayModel;
use CodeIgniter\Controller;

class Holidays extends Controller
{
    protected HolidayModel $holidayModel;

    public function __construct()
    {
        $this->holidayModel = new HolidayModel();
    }

    public function index()
    {
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        return view('holidays/index', [
            'holidays' => $this->holidayModel->forYear($year),
            'year'     => $year,
        ]);
    }

    public function store()
    {
        $rules = [
            'holiday_date' => 'required|valid_date',
            'name'         => 'required|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost(['holiday_date', 'name']);
        $data['is_recurring'] = $this->request->getPost('is_recurring') ? 1 : 0;

        if (! $this->holidayModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->holidayModel->errors());
        }

        return redirect()->to('/holidays')->with('success', 'Holiday added.');
    }

    public function delete(int $id)
    {
        $this->holidayModel->delete($id);

        return redirect()->to('/holidays')->with('success', 'Holiday removed.');
    }
}
