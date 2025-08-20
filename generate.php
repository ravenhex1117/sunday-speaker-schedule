<?php
$title = "Generated Schedule";
include 'header.php';

// --- Load Speakers ---
$speakers = [];

if ($_POST['input_method'] === 'text' && !empty($_POST['names_text'])) {
    $names = explode(',', $_POST['names_text']);
    foreach ($names as $name) {
        $clean = trim($name);
        if ($clean) $speakers[] = $clean;
    }
} elseif ($_POST['input_method'] === 'csv' && isset($_FILES['names_csv']) && $_FILES['names_csv']['error'] == 0) {
    $file = $_FILES['names_csv']['tmp_name'];
    $handle = fopen($file, 'r');
    while (($data = fgetcsv($handle, 1000)) !== false) {
        $name = trim($data[0]);
        if ($name) $speakers[] = $name;
    }
    fclose($handle);
}

if (empty($speakers)) {
    die("<p style='color:red;'>❌ No speaker names provided.</p><a href='index.php'>Go back</a>");
}

// --- Get Date Range ---
$start_month = (int)$_POST['start_month'];
$start_year = (int)$_POST['start_year'];
$end_month = (int)$_POST['end_month'];
$end_year = (int)$_POST['end_year'];

$start = DateTime::createFromFormat('Y-m-d', "$start_year-$start_month-01");
$end = DateTime::createFromFormat('Y-m-t', "$end_year-$end_month-01"); // Last day of month

// --- Generate Sundays ---
$sundays = [];
$current = clone $start;
while ($current->format('w') != 0) { // 0 = Sunday
    $current->modify('+1 day');
}
while ($current <= $end) {
    $sundays[] = clone $current;
    $current->modify('+7 days');
}

// --- Exclude Dates ---
$excluded = [];
if (!empty($_POST['exclude_dates'])) {
    foreach ($_POST['exclude_dates'] as $date) {
        $excluded[] = DateTime::createFromFormat('Y-m-d', $date)->format('Y-m-d');
    }
}

// Filter out excluded Sundays
$final_sundays = array_filter($sundays, function($sunday) use ($excluded) {
    return !in_array($sunday->format('Y-m-d'), $excluded);
});

if (empty($final_sundays)) {
    echo "<p>🚫 No Sundays available after exclusions.</p>";
    echo "<a href='index.php'>← Go back</a>";
    include 'footer.php';
    exit;
}

// --- Generate Schedule (3 speakers per Sunday) ---
$schedule = [];
$previous_week_speakers = [];

foreach ($final_sundays as $sunday) {
    $date_str = $sunday->format('Y-m-d');
    $formatted_date = $sunday->format('l, F j, Y');

    // Avoid repeating speakers from last week
    $available = array_diff($speakers, $previous_week_speakers);
    if (count($available) < 3) {
        $available = $speakers; // fallback
    }

    $keys = array_rand($available, 3);
    $selected = [];
    foreach ($keys as $k) {
        $selected[] = $available[$k];
    }

    $previous_week_speakers = $selected;

    $schedule[] = [
        'Date' => $formatted_date,
        'Speaker 1' => $selected[0],
        'Speaker 2' => $selected[1],
        'Speaker 3' => $selected[2]
    ];
}

// --- Export to Excel (XLS) ---
$filename = "Speaker_Schedule_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th>Date</th><th>Speaker 1</th><th>Speaker 2</th><th>Speaker 3</th></tr>";
foreach ($schedule as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Speaker 1']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Speaker 2']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Speaker 3']) . "</td>";
    echo "</tr>";
}
echo "</table>";

include 'footer.php';
?>