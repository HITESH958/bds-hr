<?php helper('hr'); ?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-greeting">
    <h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 17 ? 'afternoon' : 'evening') ?>, <?= esc(session()->get('username')) ?></h1>
    <p class="text-muted"><?= esc(date('l, j F Y')) ?></p>
</div>

<div class="today-layout">
    <div class="attendance-box">
        <?php if (! $today || ! $today['check_in']): ?>
            <?php if ($today && $today['status'] === 'on_leave'): ?>
                <div class="today-empty-state">
                    <svg viewBox="0 0 48 48" fill="none" class="empty-state-icon"><rect x="8" y="10" width="32" height="30" rx="3" stroke="currentColor" stroke-width="2"/><path d="M8 18h32M16 6v8M32 6v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <p>You're marked as on approved leave today.</p>
                    <span class="badge badge-on_leave">On Leave</span>
                </div>
            <?php else: ?>
                <div class="today-empty-state">
                    <svg viewBox="0 0 48 48" fill="none" class="empty-state-icon"><circle cx="24" cy="24" r="17" stroke="currentColor" stroke-width="2"/><path d="M24 15v9l6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p>You haven't checked in today.</p>
                    <form action="<?= site_url('attendance/check-in') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-primary">
                            <svg class="btn-icon" viewBox="0 0 20 20" fill="none"><path d="M13 3l5 7-5 7M3 10h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Check In
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="today-header">
                <div>
                    <span class="today-label">Today</span>
                    <p class="today-times">
                        <strong><?= esc(date('h:i A', strtotime($today['check_in']))) ?></strong>
                        <?php if ($today['check_out']): ?>
                            &rarr; <strong><?= esc(date('h:i A', strtotime($today['check_out']))) ?></strong>
                        <?php else: ?>
                            &rarr; <span class="text-muted">still checked in</span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($today['check_out']): ?>
                    <span class="badge badge-<?= esc($today['status']) ?>"><?= esc(format_status($today['status'])) ?></span>
                <?php elseif ($openBreak): ?>
                    <span class="badge badge-inactive">On Break</span>
                <?php endif; ?>
            </div>

            <?php
                $totalForBar = $todayHours['productive_hours'] + $todayHours['break_hours'];
                $prodPct     = $totalForBar > 0 ? round(($todayHours['productive_hours'] / $totalForBar) * 100) : 0;
                $breakPct    = 100 - $prodPct;
            ?>
            <?php if ($totalForBar > 0): ?>
            <div class="hours-bar" title="<?= esc(format_hours($todayHours['productive_hours'])) ?> productive, <?= esc(format_hours($todayHours['break_hours'])) ?> break">
                <div class="hours-bar-productive" style="width: <?= $prodPct ?>%"></div>
                <div class="hours-bar-break" style="width: <?= $breakPct ?>%"></div>
            </div>
            <?php endif; ?>

            <div class="hours-row">
                <div class="hours-box">
                    <svg class="hours-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="hours-number"><?= esc(format_hours($todayHours['login_hours'])) ?></span>
                    <span class="hours-label">Login Time</span>
                </div>
                <div class="hours-box hours-box-accent">
                    <svg class="hours-icon" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="hours-number"><?= esc(format_hours($todayHours['productive_hours'])) ?></span>
                    <span class="hours-label">Productive</span>
                </div>
                <div class="hours-box">
                    <svg class="hours-icon" viewBox="0 0 20 20" fill="none"><rect x="4" y="6" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 6V4.5A1.5 1.5 0 0 1 8.5 3h1a1.5 1.5 0 0 1 1.5 1.5V6" stroke="currentColor" stroke-width="1.5"/><path d="M14 8.5h2v4h-2" stroke="currentColor" stroke-width="1.5"/></svg>
                    <span class="hours-number"><?= esc(format_hours($todayHours['break_hours'])) ?></span>
                    <span class="hours-label">On Break</span>
                </div>
            </div>

            <?php if (! $today['check_out']): ?>
                <div class="action-row">
                    <?php if ($openBreak): ?>
                        <span class="badge badge-inactive">On Break since <?= esc(date('h:i A', strtotime($openBreak['break_start']))) ?></span>
                        <form action="<?= site_url('attendance/end-break') ?>" method="post" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-secondary">End Break</button>
                        </form>
                    <?php else: ?>
                        <form action="<?= site_url('attendance/start-break') ?>" method="post" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-secondary">Start Break</button>
                        </form>
                    <?php endif; ?>

                    <form action="<?= site_url('attendance/check-out') ?>" method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-primary" <?= $openBreak ? 'disabled title="End your break first"' : '' ?>>Check Out</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (! empty($todayBreaks)): ?>
            <table class="data-table" style="margin-top: 16px;">
                <thead><tr><th>Break Start</th><th>Break End</th></tr></thead>
                <tbody>
                    <?php foreach ($todayBreaks as $b): ?>
                    <tr>
                        <td><?= esc(date('h:i A', strtotime($b['break_start']))) ?></td>
                        <td><?= $b['break_end'] ? esc(date('h:i A', strtotime($b['break_end']))) : 'In progress' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="month-summary-panel">
        <h2 style="margin-top: 0;">This Month</h2>
        <div class="summary-list">
            <div class="summary-row">
                <svg class="summary-icon summary-icon-present" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 10l2.5 2.5 4.5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Present</span>
                <strong><?= esc($summary['present']) ?></strong>
            </div>
            <div class="summary-row">
                <svg class="summary-icon summary-icon-late" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Late</span>
                <strong><?= esc($summary['late']) ?></strong>
            </div>
            <div class="summary-row">
                <svg class="summary-icon summary-icon-half" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M10 2v16A8 8 0 0 0 10 2z" fill="currentColor"/></svg>
                <span>Half Day</span>
                <strong><?= esc($summary['half_day']) ?></strong>
            </div>
            <div class="summary-row">
                <svg class="summary-icon summary-icon-absent" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M7 7l6 6M13 7l-6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                <span>Absent</span>
                <strong><?= esc($summary['absent']) ?></strong>
            </div>
            <div class="summary-row">
                <svg class="summary-icon summary-icon-leave" viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14" stroke="currentColor" stroke-width="1.5"/></svg>
                <span>On Leave</span>
                <strong><?= esc($summary['on_leave']) ?></strong>
            </div>
        </div>
        <a href="<?= site_url('leave') ?>" class="btn-secondary" style="width: 100%; text-align: center; box-sizing: border-box;">Apply for Leave</a>
    </div>
