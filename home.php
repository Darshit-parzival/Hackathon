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
              <form id="timetableForm">
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

                <!-- Subjects and Total Hours (Dynamic) -->
                <div id="subjectContainer" class="mb-3">
                  <label for="subject" class="form-label">Subjects and Hours</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name="subjects[]" placeholder="Subject name" required>
                    <input type="number" class="form-control" name="hours[]" placeholder="Total hours" required>
                    <button type="button" class="btn btn-success" onclick="addSubject()">+</button>
                  </div>
                </div>

                <!-- Faculty (Dynamic) -->
                <div id="facultyContainer" class="mb-3">
                  <label for="faculty" class="form-label">Faculty and Availability</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name="faculties[]" placeholder="Faculty name" required>
                    <button type="button" class="btn btn-success" onclick="addFaculty()">+</button>
                  </div>
                  <div id="availabilityContainer"></div>
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
                  <label for="break_time" class="form-label">Break Time</label>
                  <input type="time" class="form-control" id="break_time" name="break_time" required>
                </div>

                <!-- Working Days -->
                <div class="mb-3">
                  <label for="working_days" class="form-label">Working Days</label>
                  <select class="form-control" id="working_days" name="working_days" required>
                    <option value="Mon to Fri">Mon to Fri</option>
                    <option value="Mon to Sat">Mon to Sat</option>
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
  <script>
    // Function to add new subject and total hours fields
    function addSubject() {
      const subjectContainer = document.getElementById('subjectContainer');
      const newField = document.createElement('div');
      newField.classList.add('input-group', 'mb-3');
      newField.innerHTML = `
      <input type="text" class="form-control" name="subjects[]" placeholder="Subject name" required>
      <input type="number" class="form-control" name="hours[]" placeholder="Total hours" required>
      <button type="button" class="btn btn-danger" onclick="removeField(this)">-</button>
    `;
      subjectContainer.appendChild(newField);
    }

    // Function to add new faculty fields with availability
    function addFaculty() {
      const facultyContainer = document.getElementById('facultyContainer');
      const availabilityContainer = document.getElementById('availabilityContainer');
      const facultyId = `faculty-${Date.now()}`;

      // Faculty Input
      const newFacultyField = document.createElement('div');
      newFacultyField.classList.add('input-group', 'mb-3');
      newFacultyField.innerHTML = `
      <input type="text" class="form-control" name="faculties[]" placeholder="Faculty name" required>
      <button type="button" class="btn btn-danger" onclick="removeField(this)">-</button>
    `;
      facultyContainer.appendChild(newFacultyField);

      // Availability Input
      const newAvailabilityField = document.createElement('div');
      newAvailabilityField.classList.add('mb-3');
      newAvailabilityField.innerHTML = `
      <label for="${facultyId}" class="form-label">Faculty Availability for ${newFacultyField.querySelector('input').value}</label>
      <div class="d-flex flex-wrap" id="${facultyId}">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Mon" id="dayMon-${facultyId}">
          <label class="form-check-label" for="dayMon-${facultyId}">Mon</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Tue" id="dayTue-${facultyId}">
          <label class="form-check-label" for="dayTue-${facultyId}">Tue</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Wed" id="dayWed-${facultyId}">
          <label class="form-check-label" for="dayWed-${facultyId}">Wed</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Thu" id="dayThu-${facultyId}">
          <label class="form-check-label" for="dayThu-${facultyId}">Thu</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Fri" id="dayFri-${facultyId}">
          <label class="form-check-label" for="dayFri-${facultyId}">Fri</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="availability_${facultyId}[]" value="Sat" id="daySat-${facultyId}">
          <label class="form-check-label" for="daySat-${facultyId}">Sat</label>
        </div>
      </div>
    `;
      availabilityContainer.appendChild(newAvailabilityField);
    }

    // Function to remove fields
    function removeField(button) {
      button.parentElement.remove();
    }

    // Form validation
    document.getElementById('timetableForm').addEventListener('submit', function(e) {
      const startTime = document.getElementById('start_time').value;
      const endTime = document.getElementById('end_time').value;
      const breakTime = document.getElementById('break_time').value;

      // Convert time strings to Date objects for comparison
      const start = new Date(`1970-01-01T${startTime}:00`);
      const end = new Date(`1970-01-01T${endTime}:00`);
      const breakT = new Date(`1970-01-01T${breakTime}:00`);

      // Check if break time is between start and end time
      if (breakT <= start || breakT >= end) {
        alert("Break time must be between the start and end time of the college.");
        e.preventDefault(); // Prevent form submission
        return;
      }

      // Check if break time is 30 min or 1 hour after start time
      const oneHourLater = new Date(start.getTime() + 60 * 60 * 1000); // 1 hour later
      const thirtyMinLater = new Date(start.getTime() + 30 * 60 * 1000); // 30 minutes later

      if (breakT < thirtyMinLater || (breakT > oneHourLater && breakT.getTime() !== oneHourLater.getTime())) {
        alert("Break time should be 30 minutes or 1 hour after the college start time.");
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