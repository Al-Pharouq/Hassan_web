<?php
// contact_us.php

// Start session if needed (for CSRF tokens or other purposes)
// session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Replace with your reCAPTCHA secret key
    $secretKey = '6Le20fEqAAAAAIi9feOv2dXeTqUcQB09VngQzh-o';
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Verify reCAPTCHA response
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secretKey) . "&response=" . urlencode($captchaResponse);
    $response = file_get_contents($url);
    $responseData = json_decode($response);

    $errors = [];

    if (!$responseData->success) {
        $errors[] = "Captcha verification failed. Please try again.";
    } else {
        // Sanitize and validate inputs
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $enquiry = trim($_POST['enquiry'] ?? '');

        if (empty($name)) {
            $errors[] = "Name is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid email is required.";
        }
        if (empty($enquiry)) {
            $errors[] = "Enquiry is required.";
        }

        if (empty($errors)) {
            // Sanitize inputs further before email composition
            $safeName    = filter_var($name, FILTER_SANITIZE_STRING);
            $safeEmail   = filter_var($email, FILTER_SANITIZE_EMAIL);
            $safeEnquiry = filter_var($enquiry, FILTER_SANITIZE_STRING);

            // Compose email
            $to      = 'admin@hassaan.net';
            $subject = 'New Contact Us Message';
            $message  = "Name: " . $safeName . "\n";
            $message .= "Email: " . $safeEmail . "\n";
            $message .= "Enquiry:\n" . $safeEnquiry . "\n";

            $headers  = "From: " . $safeEmail . "\r\n";
            $headers .= "Reply-To: " . $safeEmail . "\r\n";

            // Send the email
            if (mail($to, $subject, $message, $headers)) {
                $success_message = "Your enquiry has been sent successfully.";
            } else {
                $errors[] = "There was an error sending your enquiry. Please try again later.";
            }
        }
    }
}
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>


  <div class="container mt-5">
    <h2>Contact Us</h2>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
          <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
      <div class="alert alert-success">
        <p><?php echo htmlspecialchars($success_message); ?></p>
      </div>
    <?php else: ?>
      <form action="contact_us.php" method="post" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label">Name:</label>
          <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="enquiry" class="form-label">Enquiry:</label>
          <textarea name="enquiry" id="enquiry" class="form-control" rows="5" required></textarea>
        </div>

        <!-- Google reCAPTCHA widget -->
        <div class="mb-3">
          <div class="g-recaptcha" data-sitekey="6Le20fEqAAAAAPysuORb6aP3C1PsJ8O3qsgIqKsh"></div>
        </div>

        <button type="submit" class="custom-btn mb-4">Submit</button>
      </form>
    <?php endif; ?>
  </div>
<?php
require_once 'includes/footer.php';
