<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm 111 | Premium Student Residences</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3a86ff;
            --secondary: #8338ec;
            --accent: #ff006e;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.7;
            overflow-x: hidden;
        }
        
        .hero-section {
            height: 80vh;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            position: absolute;
            bottom: 15%;
            left: 10%;
            z-index: 2;
            max-width: 600px;
        }
        
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease;
        }
        
        .hero-location {
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease 0.2s forwards;
            opacity: 0;
        }
        
        .hero-location i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .price-tag {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            animation: fadeInUp 1s ease 0.4s forwards;
            opacity: 0;
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 700;
            margin-right: 10px;
        }
        
        .price-period {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .container {
            max-width: 1200px;
            margin: -100px auto 0;
            padding: 0 2rem;
            position: relative;
            z-index: 3;
        }
        
        .dorm-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 5rem;
        }
        
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            height: 500px;
        }
        
        .main-image {
            grid-column: span 2;
            position: relative;
            overflow: hidden;
        }
        
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        
        .main-image:hover img {
            transform: scale(1.05);
        }
        
        .thumbnails {
            position: relative;
            overflow: hidden;
        }
        
        .thumbnails img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .thumbnails:hover img {
            transform: scale(1.1);
        }
        
        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            padding: 2rem;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .thumbnails:hover .image-overlay {
            opacity: 1;
        }
        
        .dorm-content {
            padding: 3rem;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .feature {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            background: rgba(58, 134, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        
        .owner-section {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--dark), #16213e);
            color: white;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 3rem;
        }
        
        .owner-image {
            width: 40%;
            min-height: 400px;
            background: url('https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80') center/cover;
        }
        
        .owner-info {
            width: 60%;
            padding: 3rem;
        }
        
        .owner-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .owner-title {
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 2rem;
        }
        
        .contact-list {
            list-style: none;
            margin: 2rem 0;
        }
        
        .contact-list li {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .contact-list i {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(255, 0, 110, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 0, 110, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            margin-left: 1rem;
            box-shadow: none;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--dark);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .gallery {
                height: auto;
                grid-template-columns: 1fr;
            }
            
            .main-image {
                grid-column: span 1;
                height: 400px;
            }
            
            .thumbnails {
                height: 200px;
            }
            
            .owner-section {
                flex-direction: column;
            }
            
            .owner-image, .owner-info {
                width: 100%;
            }
            
            .owner-image {
                min-height: 300px;
            }
        }
        
        @media (max-width: 768px) {
            .hero-content {
                left: 5%;
                bottom: 10%;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Dorm 111</h1>
            <div class="hero-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Amman, Jordan - Premium Student Living</span>
            </div>
            <div class="price-tag">
                <span class="price">$555</span>
                <span class="price-period">/month all inclusive</span>
            </div>
        </div>
        
        <!-- Animated background elements -->
        <div style="position: absolute; top: 20%; right: 10%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(50px);"></div>
        <div style="position: absolute; bottom: 30%; left: 5%; width: 300px; height: 300px; background: rgba(255,0,110,0.1); border-radius: 50%; filter: blur(60px);"></div>
    </section>
    
    <div class="container">
        <div class="dorm-card">
            <div class="gallery">
                <div class="main-image">
                    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Luxury Dorm Room">
                </div>
                <div class="thumbnails">
                    <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Modern Bathroom">
                    <div class="image-overlay">
                        <h3>Luxury Bathroom</h3>
                    </div>
                </div>
                <div class="thumbnails">
                    <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Shared Kitchen">
                    <div class="image-overlay">
                        <h3>Shared Kitchen</h3>
                    </div>
                </div>
            </div>
            
            <div class="dorm-content">
                <h2 class="section-title">About This Residence</h2>
                <p>Experience premium student living in the heart of Amman. Dorm 111 offers a perfect blend of comfort, style, and convenience for the modern student. Our fully-furnished rooms are designed to provide the ideal environment for both study and relaxation.</p>
                
                <h3 class="section-title" style="margin-top: 3rem;">Amenities</h3>
                <div class="features-grid">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div>
                            <h4>High-Speed WiFi</h4>
                            <p>Fiber optic connection throughout</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-snowflake"></i>
                        </div>
                        <div>
                            <h4>Climate Control</h4>
                            <p>Individual AC units in each room</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h4>Modern Kitchen</h4>
                            <p>Fully equipped shared kitchen</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div>
                            <h4>Fitness Center</h4>
                            <p>24/7 access to gym facilities</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h4>Secure Access</h4>
                            <p>Keycard entry and CCTV</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div>
                            <h4>Laundry</h4>
                            <p>On-site laundry facilities</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="owner-section">
            <div class="owner-image"></div>
            <div class="owner-info">
                <h3 class="owner-name">faresalkahtib</h3>
                <p class="owner-title">Property Owner & Manager</p>
                <p>With over 5 years of experience in student housing, I'm committed to providing the best living experience for students in Amman.</p>
                
                <ul class="contact-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>faresplayx@gmail.com</span>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i>
                        <span>0786125882</span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Amman, Jordan</span>
                    </li>
                </ul>
                
                <div style="display: flex; margin-top: 2rem;">
                    <a href="#" class="btn">Book Now</a>
                    <a href="#" class="btn btn-outline">Ask Question</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Simple animation triggers
        document.addEventListener('DOMContentLoaded', function() {
            const features = document.querySelectorAll('.feature');
            
            features.forEach((feature, index) => {
                setTimeout(() => {
                    feature.style.opacity = '1';
                    feature.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>