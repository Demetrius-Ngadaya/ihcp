<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="gpi.png">
    <title>Global Path Insights Limited (GPI)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
          <style>
        body {
            scroll-behavior: smooth;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 1.5s ease, transform 1.5s ease;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        .hover-zoom:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .navbar-fixed {
            position: sticky;
            top: 0;
            z-index: 1020;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .parallax {
            background-image: url('https://via.placeholder.com/1920x1080');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            height: 50vh;
        }

        /* Blog Animation */
        .left-column {
            transform: translateX(100%); /* Start from the right */
            opacity: 0;
            transition: transform 1.5s ease, opacity 1.5s ease;
        }

        .right-column {
            transform: translateX(-100%); /* Start from the left */
            opacity: 0;
            transition: transform 1.5s ease, opacity 1.5s ease;
        }

        .middle-column-bottom {
            transform: translateY(100%); /* Start from the bottom */
            opacity: 0;
            transition: transform 1.5s ease, opacity 1.5s ease;
        }

        .middle-column-top {
            transform: translateY(-100%); /* Start from the top */
            opacity: 0;
            transition: transform 1.5s ease, opacity 1.5s ease;
        }

        /* Final state for Blog Section */
        .fade-in.show.left-column {
            transform: translateX(0); /* Move to original position */
            opacity: 1;
        }

        .fade-in.show.right-column {
            transform: translateX(0); /* Move to original position */
            opacity: 1;
        }

        .fade-in.show.middle-column-bottom {
            transform: translateY(0); /* Move to original position */
            opacity: 1;
        }

        .fade-in.show.middle-column-top {
            transform: translateY(0); /* Move to original position */
            opacity: 1;
        }
        .animated-numbers {
            font-size: 2rem;
            font-weight: bold;
        }
            @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-30px);
      }
      60% {
        transform: translateY(-15px);
      }
    }

    .animate-bounce {
      animation: bounce 2s infinite;
    }



    .hero-section {
        /* background-image: url('research3.webp'); */
        background-image: url('research2.jpg');
        background-size: cover;
        background-position: center;
        height: 90vh;
        position: relative;
        overflow: hidden;
    }

    .hero-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
    }

  

    /* Apply the effect to the hero words */
    .hero-section .fade-in {
        animation: slide-in 1.5s forwards;
    }

    /* Animation for text sliding from left to right */
    @keyframes slide-in {
        0% {
            opacity: 0;
            transform: translateX(-100%);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }


         /* Team image styling */
         #team img {
            width: 150px; /* Set a fixed size for the images */
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Hover effect */
        #team img:hover {
            transform: scale(2.2); /* Enlarge the image */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* Add spacing for the team members */
        #team .col-md-4 {
            margin-bottom: 2rem;
        }

        /* Center team member details */
        #team h5 {
            margin-top: 0.5rem;
            font-size: 1.25rem;
        }

        #team p {
            font-size: 0.95rem;
        }


        /* Add this CSS */
.navbar {
    background-color: black !important;  /* Black background */
}

.navbar .navbar-brand,
.navbar .nav-link {
    color: white !important;  /* White text */
}

/* Optional: Change the text color when hovering over the links */
.navbar .nav-link:hover {
    color: #ddd !important;  /* Light grey on hover */
}

    </style>
    <script>
        /* Your existing JavaScript remains the same */
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                counter.innerText = '0';

                const updateCounter = () => {
                    const target = +counter.getAttribute('data-target');
                    const current = +counter.innerText;
                    const increment = target / 200;

                    if (current < target) {
                        counter.innerText = `${Math.ceil(current + increment)}`;
                        setTimeout(updateCounter, 10);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCounter();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Trigger animation for the fade-in class
            const fadeInElements = document.querySelectorAll('.fade-in');
            fadeInElements.forEach(element => {
                element.classList.add('show');
            });
        });
    </script>
</head>

