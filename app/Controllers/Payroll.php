<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use App\Models\PayrollPeriodModel;
use App\Models\PayslipModel;
use App\Models\SalaryStructureModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;

class Payroll extends Controller
{
    protected SalaryStructureModel $salaryModel;
    protected PayrollPeriodModel   $periodModel;
    protected PayslipModel         $payslipModel;
    protected AttendanceModel      $attendanceModel;

    public function __construct()
    {
        $this->salaryModel     = new SalaryStructureModel();
        $this->periodModel     = new PayrollPeriodModel();
        $this->payslipModel    = new PayslipModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function salaryIndex()
    {
        return view('payroll/salary_structures', [
            'employees' => $this->salaryModel->listWithEmployees(),
        ]);
    }

    public function salaryEdit(int $employeeId)
    {
        $employeeModel = new EmployeeModel();
        $employee      = $employeeModel->find($employeeId);

        if (! $employee) {
            return redirect()->to('/payroll/salary')->with('error', 'Employee not found.');
        }

        return view('payroll/salary_form', [
            'employee' => $employee,
            'salary'   => $this->salaryModel->forEmployee($employeeId),
        ]);
    }

    public function salaryStore(int $employeeId)
    {
        $rules = [
            'basic'      => 'required|numeric',
            'hra'        => 'permit_empty|numeric',
            'allowances' => 'permit_empty|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->salaryModel->setForEmployee($employeeId, [
            'basic'          => $this->request->getPost('basic'),
            'hra'            => $this->request->getPost('hra') ?: 0,
            'allowances'     => $this->request->getPost('allowances') ?: 0,
            'effective_from' => date('Y-m-d'),
        ]);

        return redirect()->to('/payroll/salary')->with('success', 'Salary structure saved.');
    }

    public function periods()
    {
        return view('payroll/periods', [
            'periods' => $this->periodModel->allOrdered(),
        ]);
    }

    public function generate()
    {
        $month = (int) $this->request->getPost('month');
        $year  = (int) $this->request->getPost('year');

        if ($month < 1 || $month > 12 || $year < 2020) {
            return redirect()->to('/payroll/periods')->with('error', 'Invalid month/year.');
        }

        $period = $this->periodModel->findByMonthYear($month, $year);

        if (! $period) {
            $periodId = $this->periodModel->insert([
                'month'        => $month,
                'year'         => $year,
                'status'       => 'draft',
                'generated_by' => session()->get('user_id'),
                'generated_at' => date('Y-m-d H:i:s'),
            ], true);
        } else {
            $periodId = $period['id'];
        }

        $workingDays = $this->countWorkingDays($month, $year);

        $employeesWithSalary = $this->salaryModel->listWithEmployees();

        $generated = 0;
        $skipped   = 0;

        foreach ($employeesWithSalary as $emp) {
            if ($emp['basic'] === null) {
                $skipped++;
                continue;
            }

            if ($this->payslipModel->existsForPeriodAndEmployee($periodId, (int) $emp['employee_id'])) {
                continue;
            }

            $summary = $this->attendanceModel->monthlySummary((int) $emp['employee_id'], $year, $month);
            $lopDays = $summary['absent'] + ($summary['half_day'] * 0.5);

            $gross        = (float) $emp['basic'] + (float) $emp['hra'] + (float) $emp['allowances'];
            $perDayRate   = $workingDays > 0 ? $gross / $workingDays : 0;
            $lopDeduction = round($perDayRate * $lopDays, 2);
            $netPay       = round($gross - $lopDeduction, 2);

            $this->payslipModel->insert([
                'payroll_period_id' => $periodId,
                'employee_id'       => $emp['employee_id'],
                'basic'             => $emp['basic'],
                'hra'               => $emp['hra'],
                'allowances'        => $emp['allowances'],
                'gross_earnings'    => $gross,
                'working_days'      => $workingDays,
                'lop_days'          => $lopDays,
                'per_day_rate'      => round($perDayRate, 2),
                'lop_deduction'     => $lopDeduction,
                'net_pay'           => $netPay,
            ]);

            $generated++;
        }

        $message = "{$generated} payslip(s) generated.";
        if ($skipped > 0) {
            $message .= " {$skipped} employee(s) skipped (no salary structure set).";
        }

        return redirect()->to('/payroll/periods/' . $periodId)->with('success', $message);
    }

