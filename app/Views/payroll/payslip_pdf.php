<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e2530; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2f5c8f; padding-bottom: 10px; }
    .header h1 { color: #2f5c8f; margin: 0 0 4px; font-size: 20px; }
    .header p { margin: 0; color: #6b7280; }
    .meta { width: 100%; margin-bottom: 16px; }
    .meta td { padding: 3px 0; }
    .meta .label { color: #6b7280; width: 140px; }
    table.pay-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table.pay-table th, table.pay-table td { border: 1px solid #d7dce3; padding: 8px 10px; text-align: left; }
    table.pay-table th { background: #eef1f5; }
    .amount { text-align: right; }
    .net-pay-box { background: #eef1f5; padding: 14px; text-align: center; border-radius: 6px; margin-top: 10px; }
    .net-pay-box .label { color: #6b7280; font-size: 11px; }
    .net-pay-box .amount { font-size: 22px; font-weight: bold; color: #2f5c8f; text-align: center; }
    .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <h1>BDS HR</h1>
    <p>Payslip for <?= esc(date('F Y', mktime(0, 0, 0, $period['month'], 1, $period['year']))) ?></p>
</div>

<table class="meta">
    <tr>
        <td class="label">Employee</td>
        <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
        <td class="label">Employee Code</td>
        <td><?= esc($employee['employee_code']) ?></td>
    </tr>
    <tr>
        <td class="label">Designation</td>
        <td><?= esc($employee['designation'] ?? '-') ?></td>
        <td class="label">Pay Period</td>
        <td><?= esc(date('F Y', mktime(0, 0, 0, $period['month'], 1, $period['year']))) ?></td>
    </tr>
</table>

<table class="pay-table">
    <tr><th>Earnings</th><th class="amount">Amount</th></tr>
    <tr><td>Basic</td><td class="amount"><?= esc(number_format((float) $payslip['basic'], 2)) ?></td></tr>
    <tr><td>HRA</td><td class="amount"><?= esc(number_format((float) $payslip['hra'], 2)) ?></td></tr>
    <tr><td>Allowances</td><td class="amount"><?= esc(number_format((float) $payslip['allowances'], 2)) ?></td></tr>
    <tr><th>Gross Earnings</th><th class="amount"><?= esc(number_format((float) $payslip['gross_earnings'], 2)) ?></th></tr>
</table>

<table class="pay-table">
    <tr><th colspan="2">Attendance & Deductions</th></tr>
    <tr><td>Working Days</td><td class="amount"><?= esc($payslip['working_days']) ?></td></tr>
    <tr><td>LOP Days</td><td class="amount"><?= esc($payslip['lop_days']) ?></td></tr>
    <tr><td>Per-Day Rate</td><td class="amount"><?= esc(number_format((float) $payslip['per_day_rate'], 2)) ?></td></tr>
    <tr><td>LOP Deduction</td><td class="amount">-<?= esc(number_format((float) $payslip['lop_deduction'], 2)) ?></td></tr>
</table>

<div class="net-pay-box">
    <div class="label">NET PAY</div>
    <div class="amount"><?= esc(number_format((float) $payslip['net_pay'], 2)) ?></div>
</div>

<div class="footer">
    This is a computer-generated payslip and does not require a signature.
</div>

</body>
</html>
