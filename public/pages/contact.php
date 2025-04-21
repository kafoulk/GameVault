<?php
require '../../vendor/autoload.php';
include '../../includes/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Create a new PHPMailer instance
    $phpmailer = new PHPMailer(true);

    try {
        // Set mailer to use SMTP
        $phpmailer->isSMTP();
        $phpmailer->Host       = 'sandbox.smtp.mailtrap.io'; // Mailtrap SMTP server
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = 2525; // Mailtrap SMTP port
        $phpmailer->Username   = '4bb5eb8dfca858'; // Your Mailtrap username
        $phpmailer->Password   = '27117708b82677'; // Your Mailtrap password

        // Recipients
        $phpmailer->setFrom('your@email.com', 'Your Name');
        $phpmailer->addAddress('kafoulk@iu.edu'); // Or any test recipient email

        // Email content
        $phpmailer->isHTML(true);
        $phpmailer->Subject = htmlspecialchars($_POST['subject']);
        $phpmailer->Body    = htmlspecialchars($_POST['message']);
        $phpmailer->AltBody = strip_tags($_POST['message']); // Plain text version

        // Send the email
        $phpmailer->send();
        $success = true;
    } catch (Exception $e) {
        $error = true;
    }
}
?>


<main>
    <!-- Contact Form and Map -->
    <div class="container my-5">
        <h2 class="mb-4 text-center">Contact Us</h2>

        <!-- Success or Error Message -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center">
                Your message has been sent successfully!
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger text-center">
                There was a problem sending your message. Please try again later.
            </div>
        <?php endif; ?>

        <!-- Contact Row -->
        <div class="row contact_row">
            <!-- Google Map -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="h-100 w-100" style="min-height: 100%; border-radius: 10px; overflow: hidden;">
                    <iframe class="map"
                            src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d24530.66632023649!2d-86.1765632!3d39.7770752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1744858512968!5m2!1sen!2sus"
                            width="100%"
                            height="100%"
                            style="border:0; min-height: 400px;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-6 form">
                <form id="contactForm" action="" method="POST" class="row g-3 needs-validation" novalidate>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="johndoe@email.com" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="col-12">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Email title here" required>
                        <div class="invalid-feedback">Please enter a subject.</div>
                    </div>

                    <div class="col-12">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                        <div class="invalid-feedback">Please enter your message.</div>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary px-5">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>