    public function payslips(int $periodId)
    {
        $period = $this->periodModel->find($periodId);

        if (! $period) {
            return redirect()->to('/payroll/periods')->with('error', 'Payroll period not found.');
        }

        return view('payroll/payslips', [
            'period'   => $period,
            'payslips' => $this->payslipModel->forPeriod($periodId),
        ]);
    }

    public function payslipView(int $payslipId)
    {
        $payslip = $this->getAuthorizedPayslip($payslipId);

        if ($payslip === null) {
            return redirect()->back()->with('error', 'Payslip not found or access denied.');
        }

        $employeeModel = new EmployeeModel();
        $period        = $this->periodModel->find($payslip['payroll_period_id']);

        return view('payroll/payslip_view', [
            'payslip'  => $payslip,
            'employee' => $employeeModel->find($payslip['employee_id']),
            'period'   => $period,
        ]);
    }

    public function payslipPdf(int $payslipId)
    {
        $payslip = $this->getAuthorizedPayslip($payslipId);

        if ($payslip === null) {
            return redirect()->back()->with('error', 'Payslip not found or access denied.');
        }

        $employeeModel = new EmployeeModel();
        $employee      = $employeeModel->find($payslip['employee_id']);
        $period        = $this->periodModel->find($payslip['payroll_period_id']);

        $html = view('payroll/payslip_pdf', [
            'payslip'  => $payslip,
            'employee' => $employee,
            'period'   => $period,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Payslip-' . $employee['employee_code'] . '-' .
            date('M-Y', mktime(0, 0, 0, $period['month'], 1, $period['year'])) . '.pdf';

        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    public function myPayslips()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'Your account is not linked to an employee record.');
        }

        return view('payroll/my_payslips', [
            'payslips' => $this->payslipModel->forEmployee((int) $employeeId),
        ]);
    }

    /**
     * Admin/HR: export a period's payslips as CSV -- the payroll register
     * you'd hand to an accountant or auditor.
     */
    public function exportPayslipsCsv(int $periodId)
    {
        $period = $this->periodModel->find($periodId);

        if (! $period) {
            return redirect()->to('/payroll/periods')->with('error', 'Payroll period not found.');
        }

        $payslips = $this->payslipModel->forPeriod($periodId);
        $filename = 'payroll-' . $period['year'] . '-' . str_pad((string) $period['month'], 2, '0', STR_PAD_LEFT) . '.csv';

        $output = fopen('php://temp', 'w');
        fputcsv($output, ['Employee Code', 'Name', 'Basic', 'HRA', 'Allowances', 'Gross', 'Working Days', 'LOP Days', 'LOP Deduction', 'Net Pay']);

        foreach ($payslips as $p) {
            fputcsv($output, [
                $p['employee_code'], $p['first_name'] . ' ' . $p['last_name'],
                $p['basic'], $p['hra'], $p['allowances'], $p['gross_earnings'],
                $p['working_days'], $p['lop_days'], $p['lop_deduction'], $p['net_pay'],
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function getAuthorizedPayslip(int $payslipId): ?array
    {
        $payslip = $this->payslipModel->find($payslipId);

        if (! $payslip) {
            return null;
        }

        $role       = session()->get('role');
        $employeeId = session()->get('employee_id');

        if (! in_array($role, ['admin', 'hr'], true) && (int) $payslip['employee_id'] !== (int) $employeeId) {
            return null;
        }

        return $payslip;
    }

    private function countWorkingDays(int $month, int $year): int
    {
        $days  = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $count = 0;

        for ($d = 1; $d <= $days; $d++) {
            $dayOfWeek = (int) date('N', mktime(0, 0, 0, $month, $d, $year));
            if ($dayOfWeek < 6) {
                $count++;
            }
        }

        return $count;
    }
}
