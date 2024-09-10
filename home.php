<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
  header("Location: login.php");
  exit;
}


?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Hugo 0.84.0">
  <title>EduShed</title>

  <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">



  <!-- Bootstrap core CSS -->
  <link href="bootstrap.min.css" rel="stylesheet">

  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
  </style>


  <!-- Custom styles for this template -->
  <link href="dashboard.css" rel="stylesheet">
</head>

<body>

  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Welcome, <?php echo $_SESSION['user_name'] ?></a>
    <div class="navbar-nav">
      <div class="nav-item text-nowrap">
        <button class="nav-link px-3" onclick="window.location.href='logout.php'">Sign out</button>
      </div>
    </div>
  </header>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#">
                <span data-feather="home"></span>
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span data-feather="file"></span>
                Create TimeTable
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span data-feather="shopping-cart"></span>
                Display TimeTable
              </a>
            </li>

          </ul>
        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">For Non technical</h1>
        </div>

        <div class="container">
          <div class="row">
            <div class="col-md-6">
              <h2>Create Time Table</h2>
              <form id="timetableForm" method="post" action="submit_form.php">
                <!-- College Name -->
                <div class="mb-3">
                  <label for="college" class="form-label">College Name</label>
                  <input type="text" class="form-control" id="college" name="college" placeholder="Enter college name" required>
                </div>

                <!-- Branch Name and Semester -->
                <div class="mb-3">
                  <label for="branch" class="form-label">Branch Name</label>
                  <input type="text" class="form-control" id="branch" name="branch" placeholder="Enter branch name" required>
                </div>
                <div class="mb-3">
                  <label for="semester" class="form-label">Semester</label>
                  <input type="number" class="form-control" id="semester" name="semester" placeholder="Enter semester number" min="1" max="10" required>
                </div>

                <!-- Subject Name and Hours (Dynamic) -->
                <div id="subjectContainer" class="mb-3">
                  <label for="subject" class="form-label">Subject Name, Subject Code and Hours per Week</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name="subjects[]" placeholder="Subject name" required>
                    <input type="number" class="form-control" name="subjectcodes[]" placeholder="Subject Code" required>
                    <input type="number" class="form-control" name="subjectHours[]" placeholder="Hours per week" min="1" required>
                    <button type="button" class="btn btn-success" onclick="addSubject()">+</button>
                  </div>
                </div>

                <!-- Faculty (Dynamic) -->
                <div id="facultyContainer" class="mb-3">
                  <label for="faculty" class="form-label">Faculty and Availability</label>
                  <div id="availabilityContainer"></div>
                  <button type="button" class="btn btn-success" onclick="addFaculty()">+</button>
                </div>

                <!-- College Start and End Time -->
                <div class="mb-3">
                  <label for="start_time" class="form-label">College Start Time</label>
                  <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>
                <div class="mb-3">
                  <label for="end_time" class="form-label">College End Time</label>
                  <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>

                <!-- Break Time -->
                <div class="mb-3">
                  <label for="break_time" class="form-label">Break Time (Must be within college hours)</label>
                  <input type="time" class="form-control" id="break_time" name="break_time" required>
                </div>

                <!-- Break Duration -->
                <div class="mb-3">
                  <label for="break_duration" class="form-label">Break Duration</label>
                  <select class="form-control" id="break_duration" name="break_duration" required>
                    <option value="30">30 minutes</option>
                    <option value="60">1 hour</option>
                  </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
<script>
  // Function to dynamically add subject fields
  function addSubject() {
    const subjectContainer = document.getElementById('subjectContainer');
    const newField = document.createElement('div');
    newField.classList.add('input-group', 'mb-3');
    newField.innerHTML = `
      <input type="text" class="form-control" name="subjects[]" placeholder="Subject name" required>
      <input type="text" class="form-control" name="subjectcodes[]" placeholder="Subject Code" required>
      <input type="number" class="form-control" name="subjectHours[]" placeholder="Hours per week" min="1" required>
      <button type="button" class="btn btn-danger" onclick="removeFieldF(this)">-</button>
    `;
    subjectContainer.appendChild(newField);
  }

  // Function to dynamically add faculty and availability fields
  let facultyCounter = 0;

  // Function to dynamically add faculty fields
  function addFaculty() {
    const availabilityContainer = document.getElementById('availabilityContainer');

    // Use the counter as the facultyId
    const facultyId = `faculty-${facultyCounter}`;

    // Increment the counter for the next faculty
    facultyCounter++;

    // Create faculty input and availability checkboxes
    const newFacultyField = document.createElement('div');
    newFacultyField.classList.add('mb-3');
    newFacultyField.innerHTML = `
    <div class="input-group mb-3">
      <input type="text" class="form-control" name="faculties[]" placeholder="Faculty name" required>
      <input type="text" class="form-control" name="codes[]" placeholder="Subject Code" required>
      <button type="button" class="btn btn-danger" onclick="removeField(this)">-</button>
    </div>
    <div class="d-flex flex-wrap mb-3" id="${facultyId}">
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Mon">
        <label class="form-check-label">Mon</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Tue">
        <label class="form-check-label">Tue</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Wed">
        <label class="form-check-label">Wed</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Thu">
        <label class="form-check-label">Thu</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Fri">
        <label class="form-check-label">Fri</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Sat">
        <label class="form-check-label">Sat</label>
      </div>
    </div>
  `;
    availabilityContainer.appendChild(newFacultyField);
  }
  // Function to remove dynamically added fields
  function removeField(button) {
    button.parentElement.parentElement.remove();
  }

  function removeFieldF(button) {
    button.parentElement.remove();
  }
  // Form validation
  document.getElementById('timetableForm').addEventListener('submit', function(e) {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const breakTime = document.getElementById('break_time').value;
    const breakDuration = document.getElementById('break_duration').value;

    // Convert time strings to Date objects for comparison
    const start = new Date(`1970-01-01T${startTime}:00`);
    const end = new Date(`1970-01-01T${endTime}:00`);
    const breakStart = new Date(`1970-01-01T${breakTime}:00`);
    const breakEnd = new Date(breakStart.getTime() + breakDuration * 60 * 1000);

    // Check if break time is between start and end time
    if (breakStart <= start || breakEnd >= end) {
      alert("Break time must be between the college start and end time.");
      e.preventDefault(); // Prevent form submission
      return;
    }

    // If break is not exactly 30 minutes or 1 hour, show an error
    if (breakDuration !== "30" && breakDuration !== "60") {
      alert("Break duration must be either 30 minutes or 1 hour.");
      e.preventDefault(); // Prevent form submission
      return;
    }
  });
</script>
<script src="bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script>
<script src="dashboard.js"></script>
</body>

</html>