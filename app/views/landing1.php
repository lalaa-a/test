<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripingoo — Craft Your Perfect Journey</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --cream: #F2EFE7;
            --light-teal: #9ACBD0;
            --teal: #48A6A7;
            --dark-teal: #006A71;
            --white: #ffffff;
            --dark: #1a1a1a;
            --gray: #6b7280;
            --light-gray: #f8f7f4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background: var(--cream);
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--cream); }
        ::-webkit-scrollbar-thumb { background: var(--teal); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--dark-teal); }

        /* ============ NAVBAR ============ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 12px 60px;
            box-shadow: 0 4px 30px rgba(0, 106, 113, 0.08);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: var(--dark-teal);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            transform: rotate(-5deg);
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-teal);
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--teal);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 36px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            font-size: 15px;
            position: relative;
            padding: 4px 0;
            transition: color 0.3s;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--teal);
            transition: width 0.3s;
            border-radius: 2px;
        }

        .nav-links a:hover { color: var(--dark-teal); }
        .nav-links a:hover::after { width: 100%; }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-login {
            padding: 10px 24px;
            border: 2px solid var(--dark-teal);
            background: transparent;
            color: var(--dark-teal);
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .btn-login:hover {
            background: var(--dark-teal);
            color: white;
        }

        .btn-signup {
            padding: 10px 24px;
            background: var(--dark-teal);
            color: white;
            border: 2px solid var(--dark-teal);
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .btn-signup:hover {
            background: var(--teal);
            border-color: var(--teal);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 106, 113, 0.25);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark-teal);
            cursor: pointer;
        }

        /* ============ HERO SECTION ============ */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 120px 60px 80px;
            overflow: hidden;
        }

        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
        }

        .shape-1 {
            width: 600px;
            height: 600px;
            background: var(--teal);
            top: -200px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: var(--light-teal);
            bottom: -100px;
            left: -100px;
            animation: float 10s ease-in-out infinite reverse;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            background: var(--dark-teal);
            top: 40%;
            left: 45%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-left {
            max-width: 620px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(72, 166, 167, 0.1);
            border: 1px solid rgba(72, 166, 167, 0.2);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-teal);
            margin-bottom: 28px;
            animation: fadeInUp 0.8s ease;
        }

        .hero-badge .dot {
            width: 8px;
            height: 8px;
            background: var(--teal);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 64px;
            font-weight: 800;
            line-height: 1.1;
            color: var(--dark);
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease 0.1s both;
        }

        .hero-title .highlight {
            color: var(--dark-teal);
            position: relative;
            display: inline-block;
        }

        .hero-title .highlight::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
            height: 12px;
            background: rgba(154, 203, 208, 0.4);
            z-index: -1;
            border-radius: 4px;
        }

        .hero-subtitle {
            font-size: 18px;
            line-height: 1.7;
            color: var(--gray);
            margin-bottom: 40px;
            max-width: 500px;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            gap: 20px;
            animation: fadeInUp 0.8s ease 0.3s both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            background: var(--dark-teal);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary:hover {
            background: var(--teal);
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 106, 113, 0.3);
        }

        .btn-primary i {
            transition: transform 0.3s;
        }

        .btn-primary:hover i {
            transform: translateX(4px);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 28px;
            background: transparent;
            color: var(--dark-teal);
            border: 2px solid rgba(0, 106, 113, 0.2);
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-secondary:hover {
            border-color: var(--teal);
            background: rgba(72, 166, 167, 0.05);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid rgba(0, 106, 113, 0.1);
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .stat h3 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--dark-teal);
        }

        .stat p {
            font-size: 13px;
            color: var(--gray);
            font-weight: 500;
            margin-top: 4px;
        }

        /* Hero Right - Image Collage */
        .hero-right {
            position: relative;
            height: 600px;
            animation: fadeInRight 1s ease 0.3s both;
        }

        .hero-image-main {
            position: absolute;
            top: 20px;
            right: 0;
            width: 420px;
            height: 520px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 106, 113, 0.15);
        }

        .hero-image-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-image-secondary {
            position: absolute;
            bottom: 40px;
            left: 0;
            width: 240px;
            height: 300px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 106, 113, 0.2);
            border: 5px solid var(--cream);
            z-index: 2;
        }

        .hero-image-secondary img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-floating-card {
            position: absolute;
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            z-index: 3;
            animation: floatCard 4s ease-in-out infinite;
        }

        .floating-card-1 {
            top: 0;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-card-1 .fc-icon {
            width: 44px;
            height: 44px;
            background: rgba(72, 166, 167, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--teal);
        }

        .floating-card-1 .fc-text h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
        }

        .floating-card-1 .fc-text p {
            font-size: 12px;
            color: var(--gray);
        }

        .floating-card-2 {
            bottom: 0;
            right: 40px;
            animation-delay: 1s;
        }

        .floating-card-2 .rating {
            display: flex;
            gap: 2px;
            color: #f59e0b;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .floating-card-2 p {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
        }

        .floating-card-2 span {
            font-size: 12px;
            color: var(--gray);
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ============ SEARCH BAR ============ */
        .search-section {
            padding: 0 60px;
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }

        .search-bar {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 20px 60px rgba(0, 106, 113, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-field {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 0 20px;
            border-right: 1px solid rgba(0, 0, 0, 0.08);
        }

        .search-field:last-of-type {
            border-right: none;
        }

        .search-field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--dark-teal);
        }

        .search-field input,
        .search-field select {
            border: none;
            outline: none;
            font-size: 15px;
            color: var(--dark);
            font-family: 'Inter', sans-serif;
            background: transparent;
            padding: 4px 0;
        }

        .search-field input::placeholder {
            color: #bbb;
        }

        .search-field select {
            -webkit-appearance: none;
            cursor: pointer;
        }

        .btn-search {
            padding: 16px 32px;
            background: var(--dark-teal);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
        }

        .btn-search:hover {
            background: var(--teal);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 106, 113, 0.3);
        }

        /* ============ HOW IT WORKS ============ */
        .how-it-works {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 70px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--teal);
            margin-bottom: 16px;
        }

        .section-label::before,
        .section-label::after {
            content: '';
            width: 30px;
            height: 1px;
            background: var(--teal);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 17px;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, var(--light-teal), var(--teal), var(--dark-teal));
            z-index: 0;
            opacity: 0.3;
        }

        .step-card {
            text-align: center;
            position: relative;
            z-index: 1;
            padding: 0 10px;
        }

        .step-number {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-teal);
            box-shadow: 0 8px 30px rgba(0, 106, 113, 0.1);
            border: 3px solid rgba(72, 166, 167, 0.2);
            transition: all 0.4s;
        }

        .step-card:hover .step-number {
            background: var(--dark-teal);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 12px 40px rgba(0, 106, 113, 0.25);
        }

        .step-icon {
            font-size: 28px;
            color: var(--teal);
            margin-bottom: 16px;
        }

        .step-card h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .step-card p {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.7;
        }

        /* ============ FEATURES ============ */
        .features {
            padding: 100px 60px;
            background: white;
        }

        .features-inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .feature-card {
            padding: 40px 36px;
            background: var(--cream);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--teal), var(--dark-teal));
            transform: scaleX(0);
            transition: transform 0.4s;
            transform-origin: left;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 106, 113, 0.1);
            background: white;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .fi-teal {
            background: rgba(72, 166, 167, 0.12);
            color: var(--teal);
        }

        .fi-dark {
            background: rgba(0, 106, 113, 0.1);
            color: var(--dark-teal);
        }

        .fi-light {
            background: rgba(154, 203, 208, 0.2);
            color: var(--teal);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .feature-card h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .feature-card p {
            font-size: 15px;
            color: var(--gray);
            line-height: 1.7;
        }

        /* ============ GUIDES & DRIVERS ============ */
        .guides-drivers {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .gd-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .gd-layout.reverse {
            direction: rtl;
        }

        .gd-layout.reverse > * {
            direction: ltr;
        }

        .gd-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .gd-content h2 span {
            color: var(--dark-teal);
        }

        .gd-content > p {
            font-size: 16px;
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 32px;
        }

        .gd-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 36px;
        }

        .gd-features li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 15px;
            color: var(--dark);
        }

        .gd-features li .check {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 50%;
            background: rgba(72, 166, 167, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--teal);
            font-size: 13px;
            margin-top: 1px;
        }

        .gd-visuals {
            position: relative;
            height: 500px;
        }

        .gd-card-stack {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .gd-profile-card {
            position: absolute;
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            width: 280px;
            transition: all 0.4s;
        }

        .gd-profile-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 20px 50px rgba(0, 106, 113, 0.15);
        }

        .gd-profile-card:nth-child(1) {
            top: 20px;
            left: 20px;
            transform: rotate(-3deg);
        }

        .gd-profile-card:nth-child(2) {
            top: 140px;
            right: 20px;
            transform: rotate(2deg);
        }

        .gd-profile-card:nth-child(3) {
            bottom: 20px;
            left: 60px;
            transform: rotate(-1deg);
        }

        .gd-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 12px;
        }

        .gd-profile-card h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .gd-profile-card .location {
            font-size: 13px;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .gd-profile-card .gd-rating {
            color: #f59e0b;
            font-size: 13px;
        }

        .gd-profile-card .gd-tag {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(72, 166, 167, 0.1);
            color: var(--teal);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        /* ============ DESTINATIONS ============ */
        .destinations {
            padding: 100px 60px;
            background: white;
        }

        .destinations-inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        .dest-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: 300px 300px;
            gap: 20px;
        }

        .dest-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
        }

        .dest-card:nth-child(1) {
            grid-row: span 2;
        }

        .dest-card:nth-child(4) {
            grid-row: span 2;
        }

        .dest-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s;
        }

        .dest-card:hover img {
            transform: scale(1.08);
        }

        .dest-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px 24px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            color: white;
        }

        .dest-overlay h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 600;
        }

        .dest-overlay p {
            font-size: 13px;
            opacity: 0.8;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .dest-guides-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        /* ============ VEHICLES SECTION ============ */
        .vehicles {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .vehicle-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 106, 113, 0.12);
        }

        .vehicle-img {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .vehicle-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .vehicle-card:hover .vehicle-img img {
            transform: scale(1.06);
        }

        .vehicle-type-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 6px 14px;
            background: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--dark-teal);
        }

        .vehicle-price {
            position: absolute;
            bottom: 16px;
            right: 16px;
            padding: 8px 16px;
            background: var(--dark-teal);
            color: white;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
        }

        .vehicle-price span {
            font-size: 12px;
            font-weight: 400;
            opacity: 0.8;
        }

        .vehicle-info {
            padding: 24px;
        }

        .vehicle-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .vehicle-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
            color: var(--gray);
            margin-bottom: 16px;
        }

        .vehicle-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vehicle-driver {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .vehicle-driver img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .vehicle-driver .vd-info h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
        }

        .vehicle-driver .vd-info p {
            font-size: 12px;
            color: var(--gray);
        }

        /* ============ TESTIMONIALS ============ */
        .testimonials {
            padding: 100px 60px;
            background: var(--dark-teal);
            position: relative;
            overflow: hidden;
        }

        .testimonials::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(154, 203, 208, 0.08);
        }

        .testimonials::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(72, 166, 167, 0.1);
        }

        .testimonials-inner {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .testimonials .section-label {
            color: var(--light-teal);
        }

        .testimonials .section-label::before,
        .testimonials .section-label::after {
            background: var(--light-teal);
        }

        .testimonials .section-title {
            color: white;
        }

        .testimonials .section-subtitle {
            color: rgba(255, 255, 255, 0.7);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 36px;
            transition: all 0.4s;
        }

        .testimonial-card:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-5px);
        }

        .testimonial-stars {
            color: #f59e0b;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .testimonial-text {
            font-size: 16px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 24px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .testimonial-author img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .testimonial-author h4 {
            color: white;
            font-size: 15px;
            font-weight: 600;
        }

        .testimonial-author p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        /* ============ CTA SECTION ============ */
        .cta-section {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .cta-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .cta-card {
            position: relative;
            border-radius: 24px;
            padding: 60px 50px;
            overflow: hidden;
            min-height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cta-traveler {
            background: linear-gradient(135deg, var(--dark-teal), #004d52);
            color: white;
        }

        .cta-provider {
            background: white;
            border: 2px solid rgba(0, 106, 113, 0.1);
            color: var(--dark);
        }

        .cta-card .cta-pattern {
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            opacity: 0.1;
        }

        .cta-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 24px;
        }

        .cta-traveler .cta-icon {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .cta-provider .cta-icon {
            background: rgba(72, 166, 167, 0.1);
            color: var(--teal);
        }

        .cta-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .cta-card p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 32px;
            opacity: 0.85;
        }

        .cta-traveler .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            background: white;
            color: var(--dark-teal);
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            width: fit-content;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .cta-traveler .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .cta-provider .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            background: var(--dark-teal);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            width: fit-content;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .cta-provider .btn-cta:hover {
            background: var(--teal);
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 106, 113, 0.3);
        }

        /* ============ APP DOWNLOAD ============ */
        .app-section {
            padding: 80px 60px;
            background: var(--light-teal);
            background: linear-gradient(135deg, rgba(154, 203, 208, 0.3), rgba(72, 166, 167, 0.15));
        }

        .app-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .app-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .app-text p {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 24px;
        }

        .app-buttons {
            display: flex;
            gap: 16px;
        }

        .app-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 28px;
            background: var(--dark);
            color: white;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .app-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .app-btn i {
            font-size: 28px;
        }

        .app-btn .app-btn-text span {
            font-size: 10px;
            opacity: 0.7;
            display: block;
        }

        .app-btn .app-btn-text strong {
            font-size: 16px;
        }

        /* ============ FOOTER ============ */
        .footer {
            background: #0a2a2d;
            color: white;
            padding: 80px 60px 30px;
        }

        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr;
            gap: 40px;
            padding-bottom: 50px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-brand .logo-text {
            color: white;
        }

        .footer-brand p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            margin-top: 16px;
            max-width: 300px;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .footer-socials a {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-socials a:hover {
            background: var(--teal);
            color: white;
            transform: translateY(-3px);
        }

        .footer-col h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-col ul a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .footer-col ul a:hover {
            color: var(--light-teal);
            padding-left: 5px;
        }

        .footer-newsletter p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 16px;
        }

        .newsletter-form {
            display: flex;
            gap: 8px;
        }

        .newsletter-form input {
            flex: 1;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            outline: none;
            font-family: 'Inter', sans-serif;
        }

        .newsletter-form input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .newsletter-form button {
            padding: 12px 20px;
            background: var(--teal);
            border: none;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .newsletter-form button:hover {
            background: var(--dark-teal);
        }

        .footer-bottom {
            padding-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
        }

        .footer-bottom-links {
            display: flex;
            gap: 24px;
        }

        .footer-bottom-links a {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-bottom-links a:hover {
            color: var(--light-teal);
        }

        /* ============ SCROLL ANIMATIONS ============ */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============ MOBILE MENU ============ */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            z-index: 999;
            padding: 80px 40px 40px;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        .mobile-menu .close-btn {
            position: absolute;
            top: 24px;
            right: 24px;
            background: none;
            border: none;
            font-size: 28px;
            color: var(--dark);
            cursor: pointer;
        }

        .mobile-menu a {
            display: block;
            padding: 16px 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            text-decoration: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 1200px) {
            .hero-title { font-size: 52px; }
            .dest-grid {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: auto;
            }
            .dest-card:nth-child(1),
            .dest-card:nth-child(4) {
                grid-row: auto;
            }
        }

        @media (max-width: 1024px) {
            .navbar { padding: 16px 30px; }
            .navbar.scrolled { padding: 12px 30px; }
            .nav-links { display: none; }
            .nav-actions .btn-login { display: none; }
            .mobile-toggle { display: block; }
            .mobile-menu { display: flex; }

            .hero { padding: 120px 30px 60px; }
            .hero-content { grid-template-columns: 1fr; gap: 40px; }
            .hero-right { display: none; }
            .hero-title { font-size: 48px; }

            .search-section { padding: 0 30px; }
            .search-bar { flex-direction: column; padding: 20px; }
            .search-field { border-right: none; border-bottom: 1px solid rgba(0,0,0,0.06); padding: 12px 0; }
            .search-field:last-of-type { border-bottom: none; }
            .btn-search { width: 100%; justify-content: center; }

            .how-it-works { padding: 80px 30px; }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid::before { display: none; }

            .features { padding: 80px 30px; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }

            .guides-drivers { padding: 80px 30px; }
            .gd-layout { grid-template-columns: 1fr; gap: 40px; }
            .gd-layout.reverse { direction: ltr; }
            .gd-visuals { height: 400px; }

            .destinations { padding: 80px 30px; }
            .vehicles { padding: 80px 30px; }
            .vehicles-grid { grid-template-columns: repeat(2, 1fr); }

            .testimonials { padding: 80px 30px; }
            .testimonial-grid { grid-template-columns: 1fr; }

            .cta-section { padding: 80px 30px; }
            .cta-container { grid-template-columns: 1fr; }

            .app-section { padding: 60px 30px; }
            .app-inner { flex-direction: column; text-align: center; }

            .footer { padding: 60px 30px 20px; }
            .footer-top { grid-template-columns: 1fr 1fr; gap: 30px; }
            .footer-bottom { flex-direction: column; gap: 16px; text-align: center; }
        }

        @media (max-width: 640px) {
            .hero-title { font-size: 36px; }
            .hero-stats { flex-direction: column; gap: 20px; }
            .section-title { font-size: 32px; }
            .steps-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .vehicles-grid { grid-template-columns: 1fr; }
            .dest-grid { grid-template-columns: 1fr; }
            .footer-top { grid-template-columns: 1fr; }
            .app-buttons { flex-direction: column; align-items: center; }
            .gd-visuals { height: 500px; }
            .gd-profile-card { width: 240px; }
            .gd-content h2 { font-size: 32px; }
            .cta-card { padding: 40px 30px; }
            .cta-card h2 { font-size: 28px; }
        }
    </style>
</head>
<body>

    <!-- ============ NAVBAR ============ -->
    <nav class="navbar" id="navbar">
        <a href="#" class="logo">
            <div class="logo-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <span class="logo-text">tripin<span>goo</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#guides">Guides</a></li>
            <li><a href="#vehicles">Vehicles</a></li>
            <li><a href="#destinations">Destinations</a></li>
        </ul>
        <div class="nav-actions">
            <button class="btn-login">Log In</button>
            <button class="btn-signup">Get Started</button>
            <button class="mobile-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="close-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-times"></i>
        </button>
        <a href="#how-it-works" onclick="toggleMobileMenu()">How It Works</a>
        <a href="#features" onclick="toggleMobileMenu()">Features</a>
        <a href="#guides" onclick="toggleMobileMenu()">Guides</a>
        <a href="#vehicles" onclick="toggleMobileMenu()">Vehicles</a>
        <a href="#destinations" onclick="toggleMobileMenu()">Destinations</a>
        <a href="#" style="color: var(--dark-teal);">Log In</a>
        <a href="#" style="color: var(--teal);">Get Started</a>
    </div>

    <!-- ============ HERO ============ -->
    <section class="hero">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Your Next Adventure Starts Here
                </div>
                <h1 class="hero-title">
                    Craft Your <span class="highlight">Perfect</span> Journey, Your Way
                </h1>
                <p class="hero-subtitle">
                    Build custom itineraries, connect with expert local guides, and book trusted drivers — all in one place. Travel smarter, not harder.
                </p>
                <div class="hero-cta">
                    <a href="#" class="btn-primary">
                        Start Planning <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#how-it-works" class="btn-secondary">
                        <i class="fas fa-play-circle"></i> See How
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <h3>12K+</h3>
                        <p>Happy Travelers</p>
                    </div>
                    <div class="stat">
                        <h3>850+</h3>
                        <p>Local Guides</p>
                    </div>
                    <div class="stat">
                        <h3>2.4K</h3>
                        <p>Registered Vehicles</p>
                    </div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-image-main">
                    <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80" alt="Travel destination">
                </div>
                <div class="hero-image-secondary">
                    <img src="https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600&q=80" alt="Travelers enjoying">
                </div>
                <div class="hero-floating-card floating-card-1">
                    <div class="fc-icon"><i class="fas fa-route"></i></div>
                    <div class="fc-text">
                        <h4>Itinerary Ready!</h4>
                        <p>Bali, 7 Days Trip</p>
                    </div>
                </div>
                <div class="hero-floating-card floating-card-2">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>4.9 out of 5</p>
                    <span>Based on 3,200 reviews</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SEARCH BAR ============ -->
    <div class="search-section">
        <div class="search-bar reveal">
            <div class="search-field">
                <label>Destination</label>
                <input type="text" placeholder="Where do you want to go?">
            </div>
            <div class="search-field">
                <label>Travel Date</label>
                <input type="text" placeholder="Pick a date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
            </div>
            <div class="search-field">
                <label>Travelers</label>
                <select>
                    <option>1 Traveler</option>
                    <option>2 Travelers</option>
                    <option>3-5 Travelers</option>
                    <option>6+ Travelers</option>
                </select>
            </div>
            <div class="search-field">
                <label>Need</label>
                <select>
                    <option>Guide + Driver</option>
                    <option>Guide Only</option>
                    <option>Driver Only</option>
                    <option>Self-Planned</option>
                </select>
            </div>
            <button class="btn-search">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>

    <!-- ============ HOW IT WORKS ============ -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header reveal">
            <div class="section-label">Simple Process</div>
            <h2 class="section-title">How Tripingoo Works</h2>
            <p class="section-subtitle">Plan your dream trip in four easy steps. We handle the complexity, you enjoy the adventure.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card reveal">
                <div class="step-number">01</div>
                <div class="step-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3>Build Your Itinerary</h3>
                <p>Choose your destinations, set dates, and create a day-by-day travel plan that fits your style and budget.</p>
            </div>
            <div class="step-card reveal">
                <div class="step-number">02</div>
                <div class="step-icon"><i class="fas fa-user-friends"></i></div>
                <h3>Find Local Guides</h3>
                <p>Browse verified guides in your destination. Read reviews, check expertise, and add them to your tour.</p>
            </div>
            <div class="step-card reveal">
                <div class="step-number">03</div>
                <div class="step-icon"><i class="fas fa-car-side"></i></div>
                <h3>Book a Vehicle</h3>
                <p>Select from a range of vehicles with experienced drivers. From sedans to vans — we have you covered.</p>
            </div>
            <div class="step-card reveal">
                <div class="step-number">04</div>
                <div class="step-icon"><i class="fas fa-umbrella-beach"></i></div>
                <h3>Enjoy Your Trip</h3>
                <p>Everything's set! Travel with confidence knowing your guide, driver, and itinerary are perfectly aligned.</p>
            </div>
        </div>
    </section>

    <!-- ============ FEATURES ============ -->
    <section class="features" id="features">
        <div class="features-inner">
            <div class="section-header reveal">
                <div class="section-label">Why Tripingoo</div>
                <h2 class="section-title">Everything You Need to Travel Smart</h2>
                <p class="section-subtitle">We bring together the essential tools and connections that transform ordinary trips into extraordinary experiences.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon fi-teal">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Custom Itineraries</h3>
                    <p>Build flexible, day-by-day travel plans with our intuitive planner. Drag, drop, and organize activities effortlessly.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon fi-dark">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <h3>Location-Based Guides</h3>
                    <p>Discover expert guides right where you're heading. Each guide is verified, reviewed, and ready to show you the real local experience.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon fi-light">
                        <i class="fas fa-shuttle-van"></i>
                    </div>
                    <h3>Vehicle Marketplace</h3>
                    <p>Drivers list their vehicles with details, photos, and pricing. Choose the perfect ride for your group and journey.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon fi-teal">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Verified Profiles</h3>
                    <p>Every guide and driver goes through identity verification and background checks. Travel with peace of mind.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon fi-dark">
                        <i class="fas fa-comments-dollar"></i>
                    </div>
                    <h3>Transparent Pricing</h3>
                    <p>No hidden fees, no surprises. See clear pricing upfront for guides, vehicles, and the entire trip package.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon fi-light">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Real-Time Support</h3>
                    <p>Chat with your guide, driver, or our support team anytime. We're with you before, during, and after your journey.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ GUIDES SECTION ============ -->
    <section class="guides-drivers" id="guides">
        <div class="gd-layout reveal">
            <div class="gd-content">
                <div class="section-label" style="justify-content: flex-start;">Local Guides</div>
                <h2>Connect with <span>Expert Local Guides</span> at Every Destination</h2>
                <p>Our guides aren't just navigators — they're storytellers, culture insiders, and passionate locals who turn your trip into unforgettable memories.</p>
                <ul class="gd-features">
                    <li>
                        <span class="check"><i class="fas fa-check"></i></span>
                        Browse guides by location, language, and specialty
                    </li>
                    <li>
                        <span class="check"><i class="fas fa-check"></i></span>
                        Read authentic reviews from fellow travelers
                    </li>
                    <li>
                        <span class="check"><i class="fas fa-check"></i></span>
                        Add guides directly to your itinerary with one click
                    </li>
                    <li>
                        <span class="check"><i class="fas fa-check"></i></span>
                        Chat before booking to ensure the perfect match
                    </li>
                </ul>
                <a href="#" class="btn-primary">
                    Explore Guides <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="gd-visuals">
                <div class="gd-card-stack">
                    <div class="gd-profile-card">
                        <img class="gd-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=80" alt="Guide">
                        <h4>Marco Fernandez</h4>
                        <div class="location"><i class="fas fa-map-marker-alt"></i> Barcelona, Spain</div>
                        <div class="gd-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span style="color: var(--gray); margin-left: 4px;">4.9</span>
                        </div>
                        <span class="gd-tag">Architecture Expert</span>
                    </div>
                    <div class="gd-profile-card">
                        <img class="gd-avatar" src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&q=80" alt="Guide">
                        <h4>Aiko Tanaka</h4>
                        <div class="location"><i class="fas fa-map-marker-alt"></i> Kyoto, Japan</div>
                        <div class="gd-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span style="color: var(--gray); margin-left: 4px;">4.8</span>
                        </div>
                        <span class="gd-tag">Cultural Tours</span>
                    </div>
                    <div class="gd-profile-card">
                        <img class="gd-avatar" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&q=80" alt="Guide">
                        <h4>Raj Patel</h4>
                        <div class="location"><i class="fas fa-map-marker-alt"></i> Jaipur, India</div>
                        <div class="gd-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span style="color: var(--gray); margin-left: 4px;">5.0</span>
                        </div>
                        <span class="gd-tag">Heritage Walks</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ DRIVERS & VEHICLES ============ -->
    <section class="vehicles" id="vehicles">
        <div class="section-header reveal">
            <div class="section-label">Vehicles & Drivers</div>
            <h2 class="section-title">Ride in Comfort, Travel with Trust</h2>
            <p class="section-subtitle">Drivers register their vehicles with photos, specs, and rates. You pick the perfect ride for your journey.</p>
        </div>
        <div class="vehicles-grid">
            <div class="vehicle-card reveal">
                <div class="vehicle-img">
                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0637?w=600&q=80" alt="SUV">
                    <span class="vehicle-type-badge">SUV</span>
                    <div class="vehicle-price">$85 <span>/day</span></div>
                </div>
                <div class="vehicle-info">
                    <h3>Toyota Land Cruiser</h3>
                    <div class="vehicle-meta">
                        <span><i class="fas fa-users"></i> 7 Seats</span>
                        <span><i class="fas fa-snowflake"></i> A/C</span>
                        <span><i class="fas fa-suitcase"></i> 4 Bags</span>
                    </div>
                    <div class="vehicle-driver">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=100&q=80" alt="Driver">
                        <div class="vd-info">
                            <h4>Ahmed Hassan</h4>
                            <p>⭐ 4.9 · 320 trips</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vehicle-card reveal">
                <div class="vehicle-img">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80" alt="Sedan">
                    <span class="vehicle-type-badge">Sedan</span>
                    <div class="vehicle-price">$55 <span>/day</span></div>
                </div>
                <div class="vehicle-info">
                    <h3>Mercedes E-Class</h3>
                    <div class="vehicle-meta">
                        <span><i class="fas fa-users"></i> 4 Seats</span>
                        <span><i class="fas fa-snowflake"></i> A/C</span>
                        <span><i class="fas fa-suitcase"></i> 3 Bags</span>
                    </div>
                    <div class="vehicle-driver">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Driver">
                        <div class="vd-info">
                            <h4>Carlos Rodriguez</h4>
                            <p>⭐ 4.8 · 245 trips</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vehicle-card reveal">
                <div class="vehicle-img">
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80" alt="Van">
                    <span class="vehicle-type-badge">Mini Bus</span>
                    <div class="vehicle-price">$120 <span>/day</span></div>
                </div>
                <div class="vehicle-info">
                    <h3>Mercedes Sprinter</h3>
                    <div class="vehicle-meta">
                        <span><i class="fas fa-users"></i> 15 Seats</span>
                        <span><i class="fas fa-snowflake"></i> A/C</span>
                        <span><i class="fas fa-suitcase"></i> 10 Bags</span>
                    </div>
                    <div class="vehicle-driver">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Driver">
                        <div class="vd-info">
                            <h4>James Mitchell</h4>
                            <p>⭐ 5.0 · 189 trips</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ DESTINATIONS ============ -->
    <section class="destinations" id="destinations">
        <div class="destinations-inner">
            <div class="section-header reveal">
                <div class="section-label">Popular Destinations</div>
                <h2 class="section-title">Where Will You Go Next?</h2>
                <p class="section-subtitle">Explore trending destinations with the most active guides and drivers ready to make your trip special.</p>
            </div>
            <div class="dest-grid reveal">
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80" alt="Bali">
                    <div class="dest-overlay">
                        <h3>Bali</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Indonesia</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 124 Guides Available</span>
                    </div>
                </div>
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80" alt="Paris">
                    <div class="dest-overlay">
                        <h3>Paris</h3>
                        <p><i class="fas fa-map-marker-alt"></i> France</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 89 Guides</span>
                    </div>
                </div>
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80" alt="Rajasthan">
                    <div class="dest-overlay">
                        <h3>Rajasthan</h3>
                        <p><i class="fas fa-map-marker-alt"></i> India</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 156 Guides</span>
                    </div>
                </div>
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80" alt="Kyoto">
                    <div class="dest-overlay">
                        <h3>Kyoto</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Japan</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 78 Guides</span>
                    </div>
                </div>
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1516483638261-f4dbaf036963?w=800&q=80" alt="Amalfi">
                    <div class="dest-overlay">
                        <h3>Amalfi Coast</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Italy</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 67 Guides</span>
                    </div>
                </div>
                <div class="dest-card">
                    <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=800&q=80" alt="Santorini">
                    <div class="dest-overlay">
                        <h3>Santorini</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Greece</p>
                        <span class="dest-guides-count"><i class="fas fa-user"></i> 52 Guides</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ TESTIMONIALS ============ -->
    <section class="testimonials">
        <div class="testimonials-inner">
            <div class="section-header reveal">
                <div class="section-label">Traveler Stories</div>
                <h2 class="section-title">Loved by Thousands of Travelers</h2>
                <p class="section-subtitle">Real stories from real travelers who crafted their perfect journeys with Tripingoo.</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card reveal">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Tripingoo completely changed how I plan trips. I found an amazing guide in Kyoto who showed us hidden temples that aren't in any guidebook. The custom itinerary feature is genius!"</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80" alt="Sarah">
                        <div>
                            <h4>Sarah Mitchell</h4>
                            <p>Traveled to Japan, 14 days</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"As a family of six, finding the right vehicle was always stressful. On Tripingoo, we booked a spacious van with an incredible driver who knew every scenic route in Tuscany."</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80" alt="David">
                        <div>
                            <h4>David Chen</h4>
                            <p>Family trip to Italy, 10 days</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text">"I registered as a guide on Tripingoo and it transformed my career. The platform connects me with travelers from all over the world. The booking system is seamless and fair."</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1489980557514-251d61e3eeb6?w=100&q=80" alt="Priya">
                        <div>
                            <h4>Priya Sharma</h4>
                            <p>Local Guide, Jaipur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ CTA SECTION ============ -->
    <section class="cta-section">
        <div class="cta-container">
            <div class="cta-card cta-traveler reveal">
                <div class="cta-icon">
                    <i class="fas fa-suitcase-rolling"></i>
                </div>
                <h2>Ready to Explore?</h2>
                <p>Create your free account, build your dream itinerary, and connect with local guides and drivers. Your next adventure is just a few clicks away.</p>
                <a href="#" class="btn-cta">
                    Start Your Journey <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="cta-card cta-provider reveal">
                <div class="cta-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h2>Guide or Driver?</h2>
                <p>Join our growing community of travel professionals. Register as a guide to share your local expertise, or list your vehicle to earn with every ride.</p>
                <a href="#" class="btn-cta">
                    Register Now <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ============ APP SECTION ============ -->
    <section class="app-section">
        <div class="app-inner reveal">
            <div class="app-text">
                <h2>Take Tripingoo Everywhere</h2>
                <p>Download our app for seamless trip planning on the go. Get real-time updates, chat with guides, and manage your bookings.</p>
                <div class="app-buttons">
                    <a href="#" class="app-btn">
                        <i class="fab fa-apple"></i>
                        <div class="app-btn-text">
                            <span>Download on the</span>
                            <strong>App Store</strong>
                        </div>
                    </a>
                    <a href="#" class="app-btn">
                        <i class="fab fa-google-play"></i>
                        <div class="app-btn-text">
                            <span>Get it on</span>
                            <strong>Google Play</strong>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <span class="logo-text">tripin<span>goo</span></span>
                    </a>
                    <p>Crafting unforgettable travel experiences by connecting adventurers with local experts worldwide.</p>
                    <div class="footer-socials">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>For Travelers</h4>
                    <ul>
                        <li><a href="#">Create Itinerary</a></li>
                        <li><a href="#">Find Guides</a></li>
                        <li><a href="#">Book Vehicles</a></li>
                        <li><a href="#">Destinations</a></li>
                        <li><a href="#">Travel Tips</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>For Providers</h4>
                    <ul>
                        <li><a href="#">Register as Guide</a></li>
                        <li><a href="#">Register as Driver</a></li>
                        <li><a href="#">List Your Vehicle</a></li>
                        <li><a href="#">Provider Dashboard</a></li>
                        <li><a href="#">Help Center</a></li>
                    </ul>
                </div>
                <div class="footer-col footer-newsletter">
                    <h4>Stay Updated</h4>
                    <p>Subscribe for travel tips, new destinations, and exclusive deals.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Your email address">
                        <button><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Tripingoo. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('open');
            document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
        }

        // Scroll reveal animation
        const revealElements = document.querySelectorAll('.reveal');

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('active');
                    }, index * 100);
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Parallax effect for hero shapes
        window.addEventListener('mousemove', (e) => {
            const shapes = document.querySelectorAll('.shape');
            const x = (e.clientX / window.innerWidth - 0.5) * 2;
            const y = (e.clientY / window.innerHeight - 0.5) * 2;

            shapes.forEach((shape, i) => {
                const speed = (i + 1) * 8;
                shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
            });
        });

        // Counter animation for stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const step = target / (duration / 16);
            const suffix = element.textContent.replace(/[0-9.,]/g, '');

            function update() {
                start += step;
                if (start >= target) {
                    start = target;
                    element.textContent = formatNumber(target) + suffix;
                    return;
                }
                element.textContent = formatNumber(Math.floor(start)) + suffix;
                requestAnimationFrame(update);
            }

            update();
        }

        function formatNumber(num) {
            if (num >= 1000) {
                return (num / 1000).toFixed(num % 1000 === 0 ? 0 : 1) + 'K';
            }
            return num.toString();
        }

        // Trigger counter animation when stats are visible
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat h3');
                    statNumbers.forEach(stat => {
                        const text = stat.textContent;
                        const num = parseFloat(text.replace(/[^0-9.]/g, ''));
                        const multiplier = text.includes('K') ? 1000 : 1;
                        animateCounter(stat, num * multiplier);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const heroStats = document.querySelector('.hero-stats');
        if (heroStats) statsObserver.observe(heroStats);
    </script>
</body>
</html>