<body>
    <!-- Navbar (unchanged) -->
    <header class="navbar navbar-expand-lg navbar-light navbar-fixed">
        <div class="container">
            <img src="gpi.png" alt="Global Path Insights Limited" class="mb-3" style="width: 60px;">
            <a class="navbar-brand text-primary" href="#">Global Path Insights Limited (GPI)</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="#" class="nav-link text-primary">Home</a></li>
                    <li class="nav-item"><a href="#about" class="nav-link text-primary">About Us</a></li>
                    <li class="nav-item"><a href="#projects" class="nav-link text-primary">Projects</a></li>
                    <li class="nav-item"><a href="#team" class="nav-link text-primary">Team</a></li>
                    <li class="nav-item"><a href="#stats" class="nav-link text-primary">Stats</a></li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-primary" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- Contact Us Modal (unchanged) -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contact Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="send_email.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter your message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section (unchanged) -->
    <section class="hero-section text- d-flex align-items-center justify-content-center">
        <div class="hero-content text-center">
            <h1 class="display-4 fade-in">Innovating the Future with Research</h1>
            <p class="lead mt-3 fade-in">Providing insights, solutions, and data-driven research for impactful decisions.</p>
            <a href="#about" class="btn btn-light mt-4">Get Started</a>
        </div>
    </section>

    <!-- About Section - Now Dynamic -->
    <?php $about = $conn->query("SELECT * FROM about WHERE id=1")->fetch_assoc(); ?>
    <section id="about" class="py-5 fade-in">
        <div class="container text-center">
            <h3 class="text-primary"><?= htmlspecialchars($about['title']) ?></h3>
            <p class="mt-3 text-muted"><?= htmlspecialchars($about['content']) ?></p>
        </div>
    </section>

    <!-- Projects Section - Now Dynamic -->
    <section id="projects" class="py-5 fade-in">
        <div class="container text-center">
            <h3 class="text-primary">Our Projects</h3>
            <div class="row mt-4">
                <?php 
                $projects = $conn->query("SELECT * FROM projects ORDER BY id");
                $project_types = ['left-column', 'middle-column-bottom', 'middle-column-top', 'right-column'];
                $i = 0;
                while($p = $projects->fetch_assoc()): 
                    $col_class = $project_types[$i % count($project_types)];
                ?>
                <div class="col-md-3 fade-in <?= $col_class ?>">
                    <div class="card shadow hover-zoom">
                        <div class="card-body">
                            <h4 class="card-title text-primary"><?= htmlspecialchars($p['title']) ?></h4>
                            <p class="card-text"><?= htmlspecialchars($p['description']) ?></p>
                            <small class="text-muted">Status: <?= htmlspecialchars($p['status']) ?></small>
                        </div>
                    </div>
                </div>
                <?php 
                    $i++;
                    endwhile; 
                ?>
            </div>
        </div>
    </section>

    <!-- Blog Section - Now Dynamic -->
    <section id="blog" class="bg-light py-5">
        <div class="container text-center">
            <h3 class="text-primary">Latest Blog Posts</h3>
            <div class="row mt-4">
                <?php 
                $blogs = $conn->query("SELECT * FROM blogs ORDER BY id");
                $blog_types = ['left-column', 'middle-column-bottom', 'middle-column-top'];
                $j = 0;
                while($b = $blogs->fetch_assoc()): 
                    $blog_class = $blog_types[$j % count($blog_types)];
                ?>
                <div class="col-md-4 mt-4 fade-in <?= $blog_class ?>">
                    <div class="card shadow hover-zoom">
                        <div class="card-body">
                            <h4 class="card-title"><?= htmlspecialchars($b['title']) ?></h4>
                            <p class="card-text"><?= htmlspecialchars($b['content']) ?></p>
                            <small class="text-muted">Category: <?= htmlspecialchars($b['category']) ?></small>
                            <a href="#" class="btn btn-primary mt-2">Read More</a>
                        </div>
                    </div>
                </div>
                <?php 
                    $j++;
                    endwhile; 
                ?>
            </div>
        </div>
    </section>

    <!-- Team Section - Now Dynamic -->
    <section id="team" class="py-5 fade-in">
        <div class="container text-center">
            <h3 class="text-primary">Meet Our Team</h3>
            <div class="row mt-4">
                <?php 
                $team = $conn->query("SELECT * FROM team ORDER BY id");
                while($t = $team->fetch_assoc()): 
                ?>
                <div class="col-md-4">
                    <img src="uploads/<?= htmlspecialchars($t['image_path']) ?>" alt="<?= htmlspecialchars($t['name']) ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5><?= htmlspecialchars($t['name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($t['role']) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section - Now Dynamic -->
    <section id="stats" class="py-5 text-white" style="background: url('achievement.jpg') no-repeat center center/cover; height: 400px; background-color: rgba(0, 0, 0, 0.5); background-blend-mode: overlay;">
        <div class="container text-center">
            <h3 class="text-white">Our Achievements</h3>
            <div class="row mt-4">
                <?php 
                $stats = $conn->query("SELECT * FROM stats ORDER BY id");
                while($s = $stats->fetch_assoc()): 
                ?>
                <div class="col-md-3">
                    <div class="animated-numbers counter" data-target="<?= htmlspecialchars($s['value']) ?>">0</div>
                    <p><?= htmlspecialchars($s['label']) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer (unchanged) -->
    <footer class="footer bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="gpi2.png" alt="Global Path Insights Limited" class="mb-3" style="width: 190px;">
                    <h5>Global Path Insights Limited</h5>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-phone" style="color: green;"></i> 
                            Phone: +255 712 819 789
                        </li>
                        <li>
                            <i class="fas fa-envelope" style="color: #f39c12;"></i> 
                            Email: <a href="mailto:info@gpi.or.tz" class="text-primary">info@gpi.or.tz</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt" style="color: red;"></i> 
                            P.O. Box 175, Ifakara
                        </li>
                    </ul>
                    <div>
                        <h6>Follow us:</h6>
                        <a href="#" style="color: #4267B2;" class="me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: #1DA1F2;" class="me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #2867B2;" class="me-2"><i class="fab fa-linkedin"></i></a>
                        <a href="#" style="color: #C13584;" class="me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: red;" class="me-2"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#about" class="text-light">About</a></li>
                        <li><a href="#projects" class="text-light">Projects</a></li>
                        <li><a href="#team" class="text-light">Team</a></li>
                        <li><a href="#stats" class="text-light">Achievements</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Our office Location</h5>
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126387.90924060342!2d36.55509686104337!3d-8.139891669029529!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1857b310c02a18f9%3A0xb98701605d70cd2c!2sSt.Francis%20University%20of%20Health%20and%20Allied%20Sciences%20(SFUCHAS)!5e0!3m2!1ssw!2stz!4v1737914863738!5m2!1ssw!2stz" 
                        width="100%" 
                        height="200" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy"></iframe>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; <?= date('Y') ?> Global Path Insights Limited. All rights reserved.</p>
                <p>Designed and Developed by D_TECH</p>
            </div>
        </div>
    </footer>

    <!-- Your existing JavaScript for animations -->
    <script>
        // Function to animate numbers
        const animateNumbers = (element) => {
            const target = +element.getAttribute('data-target');
            const current = +element.innerText;
    
            const decrement = Math.ceil(target / 100);
    
            if (current > 0) {
                element.innerText = Math.max(current - decrement, 0);
                setTimeout(() => animateNumbers(element), 20);
            } else {
                element.innerText = target;
            }
        };
    
        // Intersection Observer to trigger animation
        const observ = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const counters = document.querySelectorAll('.counter');
                        counters.forEach((counter) => {
                            if (counter.innerText == counter.getAttribute('data-target')) {
                                counter.innerText = '200';
                                animateNumbers(counter);
                            }
                        });
                    }
                });
            },
            { threshold: 0.5 }
        );
    
        const achievementsSection = document.getElementById('stats');
        observ.observe(achievementsSection);
    </script>
    
    <script>
        const elements = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                } else {
                    entry.target.classList.remove('show');
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(element => observer.observe(element));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>