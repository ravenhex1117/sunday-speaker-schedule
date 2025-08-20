<?php
$title = "Sunday Speaker Scheduler";
include 'header.php';
?>

<div class="container">
  <h1>🎙️ Sunday Speaker Scheduler</h1>
  <p>Generate a speaker list for every Sunday in a date range.</p>

  <form action="generate.php" method="POST" enctype="multipart/form-data">

    <!-- Speakers Input -->
    <div class="section">
      <h3>👥 Enter Speaker Names</h3>
      <label>
        <input type="radio" name="input_method" value="text" checked> Type manually
      </label>
      <textarea name="names_text" placeholder="Enter names separated by commas: John, Sarah, David" rows="3" style="width:100%;"></textarea>

      <br><br>
      <label>
        <input type="radio" name="input_method" value="csv"> Upload CSV file
      </label>
      <input type="file" name="names_csv" accept=".csv">
      <p><small>CSV should have one column of names, no header.</small></p>
    </div>

    <!-- Date Range -->
    <div class="section">
      <h3>📅 Select Date Range</h3>
      <div class="row">
        <div class="col">
          <label>Start Month & Year</label>
          <select name="start_month" required>
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
          </select>
          <select name="start_year" required>
            <?php for ($y = 2020; $y <= 2030; $y++): ?>
              <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col">
          <label>End Month & Year</label>
          <select name="end_month" required>
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= $m ?>" <?= $m == 6 ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
          </select>
          <select name="end_year" required>
            <?php for ($y = 2020; $y <= 2030; $y++): ?>
              <option value="<?= $y ?>" <?= $y == date('Y') + 1 ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Exclude Dates -->
    <div class="section">
      <h3>🚫 Exclude Specific Sundays</h3>
      <p>Select Sundays you want to skip (e.g., holidays):</p>
      <div id="exclude-dates-container">
        <!-- Will be populated dynamically with JavaScript -->
      </div>
      <button type="button" onclick="addExcludeDate()">➕ Add Another Date</button>
    </div>

    <!-- Submit -->
    <div class="section">
      <button type="submit">Generate Schedule & Download Excel</button>
    </div>

  </form>
</div>

<script>
function addExcludeDate() {
  const container = document.getElementById('exclude-dates-container');
  const input = document.createElement('input');
  input.type = 'date';
  input.name = 'exclude_dates[]';
  input.style.margin = '5px 0';
  container.appendChild(input);
}
</script>

<?php include 'footer.php'; ?>