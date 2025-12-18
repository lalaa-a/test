<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Explore Sri Lanka</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        /* CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Geologica', sans-serif;
            background-color: #f9fafb;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Hero Section */
        .hero-section {
            position: relative;
            height: 60vh;
            min-height: 400px;
            background: linear-gradient(135deg, #006a71 0%, #009ba6 100%);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .hero-content {
            color: white;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }
        
        .hero-title {
            font-family: 'Poppins', 'Inter', Arial, sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-family: 'Roboto', sans-serif;
            font-size: 1.3rem;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.6;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Content Sections */
        .content-section {
            margin-bottom: 80px;
        }
        
        .section-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            color: #374151;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .section-content {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }
        
        .about-text {
            font-family: 'Roboto', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 30px;
            text-align: justify;
        }
        
        /* Mission & Vision Grid */
        .mission-vision-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .mission-card, .vision-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .mission-card:hover, .vision-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        
        .mission-card {
            border-top: 4px solid #006a71;
        }
        
        .vision-card {
            border-top: 4px solid #009ba6;
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #006a71;
        }
        
        .card-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: #374151;
            margin-bottom: 20px;
        }
        
        .card-text {
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
            line-height: 1.7;
            color: #6b7280;
        }
        
        /* Values Grid */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .value-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .value-card:hover {
            transform: translateY(-3px);
        }
        
        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #006a71;
        }
        
        .value-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 1.3rem;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .value-text {
            font-family: 'Roboto', sans-serif;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #6b7280;
        }
        
        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .team-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
        }
        
        .team-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #006a71 0%, #009ba6 100%);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }
        
        .team-name {
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 1.3rem;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .team-role {
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
            color: #006a71;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .team-bio {
            font-family: 'Roboto', sans-serif;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #6b7280;
        }
        
        /* Contact CTA */
        .contact-cta {
            background: linear-gradient(135deg, #006a71 0%, #009ba6 100%);
            border-radius: 16px;
            padding: 50px;
            text-align: center;
            color: white;
        }
        
        .cta-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }
        
        .cta-text {
            font-family: 'Roboto', sans-serif;
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .cta-button {
            display: inline-block;
            background: white;
            color: #006a71;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .section-content {
                padding: 30px 20px;
            }
            
            .mission-card, .vision-card {
                padding: 30px 20px;
            }
            
            .contact-cta {
                padding: 40px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 15px;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .mission-vision-grid,
            .values-grid,
            .team-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>

</head>
<body>
    
    <?php renderComponent('inc','navigation',[]); ?>

    <!-- Main Content -->
        <section class="content-section">
            <h2 class="section-title">Our Story</h2>
            <div class="section-content">
                <p class="about-text">
                           Tripingoo is a travel planing web application designed and developed by CS group 07 for 2nd year group project in University of Colombo School of Computing.
                </p>


        <!-- Our Team -->
        <section class="content-section">
            <h2 class="section-title">Our Team</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3 class="team-name">Lalinda Ravishan</h3>
                    <p class="team-role">Team Member</p>
                    
                </div>
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3 class="team-name">Abhijeeth Kandauda</h3>
                    <p class="team-role">Team Member</p>
                    
                </div>
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3 class="team-name">Chiran Sandeepa</h3>
                    <p class="team-role">Team Member</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3 class="team-name">Ransara Geeneth</h3>
                    <p class="team-role">Team Member</p>
            </div>
        </section>


    </main>

    <?php renderComponent('inc','footer',[]); ?>

    
</body>
</html>