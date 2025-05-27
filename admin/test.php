<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Multiselect Dropdown</title>
  <link rel="stylesheet" href="multiselect.css">
</head>
<body>

<div class="multiselect-container">
  <div class="select-box" id="selectBox">Odaberi opcije ▼</div>
  <div class="checkbox-list" id="checkboxList">
    <!-- Checkbox opcije se dinamički popunjavaju iz PHP-a -->
    <!-- Primeri: -->
    <label><input type="checkbox" name="options[]" value="opcija1"> Opcija 1</label>
    <label><input type="checkbox" name="options[]" value="opcija2"> Opcija 2</label>
    <label><input type="checkbox" name="options[]" value="opcija3"> Opcija 3</label>
    <label><input type="checkbox" name="options[]" value="opcija4"> Opcija 4</label>
  </div>
</div>

<script src="multiselect.js"></script>
</body>
</html>
