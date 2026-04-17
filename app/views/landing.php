<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripingoo — Craft Your Perfect Journey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #F2EFE7;
            --light-teal: #9ACBD0;
            --teal: #48A6A7;
            --dark-teal: #006A71;
            --white: #ffffff;
            --dark: #1a1a1a;
            --gray: #6b7280;
            --shadow: rgba(0, 106, 113, 0.1);
            --shadow-md: rgba(0, 106, 113, 0.15);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream);
            color: #2d3436;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Custom Cursor */
        .cursor-dot {
            width: 8px;
            height: 8px;
            background: var(--dark-teal);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99999;
            transition: transform 0.1s;
        }

        .cursor-ring {
            width: 35px;
            height: 35px;
            border: 2px solid var(--teal);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99998;
            transition: transform 0.15s, width 0.3s, height 0.3s;
        }

        /* Navbar */
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
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .navbar.scrolled {
            background: rgba(242, 239, 231, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 15px 60px;
            box-shadow: 0 4px 30px var(--shadow);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--teal), var(--dark-teal));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid var(--white);
            border-radius: 50%;
            top: 8px;
            left: 8px;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 10px solid var(--white);
            bottom: 8px;
            right: 8px;
            transform: rotate(45deg);
        }

        .logo-text {
            font-family: 'Geologica', sans-serif;
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
            gap: 40px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark-teal);
            font-weight: 500;
            font-size: 15px;
            position: relative;
            padding: 5px 0;
            transition: color 0.3s;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--teal);
            transition: width 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-ghost {
            background: transparent;
            color: var(--dark-teal);
            border: 2px solid transparent;
        }

        .btn-ghost:hover {
            border-color: var(--teal);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal), var(--dark-teal));
            color: var(--white);
            box-shadow: 0 4px 15px rgba(72, 166, 167, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 166, 167, 0.5);
        }

        .btn-outline {
            background: transparent;
            color: var(--dark-teal);
            border: 2px solid var(--dark-teal);
        }

        .btn-outline:hover {
            background: var(--dark-teal);
            color: var(--white);
        }

        .btn-large {
            padding: 16px 36px;
            font-size: 15px;
        }

        .btn-white {
            background: var(--white);
            color: var(--dark-teal);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
        }

        .hamburger span {
            width: 28px;
            height: 2px;
            background: var(--dark-teal);
            transition: all 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -6px);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 60px 80px;
            position: relative;
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
            z-index: 1;
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

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-title {
            font-family: 'Geologica', sans-serif;
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
            font-family: 'Geologica', sans-serif;
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

        .hero-right {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-visual {
            position: relative;
            width: 100%;
            max-width: 550px;
        }

        .hero-card-main {
            background: var(--white);
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 106, 113, 0.12);
            position: relative;
            z-index: 2;
        }

        .hero-card-image {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, var(--teal), var(--dark-teal));
            position: relative;
            overflow: hidden;
        }

        .hero-card-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 70%),
                linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.3) 100%);
        }

        .mountains-svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .sun-element {
            position: absolute;
            top: 40px;
            right: 50px;
            width: 60px;
            height: 60px;
            background: rgba(255, 200, 87, 0.9);
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(255, 200, 87, 0.5);
        }

        .cloud {
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 50px;
            height: 20px;
        }

        .cloud-1 {
            width: 80px;
            top: 50px;
            left: 40px;
            animation: cloudMove 12s linear infinite;
        }

        .cloud-2 {
            width: 60px;
            top: 80px;
            left: 120px;
            animation: cloudMove 15s linear infinite 3s;
        }

        .cloud-3 {
            width: 70px;
            top: 35px;
            left: 200px;
            animation: cloudMove 18s linear infinite 6s;
        }

        @keyframes cloudMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(300px); opacity: 0; }
        }

        .birds {
            position: absolute;
            top: 60px;
            left: 100px;
        }

        .bird {
            position: absolute;
            width: 0;
            height: 0;
        }

        .bird::before, .bird::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 2px;
            background: rgba(255,255,255,0.6);
            border-radius: 10px;
        }

        .bird::before {
            transform: rotate(-30deg);
            transform-origin: right;
        }

        .bird::after {
            transform: rotate(30deg);
            transform-origin: left;
        }

        .bird:nth-child(2) { left: 30px; top: -10px; transform: scale(0.7); }
        .bird:nth-child(3) { left: 15px; top: 15px; transform: scale(0.5); }

        .hero-card-body {
            padding: 25px 30px 30px;
        }

        .hero-card-location {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #7a8f96;
            margin-bottom: 8px;
        }

        .hero-card-location svg {
            width: 14px;
            height: 14px;
            fill: var(--teal);
        }

        .hero-card-title {
            font-family: 'Geologica', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--dark-teal);
            margin-bottom: 15px;
        }

        .hero-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-card-details {
            display: flex;
            gap: 20px;
        }

        .hero-card-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #7a8f96;
        }

        .hero-card-detail svg {
            width: 14px;
            height: 14px;
            fill: var(--teal);
        }

        .hero-card-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--teal);
        }

        .hero-card-price small {
            font-size: 12px;
            font-weight: 400;
            color: #7a8f96;
        }

        /* Floating cards */
        .floating-card {
            position: absolute;
            background: var(--white);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 15px 40px rgba(0, 106, 113, 0.1);
            z-index: 3;
            animation: floatCard 4s ease-in-out infinite;
        }

        .floating-card-1 {
            top: 30px;
            left: -40px;
            animation-delay: 0s;
        }

        .floating-card-2 {
            bottom: 60px;
            right: -30px;
            animation-delay: 1.5s;
        }

        .floating-card-3 {
            bottom: -10px;
            left: 30px;
            animation-delay: 3s;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .fc-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .fc-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .fc-icon.guide {
            background: rgba(154, 203, 208, 0.3);
        }

        .fc-icon.driver {
            background: rgba(72, 166, 167, 0.2);
        }

        .fc-icon.rating {
            background: rgba(255, 200, 87, 0.2);
        }

        .fc-text h4 {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-teal);
        }

        .fc-text p {
            font-size: 11px;
            color: #7a8f96;
        }

        /* Marquee */
        .marquee-section {
            padding: 30px 0;
            background: var(--dark-teal);
            overflow: hidden;
            position: relative;
        }

        .marquee-track {
            display: flex;
            animation: marquee 30s linear infinite;
            width: max-content;
        }

        .marquee-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0 40px;
            white-space: nowrap;
        }

        .marquee-item span {
            font-size: 16px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .marquee-dot {
            width: 6px;
            height: 6px;
            background: var(--teal);
            border-radius: 50%;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ============ HOW IT WORKS ============ */
        .how-it-works {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: 2px;
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
            font-family: 'Geologica', sans-serif;
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
            font-family: 'Geologica', sans-serif;
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
            font-family: 'Geologica', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .step-card p {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.7;
            max-width: 220px;
            margin: 0 auto;
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
            font-family: 'Geologica', sans-serif;
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

        /* Destinations */
        .destinations-section {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dest-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto auto;
            gap: 25px;
        }

        .dest-card {
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.4s;
            min-height: 300px;
        }

        .dest-card:hover {
            transform: scale(1.02);
        }

        .dest-card:nth-child(1) {
            grid-column: span 2;
            grid-row: span 2;
            min-height: 625px;
        }

        .dest-card-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            transition: transform 0.6s;
        }

        .dest-card:hover .dest-card-bg {
            transform: scale(1.08);
        }

        .dest-bg-1 {
            background: linear-gradient(135deg, #006A71 0%, #48A6A7 50%, #9ACBD0 100%);
        }

        .dest-bg-2 {
            background: linear-gradient(135deg, #48A6A7 0%, #006A71 100%);
        }

        .dest-bg-3 {
            background: linear-gradient(135deg, #9ACBD0 0%, #48A6A7 100%);
        }

        .dest-bg-4 {
            background: linear-gradient(135deg, #006A71 0%, #2c8587 100%);
        }

        .dest-bg-5 {
            background: linear-gradient(135deg, #48A6A7 0%, #9ACBD0 100%);
        }

        .dest-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image:
                radial-gradient(circle at 20% 30%, white 1px, transparent 1px),
                radial-gradient(circle at 80% 70%, white 1px, transparent 1px),
                radial-gradient(circle at 50% 50%, white 2px, transparent 2px);
            background-size: 60px 60px, 80px 80px, 100px 100px;
        }

        .dest-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px;
            background: linear-gradient(transparent, rgba(0,0,0,0.6));
            z-index: 2;
        }

        .dest-tag {
            display: inline-block;
            padding: 5px 14px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .dest-name {
            font-family: 'Geologica', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 5px;
        }

        .dest-card:nth-child(1) .dest-name {
            font-size: 34px;
        }

        .dest-info {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }

        /* Registration CTA */
        .register-section {
            padding: 120px 60px;
            background: var(--white);
        }

        .register-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        .register-card {
            border-radius: 32px;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s;
        }

        .register-card:hover {
            transform: translateY(-5px);
        }

        .register-card.guide-card {
            background: linear-gradient(135deg, var(--dark-teal), #004a4f);
            color: var(--white);
        }

        .register-card.driver-card {
            background: var(--white);
            color: var(--dark);
            box-shadow: 0 10px 40px rgba(0, 106, 113, 0.08);
            border: 1px solid rgba(0, 106, 113, 0.1);
        }

        .driver-card .register-emoji {
            color: var(--teal);
        }

        .driver-card .register-title {
            color: var(--dark-teal);
        }

        .driver-card .register-desc {
            opacity: 1;
            color: var(--gray);
        }

        .register-card-pattern {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            opacity: 0.08;
        }

        .register-emoji {
            font-size: 50px;
            margin-bottom: 25px;
            display: block;
        }

        .register-emoji i {
            font-size: inherit;
            line-height: 1;
        }

        .register-title {
            font-family: 'Geologica', sans-serif;
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .register-desc {
            font-size: 15px;
            line-height: 1.7;
            opacity: 0.85;
            margin-bottom: 30px;
            max-width: 400px;
        }

        .register-features {
            list-style: none;
            margin-bottom: 35px;
        }

        .register-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            font-size: 14px;
        }

        .register-features li .check {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .guide-card .check {
            background: rgba(255,255,255,0.15);
        }

        .driver-card .check {
            background: rgba(0, 106, 113, 0.15);
        }

        /* Testimonials */
        .testimonials-section {
            padding: 120px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: 24px;
            padding: 40px 35px;
            position: relative;
            transition: all 0.4s;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px var(--shadow);
        }

        .testimonial-card:nth-child(2) {
            transform: translateY(-20px);
        }

        .testimonial-card:nth-child(2):hover {
            transform: translateY(-25px);
        }

        .quote-mark {
            font-family: 'Geologica', sans-serif;
            font-size: 60px;
            color: var(--light-teal);
            line-height: 1;
            margin-bottom: 10px;
        }

        .testimonial-text {
            font-size: 15px;
            color: #5a6c72;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: var(--white);
        }

        .ta-1 { background: linear-gradient(135deg, var(--teal), var(--dark-teal)); }
        .ta-2 { background: linear-gradient(135deg, var(--dark-teal), #004a4f); }
        .ta-3 { background: linear-gradient(135deg, var(--light-teal), var(--teal)); }

        .testimonial-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark-teal);
        }

        .testimonial-role {
            font-size: 12px;
            color: #7a8f96;
        }

        .testimonial-stars {
            margin-left: auto;
            color: #ffc857;
            font-size: 14px;
            letter-spacing: 2px;
        }

        /* App Preview / CTA */
        .cta-section {
            padding: 120px 60px;
            background: linear-gradient(135deg, var(--dark-teal), #004a4f);
            position: relative;
            overflow: hidden;
        }

        .cta-bg-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .cta-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .cta-circle-1 { width: 500px; height: 500px; top: -200px; right: -100px; }
        .cta-circle-2 { width: 300px; height: 300px; bottom: -100px; left: -50px; }
        .cta-circle-3 { width: 200px; height: 200px; top: 50%; left: 50%; transform: translate(-50%, -50%); }

        .cta-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
            position: relative;
            z-index: 1;
        }

        .cta-left {
            max-width: 550px;
        }

        .cta-title {
            font-family: 'Geologica', sans-serif;
            font-size: 48px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 20px;
            letter-spacing: -1px;
            line-height: 1.15;
        }

        .cta-desc {
            font-size: 17px;
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .cta-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .cta-right {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .cta-stat-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 35px 30px;
            text-align: center;
            min-width: 180px;
            transition: transform 0.3s;
        }

        .cta-stat-card:hover {
            transform: translateY(-5px);
        }

        .cta-stat-card:nth-child(2) {
            margin-top: 40px;
        }

        .cta-stat-icon {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .cta-stat-number {
            font-size: 32px;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 5px;
        }

        .cta-stat-label {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
        }

        /* Footer */
        .footer {
            background: #0a2e30;
            padding: 80px 60px 30px;
            color: rgba(255,255,255,0.6);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 60px;
        }

        .footer-brand .logo-text {
            color: var(--white);
            margin-bottom: 15px;
        }

        .footer-brand .logo-text span {
            color: var(--teal);
        }

        .footer-brand p {
            font-size: 14px;
            line-height: 1.7;
            max-width: 300px;
            margin-bottom: 25px;
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .social-link {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: rgba(255,255,255,0.5);
            transition: all 0.3s;
            font-size: 16px;
        }

        .social-link:hover {
            background: var(--teal);
            color: var(--white);
            transform: translateY(-3px);
        }

        .footer-col h4 {
            color: var(--white);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-col ul li a:hover {
            color: var(--teal);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .footer-bottom-links {
            display: flex;
            gap: 25px;
        }

        .footer-bottom-links a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-bottom-links a:hover {
            color: var(--teal);
        }

        /* Scroll Animation */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(242, 239, 231, 0.98);
            backdrop-filter: blur(20px);
            z-index: 999;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 30px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .mobile-menu.open {
            opacity: 1;
            pointer-events: all;
        }

        .mobile-menu a {
            font-family: 'Geologica', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-teal);
            text-decoration: none;
            transition: color 0.3s;
        }

        .mobile-menu a:hover {
            color: var(--teal);
        }

        /* Counter Animation */
        .counter-animated {
            display: inline-block;
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--teal), var(--dark-teal));
            color: var(--white);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(72, 166, 167, 0.4);
            z-index: 100;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s;
        }

        .back-to-top.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(72, 166, 167, 0.5);
        }

        /* Loading Screen */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--dark-teal);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            transition: opacity 0.6s, visibility 0.6s;
        }

        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-text {
            font-family: 'Geologica', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--white);
        }

        .loader-text span {
            color: var(--light-teal);
        }

        .loader-bar {
            width: 200px;
            height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .loader-progress {
            height: 100%;
            background: var(--teal);
            border-radius: 10px;
            animation: loadProgress 1.5s ease-in-out forwards;
        }

        @keyframes loadProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 56px;
            }

            .hero-content {
                gap: 50px;
            }

            .steps-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }

            .steps-grid::before {
                display: none;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dest-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dest-card:nth-child(1) {
                grid-column: span 2;
                grid-row: span 1;
                min-height: 350px;
            }

            .footer-content {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        @media (max-width: 992px) {
            .navbar {
                padding: 15px 30px;
            }

            .nav-links {
                display: none;
            }

            .nav-actions {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .mobile-menu {
                display: flex;
            }

            .hero {
                padding: 120px 30px 80px;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .hero-title {
                font-size: 48px;
            }

            .hero-right {
                order: -1;
            }

            .hero-visual {
                max-width: 450px;
                margin: 0 auto;
            }

            .floating-card-1 {
                left: -10px;
            }

            .floating-card-2 {
                right: -10px;
            }

            .how-it-works {
                padding: 80px 30px;
            }

            .features {
                padding: 80px 30px;
            }

            .register-section {
                padding: 80px 30px;
            }

            .register-container {
                grid-template-columns: 1fr;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .testimonial-card:nth-child(2) {
                transform: none;
            }

            .testimonial-card:nth-child(2):hover {
                transform: translateY(-5px);
            }

            .cta-section {
                padding: 80px 30px;
            }

            .cta-content {
                flex-direction: column;
                text-align: center;
            }

            .cta-actions {
                justify-content: center;
            }

            .footer {
                padding: 60px 30px 30px;
            }

            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }

            .section-title {
                font-size: 38px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 38px;
                letter-spacing: -1px;
            }

            .hero-stats {
                gap: 30px;
            }

            .stat-number {
                font-size: 28px;
            }

            .steps-grid {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .dest-grid {
                grid-template-columns: 1fr;
            }

            .dest-card:nth-child(1) {
                grid-column: span 1;
                min-height: 300px;
            }

            .destinations-section {
                padding: 80px 30px;
            }

            .testimonials-section {
                padding: 80px 30px;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .cta-right {
                flex-direction: column;
                align-items: center;
            }

            .cta-stat-card:nth-child(2) {
                margin-top: 0;
            }

            .register-card {
                padding: 40px 30px;
            }

            .cursor-dot, .cursor-ring {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 32px;
            }

            .hero-desc {
                font-size: 16px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }

            .section-title {
                font-size: 30px;
            }

            .cta-title {
                font-size: 32px;
            }

            .floating-card {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <img src="http://localhost/test/public/img/logo/logo design 1(2).png" alt="Tripingoo" style="height: 70px; width: auto; margin-bottom: 20px;">
        <div class="loader-bar">
            <div class="loader-progress"></div>
        </div>
    </div>

    <!-- Custom Cursor -->
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <a href="#" class="logo">
            <img src="http://localhost/test/public/img/logo/logo design 1(2).png" alt="Tripingoo Logo" style="height: 55px; width: auto;">
        </a>

        <ul class="nav-links">
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#destinations">Destinations</a></li>
            <li><a href="#join">Join Us</a></li>
            <li><a href="#testimonials">Stories</a></li>
        </ul>

        <div class="nav-actions">
            <a href="#" class="btn btn-ghost">Log In</a>
            <a href="#" class="btn btn-primary">Get Started</a>
        </div>

        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="#how-it-works">How It Works</a>
        <a href="#features">Features</a>
        <a href="#destinations">Destinations</a>
        <a href="#join">Join Us</a>
        <a href="#testimonials">Stories</a>
        <a href="#" class="btn btn-primary btn-large" style="margin-top: 20px;">Get Started</a>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-badge">
                    <div class="pulse"></div>
                    Now available in 50+ countries
                </div>

                <h1 class="hero-title">
                    Craft Your <br>
                    <span class="highlight">Perfect</span>
                    <span class="accent"> Journey,</span><br>
                    Your Way
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
                        <div class="stat-number"><span class="counter" data-target="12">0</span>K<span>+</span></div>
                        <div class="stat-label">Happy Travelers</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><span class="counter" data-target="3">0</span>K<span>+</span></div>
                        <div class="stat-label">Verified Guides</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><span class="counter" data-target="50">0</span><span>+</span></div>
                        <div class="stat-label">Countries</div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-visual">
                    <!-- Main Card -->
                    <div class="hero-card-main">
                        <div class="hero-card-image">
                            <div class="sun-element"></div>
                            <div class="cloud cloud-1"></div>
                            <div class="cloud cloud-2"></div>
                            <div class="cloud cloud-3"></div>
                            <div class="birds">
                                <div class="bird"></div>
                                <div class="bird"></div>
                                <div class="bird"></div>
                            </div>
                            <svg class="mountains-svg" viewBox="0 0 500 150" preserveAspectRatio="none">
                                <polygon fill="rgba(0,106,113,0.6)" points="0,150 80,60 160,120 250,30 340,90 420,50 500,100 500,150"/>
                                <polygon fill="rgba(0,106,113,0.8)" points="0,150 60,100 140,70 200,110 300,60 380,100 500,70 500,150"/>
                                <polygon fill="rgba(0,106,113,1)" points="0,150 100,120 180,100 260,130 350,95 440,120 500,110 500,150"/>
                            </svg>
                        </div>
                        <div class="hero-card-body">
                            <div class="hero-card-location">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                Ella, Sri Lanka
                            </div>
                            <div class="hero-card-title">Ella Rock Sunrise Trek</div>
                            <div class="hero-card-meta">
                                <div class="hero-card-details">
                                    <div class="hero-card-detail">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                        5 Days
                                    </div>
                                    <div class="hero-card-detail">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        Guide + Driver
                                    </div>
                                </div>
                                <div class="hero-card-price">
                                    $89 <small>/day</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Cards -->
                    <div class="floating-card floating-card-1">
                        <div class="fc-row">
                            <div class="fc-icon guide"><i class="fas fa-compass"></i></div>
                            <div class="fc-text">
                                <h4>Kamal — Local Guide</h4>
                                <p><i class="fas fa-star"></i> 4.9 · 230 tours</p>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card floating-card-2">
                        <div class="fc-row">
                            <div class="fc-icon driver"><i class="fas fa-shuttle-van"></i></div>
                            <div class="fc-text">
                                <h4>Vehicle Booked!</h4>
                                <p>Toyota HiAce · AC</p>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card floating-card-3">
                        <div class="fc-row">
                            <div class="fc-icon rating"><i class="fas fa-star"></i></div>
                            <div class="fc-text">
                                <h4>4.9 out of 5</h4>
                                <p>12,340 reviews</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marquee -->
    <div class="marquee-section">
        <div class="marquee-track">
            <div class="marquee-item"><span>Custom Itineraries</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Local Guides</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Trusted Drivers</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>50+ Countries</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Authentic Experiences</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Secure Booking</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Real Reviews</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>24/7 Support</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Custom Itineraries</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Local Guides</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Trusted Drivers</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>50+ Countries</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Authentic Experiences</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Secure Booking</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>Real Reviews</span><div class="marquee-dot"></div></div>
            <div class="marquee-item"><span>24/7 Support</span><div class="marquee-dot"></div></div>
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

    <!-- Destinations -->
    <section class="destinations-section" id="destinations">
        <div class="section-header reveal">
            <div class="section-label">
                Popular Destinations
            </div>
            <h2 class="section-title">Where Will You Go Next?</h2>
            <p class="section-subtitle">Explore trending destinations with guides and drivers ready to welcome you</p>
        </div>

        <div class="dest-grid">
            <div class="dest-card reveal">
                <div class="dest-card-bg dest-bg-1"></div>
                <div class="dest-pattern"></div>
                <div class="dest-card-content">
                    <div class="dest-tag">Trending</div>
                    <div class="dest-name">Sri Lanka</div>
                    <div class="dest-info">245 Guides · 180 Drivers</div>
                </div>
            </div>

            <div class="dest-card reveal reveal-delay-1">
                <div class="dest-card-bg dest-bg-2"></div>
                <div class="dest-pattern"></div>
                <div class="dest-card-content">
                    <div class="dest-tag">Popular</div>
                    <div class="dest-name">Bali</div>
                    <div class="dest-info">312 Guides · 250 Drivers</div>
                </div>
            </div>

            <div class="dest-card reveal reveal-delay-2">
                <div class="dest-card-bg dest-bg-3"></div>
                <div class="dest-pattern"></div>
                <div class="dest-card-content">
                    <div class="dest-tag">Rising</div>
                    <div class="dest-name">Vietnam</div>
                    <div class="dest-info">189 Guides · 145 Drivers</div>
                </div>
            </div>

            <div class="dest-card reveal reveal-delay-3">
                <div class="dest-card-bg dest-bg-4"></div>
                <div class="dest-pattern"></div>
                <div class="dest-card-content">
                    <div class="dest-tag">Classic</div>
                    <div class="dest-name">Morocco</div>
                    <div class="dest-info">156 Guides · 120 Drivers</div>
                </div>
            </div>

            <div class="dest-card reveal reveal-delay-4">
                <div class="dest-card-bg dest-bg-5"></div>
                <div class="dest-pattern"></div>
                <div class="dest-card-content">
                    <div class="dest-tag">New</div>
                    <div class="dest-name">Georgia</div>
                    <div class="dest-info">98 Guides · 75 Drivers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Register CTA -->
    <section class="register-section" id="join">
        <div class="section-header reveal">
            <div class="section-label">
                Join Our Community
            </div>
            <h2 class="section-title">Are You a Guide or Driver?</h2>
            <p class="section-subtitle">Join thousands of professionals earning with Tripingoo. Registration is free.</p>
        </div>

        <div class="register-container">
            <div class="register-card guide-card reveal reveal-delay-1">
                <svg class="register-card-pattern" viewBox="0 0 300 300">
                    <circle cx="250" cy="50" r="150" fill="white"/>
                    <circle cx="280" cy="20" r="80" fill="white"/>
                </svg>
                <span class="register-emoji"><i class="fas fa-compass"></i></span>
                <h3 class="register-title">Register as a Guide</h3>
                <p class="register-desc">Share your local expertise with travelers from around the world. Set your own schedule, locations, and rates.</p>
                <ul class="register-features">
                    <li>
                        <span class="check">✓</span>
                        Set your availability & guide locations
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Get booked by travelers for their tours
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Build your reputation with verified reviews
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Receive secure, on-time payments
                    </li>
                </ul>
                <a href="#" class="btn btn-white btn-large">Become a Guide</a>
            </div>

            <div class="register-card driver-card reveal reveal-delay-2">
                <svg class="register-card-pattern" viewBox="0 0 300 300">
                    <circle cx="250" cy="50" r="150" fill="rgba(0,106,113,0.1)"/>
                    <circle cx="280" cy="20" r="80" fill="rgba(0,106,113,0.1)"/>
                </svg>
                <span class="register-emoji"><i class="fas fa-shuttle-van"></i></span>
                <h3 class="register-title">Register as a Driver</h3>
                <p class="register-desc">List your vehicle, set your zones, and connect with travelers who need reliable transportation.</p>
                <ul class="register-features">
                    <li>
                        <span class="check">✓</span>
                        Add your vehicle with full details & photos
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Travelers browse & book your vehicle directly
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Flexible scheduling — drive when you want
                    </li>
                    <li>
                        <span class="check">✓</span>
                        Earn consistently with repeat bookings
                    </li>
                </ul>
                <a href="#" class="btn btn-primary btn-large">Register Your Vehicle</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section" id="testimonials">
        <div class="section-header reveal">
            <div class="section-label">
                Traveler Stories
            </div>
            <h2 class="section-title">Loved by Thousands</h2>
            <p class="section-subtitle">Hear from real travelers who crafted their perfect journeys with Tripingoo</p>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card reveal reveal-delay-1">
                <div class="quote-mark">"</div>
                <p class="testimonial-text">Tripingoo made our Sri Lanka trip absolutely seamless. Our guide Kamal knew every hidden gem, and our driver was always on time with a spotless vehicle. The itinerary builder saved us hours of planning!</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar ta-1">S</div>
                    <div>
                        <div class="testimonial-name">Sarah Mitchell</div>
                        <div class="testimonial-role">Traveled to Sri Lanka</div>
                    </div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>

            <div class="testimonial-card reveal reveal-delay-2">
                <div class="quote-mark">"</div>
                <p class="testimonial-text">As a solo female traveler, safety was my priority. Every guide and driver on Tripingoo is verified, and the reviews gave me so much confidence. I've now used it for three trips — Morocco, Bali, and Vietnam!</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar ta-2">E</div>
                    <div>
                        <div class="testimonial-name">Emma Rodriguez</div>
                        <div class="testimonial-role">3 trips with Tripingoo</div>
                    </div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>

            <div class="testimonial-card reveal reveal-delay-3">
                <div class="quote-mark">"</div>
                <p class="testimonial-text">I registered as a guide 8 months ago, and Tripingoo has completely transformed my business. The platform is fair, payments are reliable, and I get to share my culture with amazing people every week.</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar ta-3">R</div>
                    <div>
                        <div class="testimonial-name">Rajesh Perera</div>
                        <div class="testimonial-role">Guide · Colombo, Sri Lanka</div>
                    </div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-bg-elements">
            <div class="cta-circle cta-circle-1"></div>
            <div class="cta-circle cta-circle-2"></div>
            <div class="cta-circle cta-circle-3"></div>
        </div>

        <div class="cta-content">
            <div class="cta-left reveal">
                <h2 class="cta-title">Ready to Start Your Next Adventure?</h2>
                <p class="cta-desc">Join thousands of travelers who plan smarter, travel deeper, and create memories that last a lifetime. Your perfect trip is just a few clicks away.</p>
                <div class="cta-actions">
                    <a href="#" class="btn btn-white btn-large">
                        Start Planning Free
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#" class="btn btn-large" style="border: 2px solid rgba(255,255,255,0.3); color: white;">
                        Register as Partner
                    </a>
                </div>
            </div>

            <div class="cta-right reveal reveal-delay-2">
                <div class="cta-stat-card">
                    <div class="cta-stat-icon">🌍</div>
                    <div class="cta-stat-number">50+</div>
                    <div class="cta-stat-label">Countries</div>
                </div>
                <div class="cta-stat-card">
                    <div class="cta-stat-icon">🤝</div>
                    <div class="cta-stat-number">15K+</div>
                    <div class="cta-stat-label">Successful Trips</div>
                </div>
                <div class="cta-stat-card">
                    <div class="cta-stat-icon">💎</div>
                    <div class="cta-stat-number">4.9</div>
                    <div class="cta-stat-label">Average Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <img src="http://localhost/test/public/img/logo/logo design 1(2).png" alt="Tripingoo Logo" style="height: 58px; width: auto; margin-bottom: 20px; display: block;">
                <p>Connecting travelers with authentic local experiences. Build itineraries, book guides, hire drivers — all in one beautiful platform.</p>
                <div class="social-links">
                    <a href="#" class="social-link">𝕏</a>
                    <a href="#" class="social-link">in</a>
                    <a href="#" class="social-link">IG</a>
                    <a href="#" class="social-link">fb</a>
                </div>
            </div>

            <div class="footer-col">
                <h4>For Travelers</h4>
                <ul>
                    <li><a href="#">Plan a Trip</a></li>
                    <li><a href="#">Find Guides</a></li>
                    <li><a href="#">Book Vehicles</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Travel Tips</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>For Partners</h4>
                <ul>
                    <li><a href="#">Become a Guide</a></li>
                    <li><a href="#">Register Vehicle</a></li>
                    <li><a href="#">Partner Dashboard</a></li>
                    <li><a href="#">Success Stories</a></li>
                    <li><a href="#">Partner FAQ</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Safety</a></li>
                    <li><a href="#">Cancellation</a></li>
                    <li><a href="#">Report Issue</a></li>
                    <li><a href="#">Community</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© 2025 Tripingoo. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Settings</a>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop">↑</button>

    <script>
        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loader').classList.add('hidden');
            }, 1800);
        });

        // Custom Cursor
        const cursorDot = document.getElementById('cursorDot');
        const cursorRing = document.getElementById('cursorRing');
        let mouseX = 0, mouseY = 0;
        let ringX = 0, ringY = 0;

        document.addEventListener('mousemove', function(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            cursorDot.style.left = mouseX - 4 + 'px';
            cursorDot.style.top = mouseY - 4 + 'px';
        });

        function animateRing() {
            ringX += (mouseX - ringX) * 0.15;
            ringY += (mouseY - ringY) * 0.15;
            cursorRing.style.left = ringX - 17.5 + 'px';
            cursorRing.style.top = ringY - 17.5 + 'px';
            requestAnimationFrame(animateRing);
        }
        animateRing();

        // Hover effects on interactive elements
        document.querySelectorAll('a, button, .feature-card, .dest-card, .step-card').forEach(function(el) {
            el.addEventListener('mouseenter', function() {
                cursorRing.style.width = '50px';
                cursorRing.style.height = '50px';
                cursorRing.style.borderColor = 'rgba(72, 166, 167, 0.5)';
                cursorDot.style.transform = 'scale(1.5)';
            });
            el.addEventListener('mouseleave', function() {
                cursorRing.style.width = '35px';
                cursorRing.style.height = '35px';
                cursorRing.style.borderColor = '#48A6A7';
                cursorDot.style.transform = 'scale(1)';
            });
        });

        // Navbar Scroll
        var navbar = document.getElementById('navbar');
        var backToTop = document.getElementById('backToTop');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            if (window.scrollY > 600) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Hamburger Menu
        var hamburger = document.getElementById('hamburger');
        var mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });

        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('open');
                document.body.style.overflow = '';
            });
        });

        // Scroll Reveal
        function revealOnScroll() {
            var reveals = document.querySelectorAll('.reveal');
            reveals.forEach(function(el) {
                var windowHeight = window.innerHeight;
                var elementTop = el.getBoundingClientRect().top;
                var revealPoint = 100;

                if (elementTop < windowHeight - revealPoint) {
                    el.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', function() {
            setTimeout(revealOnScroll, 200);
        });

        // Counter Animation
        function animateCounters() {
            var counters = document.querySelectorAll('.counter');
            counters.forEach(function(counter) {
                if (counter.dataset.animated) return;

                var rect = counter.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    counter.dataset.animated = 'true';
                    var target = parseInt(counter.getAttribute('data-target'));
                    var duration = 2000;
                    var start = 0;
                    var startTime = null;

                    function updateCounter(currentTime) {
                        if (!startTime) startTime = currentTime;
                        var progress = Math.min((currentTime - startTime) / duration, 1);
                        var eased = 1 - Math.pow(1 - progress, 3);
                        var current = Math.floor(eased * target);
                        counter.textContent = current;

                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    }
                    requestAnimationFrame(updateCounter);
                }
            });
        }

        window.addEventListener('scroll', animateCounters);
        window.addEventListener('load', function() {
            setTimeout(animateCounters, 2000);
        });

        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    var offsetTop = target.getBoundingClientRect().top + window.pageYOffset - 80;
                    window.scrollTo({ top: offsetTop, behavior: 'smooth' });
                }
            });
        });

        // Parallax effect on hero shapes
        window.addEventListener('scroll', function() {
            var scrolled = window.scrollY;
            var shapes = document.querySelectorAll('.shape');
            shapes.forEach(function(shape, index) {
                var speed = (index + 1) * 0.05;
                shape.style.transform = 'translateY(' + (scrolled * speed) + 'px)';
            });
        });

        // Intersection Observer for staggered animations
        if ('IntersectionObserver' in window) {
            var staggerObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.step-card, .feature-card, .dest-card').forEach(function(el) {
                staggerObserver.observe(el);
            });
        }
    </script>
</body>
</html>