</div>

<?php
    $prevMonth = $month - 1; $prevYear = $year;
    if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
    $nextMonth = $month + 1; $nextYear = $year;
    if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

    $daysInMonth  = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
    $firstWeekday = (int) date('N', mktime(0, 0, 0, $month, 1, $year)); // 1=Mon .. 7=Sun
    $isCurrentMonth = ($year === (int) date('Y') && $month === (int) date('n'));

    $statusClass = [
        'present'  => 'cal-present',
        'late'     => 'cal-late',
        'half_day' => 'cal-half',
        'absent'   => 'cal-absent',
        'on_leave' => 'cal-leave',
        'holiday'  => 'cal-holiday',
        'weekend'  => 'cal-weekend',
        'future'   => 'cal-future',
    ];
?>

<div class="page-header">
    <h2 style="margin-bottom:0;"><?= esc(date('F Y', mktime(0, 0, 0, $month, 1, $year))) ?></h2>
    <div>
        <a href="<?= site_url('attendance?year=' . $prevYear . '&month=' . $prevMonth) ?>" class="btn-secondary">&laquo; Prev</a>
        <a href="<?= site_url('attendance?year=' . $nextYear . '&month=' . $nextMonth) ?>" class="btn-secondary">Next &raquo;</a>
    </div>
</div>

<div class="calendar-grid">
    <div class="cal-head">Mon</div><div class="cal-head">Tue</div><div class="cal-head">Wed</div>
    <div class="cal-head">Thu</div><div class="cal-head">Fri</div><div class="cal-head">Sat</div><div class="cal-head">Sun</div>

    <?php for ($i = 1; $i < $firstWeekday; $i++): ?>
        <div class="cal-cell cal-empty"></div>
    <?php endfor; ?>

    <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
        <?php $entry = $dayMap[$d]; $isToday = $isCurrentMonth && $d === (int) date('j'); ?>
        <div class="cal-cell <?= esc($statusClass[$entry['status']] ?? '') ?> <?= $isToday ? 'cal-today' : '' ?>"
             <?= $entry['holiday_name'] ? 'title="' . esc($entry['holiday_name']) . '"' : '' ?>>
            <span class="cal-day-num"><?= $d ?></span>
            <?php if ($entry['status'] === 'holiday'): ?>
                <span class="cal-dot cal-dot-holiday"></span>
            <?php elseif (! in_array($entry['status'], ['weekend', 'future'], true)): ?>
                <span class="cal-dot cal-dot-<?= esc($statusClass[$entry['status']]) ?>"></span>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>

<div class="cal-legend">
    <span><span class="legend-dot cal-present"></span> Present</span>
    <span><span class="legend-dot cal-late"></span> Late</span>
    <span><span class="legend-dot cal-half"></span> Half Day</span>
    <span><span class="legend-dot cal-absent"></span> Absent</span>
    <span><span class="legend-dot cal-leave"></span> On Leave</span>
    <span><span class="legend-dot cal-holiday"></span> Holiday</span>
</div>

<h2>History</h2>
<table class="data-table">
    <thead>
        <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Login</th><th>Productive</th><th>Break</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php for ($d = $daysInMonth; $d >= 1; $d--): ?>
            <?php $entry = $dayMap[$d]; if (in_array($entry['status'], ['weekend', 'future'], true) && ! $entry['row']) { continue; } ?>
            <tr>
                <td><?= esc(date('d M', mktime(0, 0, 0, $month, $d, $year))) ?></td>
                <td><?= $entry['row'] && $entry['row']['check_in'] ? esc(date('h:i A', strtotime($entry['row']['check_in']))) : '-' ?></td>
                <td><?= $entry['row'] && $entry['row']['check_out'] ? esc(date('h:i A', strtotime($entry['row']['check_out']))) : '-' ?></td>
                <td><?= esc(format_hours($entry['hours']['login_hours'])) ?></td>
                <td><?= esc(format_hours($entry['hours']['productive_hours'])) ?></td>
                <td><?= esc(format_hours($entry['hours']['break_hours'])) ?></td>
                <td>
                    <?php if ($entry['status'] === 'holiday'): ?>
                        <span class="badge badge-on_leave">🎉 <?= esc($entry['holiday_name']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-<?= esc($entry['status']) ?>"><?= esc(format_status($entry['status'])) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
