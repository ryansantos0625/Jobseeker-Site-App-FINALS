<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login/login.php');
    exit;
}

// Fetch job postings
$stmt = $pdo->query("SELECT * FROM job_postings");
$job_postings = $stmt->fetchAll();

if (isset($_POST['submit'])) {
    include 'db.php';

    // Get logged-in user's username
    $username = $_SESSION['username'];  // Assuming the username is stored in the session as user_id

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $cover_letter = $_POST['cover_letter'];
    $job_title = $_POST['job_title'];

    // Handle file upload for resume
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileSize = $_FILES['resume']['size'];
        $fileType = $_FILES['resume']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = 'uploads/';
            $destPath = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert application including the username
                $stmt = $pdo->prepare("INSERT INTO job_applications (firstname, lastname, email, phone, cover_letter, job_title, resume, username) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstname, $lastname, $email, $phone, $cover_letter, $job_title, $fileName, $username]);
            } else {
                $error_message = "There was an error moving the uploaded file.";
            }
        } else {
            $error_message = "Invalid file type. Only PDF files are allowed.";
        }
    } else {
        $error_message = "Error in file upload.";
    }

    header('Location: /appli/ty.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $message = $_POST['message'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("INSERT INTO messages (username, email, message) VALUES (:username, :email, :message)");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'message' => $message,
    ]);

    header('Location: index.php');
    exit;
}

$country_codes = [
    "+93" => "Afghanistan",
    "+355" => "Albania",
    "+213" => "Algeria",
    "+61" => "Australia",
    "+1" => "Canada",
    "+86" => "China",
    "+91" => "India",
    "+44" => "United Kingdom",
    "+1" => "United States",
    "+97" => "Saudi Arabia",
    "+880" => "Bangladesh",
    "+975" => "Bhutan",
    "+60" => "Brunei",
    "+975" => "Bhutan",
    "+91" => "India",
    "+62" => "Indonesia",
    "+98" => "Iran",
    "+964" => "Iraq",
    "+81" => "Japan",
    "+962" => "Jordan",
    "+855" => "Cambodia",
    "+254" => "Kenya",
    "+961" => "Lebanon",
    "+965" => "Kuwait",
    "+996" => "Kyrgyzstan",
    "+84" => "Vietnam",
    "+856" => "Laos",
    "+965" => "Kuwait",
    "+971" => "United Arab Emirates",
    "+976" => "Mongolia",
    "+977" => "Nepal",
    "+92" => "Pakistan",
    "+63" => "Philippines",
    "+974" => "Qatar",
    "+7" => "Kazakhstan",
    "+966" => "Saudi Arabia",
    "+82" => "South Korea",
    "+94" => "Sri Lanka",
    "+66" => "Thailand",
    "+256" => "Uganda",
    "+852" => "Hong Kong",
    "+886" => "Taiwan",
    "+90" => "Turkey",
    "+967" => "Yemen"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/logorss.png" type="image/x-icon">
    <title>FindHire | Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="#" data-bs-toggle="tooltip" title="Dashboard">
            <span class="find-hire-text">𝙵𝚒𝚗𝚍𝙷𝚒𝚛𝚎</span>
        </a>

        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto align-items-center">

                <!-- View Applications -->
                <li class="nav-item">
                    <a class="nav-link" href="viewapp.php" data-bs-toggle="tooltip" title="View Your Application">
                        <i class="fa-solid fa-street-view"></i>
                    </a>
                </li>

                <!-- Support Ticket -->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#chatModal" title="Support Ticket">
                        <i class="fa fa-ticket" aria-hidden="true"></i>
                    </a>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="#">Profile</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/login/logout.php">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Job Title</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($job_postings as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($job['job_description']); ?></td>
                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                        <td><?php echo htmlspecialchars($job['salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['last_updated']); ?></td>
                        <td>
                            <a href="#" 
                               class="btn btn-outline-info btn-sm" 
                               title="Apply Job" 
                               data-bs-toggle="modal" 
                               data-bs-target="#applicationModal" 
                               onclick="setJobTitle('<?php echo htmlspecialchars($job['job_title']); ?>')">
                               Apply
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Support Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Use modal-lg for larger screens -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Chat Support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="row mb-3">
                        <!-- User ID (Hidden) -->
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">

                        <!-- Email Input -->
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <!-- Message Input -->
                        <div class="col-12 col-md-6">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

  <!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> 
            <div class="modal-body text-center">
                <?php
                $profilePicture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'https://via.placeholder.com/100';
                ?>
                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="rounded-circle mb-3">
                <h3 class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <div class="mt-3">
                    <a href="/login/logout.php" class="btn btn-danger me-2" title="Logout">
                        <i class="fa fa-sign-out" aria-hidden="true"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Job Application Form -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="applicationModalLabel">Job Application</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Job Application Form -->
                <form action=" " method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="job_title" name="job_title">
                    
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <select class="form-select" name="country_code" required>
                                <option value="">Select Country Code</option>
                                <?php foreach ($country_codes as $code => $country): ?>
                                    <option value="<?php echo htmlspecialchars($code); ?>">
                                        <?php echo htmlspecialchars($code . " (" . $country . ")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="form-control" name="phone" placeholder="1234567890" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cover_letter" class="form-label">Cover Letter</label>
                        <textarea class="form-control" name="cover_letter" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resume" class="form-label">Resume (PDF Only)</label>
                        <input type="file" class="form-control" name="resume" accept=".pdf" required>
                    </div>
                    
                    <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            if (title.includes(searchQuery)) {
                card.parentElement.style.display = '';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });
</script>

<script>
    function setJobTitle(jobTitle) {
        document.getElementById('job_title').value = jobTitle;
    }

    function setJobTitle(jobTitle, jobLocation) {
        document.getElementById('job_title').value = jobTitle;
        // Optionally, you can also set the location field if needed
        document.getElementById('job_location').value = jobLocation;
    }
</script>

<script>
  // Initialize tooltips for elements with data-bs-toggle="tooltip"
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  })
</script>

<script>
    // Popover content (using HTML)
    const popoverContent = `
      <div class="text-center">
        <img src="" alt="Profile Picture" class="rounded-circle mb-2">
        <h6 class="fw-bold mb-1">John Doe</h6>
        <p class="mb-1">johndoe@example.com</p>
        <p class="mb-1">+123 456 7890</p>
        <div>
          <a href="https://facebook.com" target="_blank" class="me-2"><i class="bi bi-facebook"></i></a>
          <a href="https://instagram.com" target="_blank" class="me-2"><i class="bi bi-instagram"></i></a>
          <a href="https://github.com" target="_blank"><i class="bi bi-github"></i></a>
        </div>
      </div>
    `;

    // Initialize Popover
    const profilePopover = document.getElementById('profilePopover');
    new bootstrap.Popover(profilePopover, {
      content: popoverContent,
      html: true,
      placement: 'bottom', // Position the popover below the button
      trigger: 'click' // Show popover on click
    });
  </script>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
