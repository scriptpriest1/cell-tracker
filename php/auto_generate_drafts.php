<?php
// Simple script to auto-generate report drafts for all cells.
// Intended to be run by cron (weekly, on Monday at 00:00) or manually for testing.
//
// Usage (CLI):
//   php auto_generate_drafts.php            # uses today's date
//   php auto_generate_drafts.php 2025-08-11 # use specific date (Mon)
//
// Cron example (run at 00:00 on Mondays):
// 0 0 * * 1 /usr/bin/php /path/to/htdocs/cell-tracker/php/auto_generate_drafts.php >> /var/log/cell-tracker/auto_generate.log 2>&1
//
// Security note: if exposing to web, protect with authentication or run only from CLI.

if (php_sapi_name() !== 'cli') {
  // web invocation: optional basic protection (token) could be added here if needed.
}

// Load environment
require_once __DIR__ . '/connect_db.php';
require_once __DIR__ . '/functions.php';

$dateArg = null;
if (php_sapi_name() === 'cli') {
  global $argv;
  $dateArg = isset($argv[1]) ? $argv[1] : null;
} else {
  $dateArg = isset($_GET['date']) ? $_GET['date'] : null;
}

$targetDate = $dateArg ?: date('Y-m-d');
try {
  $reportDate = new DateTime($targetDate);
} catch (Exception $e) {
  $msg = "Invalid date: {$targetDate}\n";
  if (php_sapi_name() === 'cli') echo $msg;
  else echo $msg;
  exit(1);
}

// compute week based on first Monday approach
$computeWeek = function (DateTime $d) {
  $year = (int)$d->format('Y');
  $month = (int)$d->format('m');
  $day = (int)$d->format('j');

  $firstOfMonth = new DateTime("$year-$month-01 00:00:00");
  $dow = (int)$firstOfMonth->format('N'); // 1=Mon
  $firstMonday = clone $firstOfMonth;
  if ($dow !== 1) $firstMonday->modify('next Monday');
  $firstMondayDay = (int)$firstMonday->format('j');
  if ($day < $firstMondayDay) return 0;
  return 1 + floor(($day - $firstMondayDay) / 7);
};

$week = $computeWeek($reportDate);
if ($week < 1 || $week > 5) {
  $out = "Not a reporting week for date {$reportDate->format('Y-m-d')}. Nothing generated.\n";
  if (php_sapi_name() === 'cli') echo $out; else echo nl2br($out);
  exit(0);
}

// Compute draftMonday & expiry once and reuse (Monday 00:00:00)
$year = (int)$reportDate->format('Y');
$month = (int)$reportDate->format('m');
$firstOfMonth = new DateTime("$year-$month-01 00:00:00");
$dow = (int)$firstOfMonth->format('N');
$firstMonday = clone $firstOfMonth;
if ($dow !== 1) $firstMonday->modify('next Monday');
$draftMonday = clone $firstMonday;
$draftMonday->modify('+' . ($week - 1) * 7 . ' days');
$draftMonday->setTime(0,0,0);

$expiry = clone $draftMonday;
$expiry->modify('next Sunday');
$expiry->setTime(23,59,59);

$draftMonth0 = (int)$draftMonday->format('m');
$draftYear0  = (int)$draftMonday->format('Y');

$cellsStmt = $conn->prepare("SELECT id FROM cells");
$cellsStmt->execute();
$cells = $cellsStmt->fetchAll(PDO::FETCH_ASSOC);

$generated = 0;
$skipped = 0;
$errors = [];

foreach ($cells as $c) {
  $cellId = $c['id'];

  $check = $conn->prepare("
    SELECT COUNT(*) FROM cell_report_drafts 
    WHERE cell_id = ? AND week = ? 
      AND MONTH(date_generated) = ? AND YEAR(date_generated) = ?
  ");
  $check->execute([
    $cellId,
    $week,
    $draftMonth0,
    $draftYear0
  ]);
  $exists = (int)$check->fetchColumn();
  if ($exists > 0) {
    $skipped++;
    continue;
  }

  $description = getMeetingDescription($week);
  $type = getReportTypeByWeek($week);

  try {
    $ins = $conn->prepare("
      INSERT INTO cell_report_drafts (type, week, description, status, date_generated, expiry_date, cell_id)
      VALUES (?, ?, ?, 'pending', ?, ?, ?)
    ");
    $ins->execute([
      $type,
      $week,
      $description,
      $draftMonday->format('Y-m-d H:i:s'),
      $expiry->format('Y-m-d H:i:s'),
      $cellId
    ]);
    $generated++;
  } catch (PDOException $ex) {
    error_log("auto_generate_drafts.php insert error for cell {$cellId}: " . $ex->getMessage());
    $errors[] = "Cell {$cellId}: DB error";
  }
}

$out = "Auto-generate summary for date {$reportDate->format('Y-m-d')}:\nGenerated: {$generated}\nSkipped (already existed): {$skipped}\n";
if (!empty($errors)) $out .= "Errors:\n" . implode("\n", $errors) . "\n";

if (php_sapi_name() === 'cli') echo $out;
else echo nl2br(htmlspecialchars($out));
exit(0);
    error_log("auto_generate_drafts.php insert error for cell {$cellId}: " . $ex->getMessage());
    $errors[] = "Cell {$cellId}: DB error";
  }
}

$out = "Auto-generate summary for date {$reportDate->format('Y-m-d')}:\nGenerated: {$generated}\nSkipped (already existed): {$skipped}\n";
if (!empty($errors)) $out .= "Errors:\n" . implode("\n", $errors) . "\n";

if (php_sapi_name() === 'cli') echo $out;
else echo nl2br(htmlspecialchars($out));
exit(0);
