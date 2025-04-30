<?php include "../../includes/header.php";?>
<main>
    <div class="row contact_row" style="padding:0 200px">
        <div class="col-lg-12 form" style="padding:50px">
            <form id="contactForm" action="" method="POST" class="row g-3 needs-validation" novalidate>
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                    <div class="invalid-feedback">Please enter your name.</div>
                </div>

                <div class="col-md-12">
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
</main>
<?php include "../../includes/footer.php"; ?>
