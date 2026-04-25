<?php
// index.php
session_start();
require_once 'db.php';
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $patient_name = trim($_POST['patient_name']);
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $department = $_POST['department'];
    $symptoms = trim($_POST['symptoms']);
    
    $errors = [];
    
    // Validation
    if (empty($patient_name)) $errors[] = "Patient name is required";
    if ($age < 0 || $age > 120) $errors[] = "Valid age is required (0-120)";
    if (empty($gender)) $errors[] = "Gender is required";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Valid 10-digit phone number is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($appointment_date) || strtotime($appointment_date) < strtotime(date('Y-m-d'))) $errors[] = "Valid future date is required";
    if (empty($appointment_time)) $errors[] = "Appointment time is required";
    if (empty($department)) $errors[] = "Department selection is required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_name, age, gender, phone, email, appointment_date, appointment_time, department, symptoms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$patient_name, $age, $gender, $phone, $email, $appointment_date, $appointment_time, $department, $symptoms]);
            $success_message = "Appointment booked successfully! We'll contact you shortly.";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error_message = "This time slot is already booked. Please choose another date or time.";
            } else {
                $error_message = "Booking failed: " . $e->getMessage();
            }
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>First Smile Dental</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-tooth"></i>
                  <span>
                        First Smile <span style="color: #66BB66;">Dental</span>
                  </span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#doctor" class="nav-link">About Doctor</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#appointment" class="nav-link">Appointment</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
                <li><a href="admin_login.php" class="nav-link admin-link"><i class="fas fa-user-shield"></i> Admin</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <main>
        <section id="home" class="hero">
            <div class="hero-container">
                <div class="hero-content">
                    <h1>Your Health, Our Priority</h1>
                    <p>Experience world-class dental & medical care with Dr. B Das and expert team. Advanced treatments with compassionate approach.</p>
                    <a href="#appointment" class="btn-primary"><i class="fas fa-calendar-check"></i> Book Appointment</a>
                </div>
                <div class="hero-image">
                    <img src="images/1.jpeg" alt="Doctor with patient">
                </div>
            </div>
        </section>

        <section id="doctor" class="doctor-section">
            <div class="container">
                <div class="section-header">
                    <h2>Meet Our Leading <span class="highlight">Doctor</span></h2>
                    <p>Expert care with years of excellence</p>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img">
                        <img src="images/2.jpeg" alt="Dr. B Das">
                    </div>
                    <div class="doctor-details">
                        <h3>Dr. B Das</h3>
                        <p class="specialization"><i class="fas fa-stethoscope"></i> Oral and Dental Surgeon</p>
                        <div class="doctor-info-grid">
                            <div><i class="fas fa-graduation-cap"></i> <strong>Qualifications:</strong> BDS (Kol), WBUHS (Kol)</div>
                            <div><i class="fas fa-id-card"></i> <strong>Reg. No.:</strong> 3235(A)</div>
                            <div><i class="fas fa-building"></i> <strong>Affiliated:</strong> Dr. R. Ahmed Dental College & Hospital</div>
                            <div><i class="fas fa-clock"></i> <strong>Experience:</strong> 12+ years specialized practice</div>
                        </div>
                        <div class="attached-clinics">
                            <h4><i class="fas fa-hospital-user"></i> Attached With</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> OLIVA NURSING HOME & DIAGNOSTIC CENTER, Dum Dum (NABH Accredited)</li>
                                <li><i class="fas fa-check-circle"></i> HEALTH CARE DIAGNOSTIC CENTER, Santipur</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="services-section">
            <div class="container">
                <div class="section-header">
                    <h2>Our <span class="highlight">Services</span></h2>
                    <p>Comprehensive medical care under one roof</p>
                </div>
                <div class="services-grid">
                    <div class="service-card">
                        <i class="fas fa-stethoscope"></i>
                        <h3>General Checkup</h3>
                        <p>Complete health screening, vitals check, and preventive care consultations.</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Cardiology</h3>
                        <p>Heart health assessments, ECG, stress tests, and expert cardiac advice.</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-tooth"></i>
                        <h3>Dental Care</h3>
                        <p>Root canals, scaling, cosmetic dentistry, implants, and emergency dental.</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-baby-carriage"></i>
                        <h3>Child Care</h3>
                        <p>Pediatric checkups, vaccinations, growth monitoring & child specialist.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="appointment" class="appointment-section">
            <div class="container">
                <div class="section-header">
                    <h2>Book Your <span class="highlight">Appointment</span></h2>
                    <p>Fill the form below and our team will confirm your slot</p>
                </div>
                <?php if ($success_message): ?>
                    <div class="alert success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form id="appointmentForm" method="POST" action="" class="appointment-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name *</label>
                            <input type="text" name="patient_name" id="patient_name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Age *</label>
                            <input type="number" name="age" id="age" min="0" max="120" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Gender *</label>
                            <select name="gender" id="gender" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone-alt"></i> Phone Number *</label>
                            <input type="tel" name="phone" id="phone" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email *</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day"></i> Appointment Date *</label>
                            <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Appointment Time *</label>
                            <input type="time" name="appointment_time" id="appointment_time" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-building"></i> Department *</label>
                            <select name="department" id="department" required>
                                <option value="">Select Department</option>
                                <option value="Dental">Dental Care</option>
                                <option value="General Medicine">General Medicine</option>
                                <option value="Cardiology">Cardiology</option>
                                <option value="Pediatrics">Pediatrics</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-notes-medical"></i> Message / Symptoms</label>
                        <textarea name="symptoms" rows="3" placeholder="Describe your symptoms or special requests..."></textarea>
                    </div>
                    <button type="submit" name="book_appointment" class="btn-submit"><i class="fas fa-check-circle"></i> Confirm Booking</button>
                </form>
            </div>
        </section>

        <section id="contact" class="contact-section">
            <div class="container">
                <div class="section-header">
                    <h2>Get In <span class="highlight">Touch</span></h2>
                    <p>Visit us or reach out for emergency assistance</p>
                </div>
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="info-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <h3>Clinic Address</h3>
                            <p>OLIVA NURSING HOME & DIAGNOSTIC CENTER,<br> Dum Dum, Kolkata - 700030</p>
                            <p><strong>Also at:</strong> HEALTH CARE DIAGNOSTIC CENTER, Santipur</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-phone-alt"></i>
                            <h3>Contact Numbers</h3>
                            <p><strong>Appointment:</strong> 8927705797</p>
                            <p><strong>Emergency:</strong> 8900453933</p>
                            <p><strong>Query:</strong> 8927705797</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-envelope"></i>
                            <h3>Email</h3>
                            <p>care@healthcareplus.com</p>
                            <p>drbdas@clinic.com</p>
                        </div>
                    </div>
                    <div class="map-container">
                        <iframe src="https://maps.google.com/maps?q=Dum%20Dum%20Kolkata&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 HealthCarePlus | Dr. B Das Dental & Medical Clinic | All Rights Reserved</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) target.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>