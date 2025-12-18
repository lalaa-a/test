<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Events</title>
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

        :root {
            --primary-color: #006a71;
            --secondary-color:#48A6A7;
            --tertiary-color:#9ACBD0;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-light: #4b5563;
            --background-gray: #f9fafb;
            --card-background: white;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.13);
            --card-border-radius: 12px;
            --font-primary: 'Geologica', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background-color: var(--background-gray);
            color: var(--text-primary);
            
        }

        .content-wrapper {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            box-sizing: border-box;
            margin: 0 auto;
        }

        .trip-details-card {
            background: var(--card-background);
            border-radius: var(--card-border-radius);
            padding: 20px; /* Further reduced padding */
            margin-bottom: 20px; /* Reduced margin */
            border: 2px solid var(--border-color);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .trip-details-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            top:0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #00a8b1);
        }

        .trip-header {
            margin-bottom: 20px; /* Increased margin for more space */
        }

        .trip-image {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .trip-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .trip-image:hover img {
            transform: scale(1.05);
        }

        .trip-image .image-placeholder {
            color: white;
            font-size: 3rem;
            opacity: 0.8;
        }

        .trip-image .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.4));
            padding: 20px;
            color: white;
        }

        .trip-image .image-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .trip-image .image-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .trip-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 16px;
        }

        .trip-title-section {
            flex: 1;
        }

        .trip-status-section {
            flex-shrink: 0;
        }

        .trip-title {
            font-size: 1.6rem; /* Slightly larger */
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0;
            line-height: 1.2;
        }

        .trip-status {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #d1ecf1 0%, #9eeaf9 100%);
            color: #0c5460;
            border: 1px solid #9eeaf9;
            box-shadow: 0 2px 4px rgba(158, 234, 249, 0.3);
        }

        .trip-description {
            display: flex;
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.5; /* More comfortable reading */
            background: var(--background-gray);
            border-radius: 8px;
            min-height: auto; /* Allow content to determine height */
            overflow-wrap: break-word; /* Handle long words */
            word-wrap: break-word;
        }

        .trip-dates-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px; /* Reduced gap */
            margin-bottom: 0; /* Remove bottom margin */
        }

        .date-card {
            border-radius: 8px;
            padding: 12px 16px; /* Reduced padding */
            border: 1px solid var(--border-color);
            flex: 1;
            min-width: 120px; /* Minimum width for readability */
        }

        .date-label {
            font-size: 0.75rem; /* Smaller font */
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px; /* Reduced spacing */
            margin-bottom: 4px; /* Reduced margin */
            font-weight: 600;
        }

        .date-value {
            font-size: 1rem; /* Smaller font */
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px; /* Reduced gap */
        }

        .date-value i {
            color: var(--primary-color);
        }

        /* Date Navigation Bar */
        .date-navigation {
            background: var(--card-background);
            border-radius: var(--card-border-radius);
            box-shadow: 0 4px 16px var(--shadow-color);
            margin-bottom: 24px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .date-nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .date-nav-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .date-nav-controls {
            display: flex;
            gap: 8px;
        }

        .nav-btn {
            background: var(--background-gray);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .nav-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .date-nav-grid {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 8px 0;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        .date-nav-grid::-webkit-scrollbar {
            height: 6px;
        }

        .date-nav-grid::-webkit-scrollbar-track {
            background: var(--background-gray);
            border-radius: 3px;
        }

        .date-nav-grid::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .date-nav-grid::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        .date-nav-item {
            min-width: 120px;
            background: var(--background-gray);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .date-nav-item:hover {
            border-color: var(--primary-color);
            background: rgba(0, 106, 113, 0.05);
        }

        .date-nav-item.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .date-nav-day {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .date-nav-date {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .date-nav-month {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 2px;
        }

        /* Events Section */
        .events-section {
            background: var(--card-background);
            border-radius: var(--card-border-radius);
            box-shadow: 0 4px 16px var(--shadow-color);
            padding: 24px;
            border: 1px solid var(--border-color);
            max-width: 700px;
        }

        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--background-gray);
        }

        .events-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .selected-date-info {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .add-event-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .add-event-btn:hover {
            background: #005a61;
            transform: translateY(-1px);
        }

        .events-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .event-card {
            background: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0;
            transition: all 0.3s ease;
            border-left: none;
            display: flex;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            min-height: 140px;
        }

        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .event-time-section {
            width: 80px;
            background: var(--primary-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 16px 8px;
            flex-shrink: 0;
        }

        .event-start-time {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .event-end-time {
            font-size: 0.9rem;
            font-weight: 700;
            opacity: 0.9;
        }

        .time-label {
            font-size: 0.6rem;
            font-weight: 600;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .event-image {
            width: 200px;
            min-height: 140px;
            flex-shrink: 0;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-image i {
            font-size: 2.5rem;
            color: white;
            opacity: 0.9;
        }

        .event-content {
            flex: 1;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .event-type-badge {
            background: var(--primary-color);
            color: white;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .event-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-details {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--text-light);
            background: var(--background-gray);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .event-detail i {
            color: var(--primary-color);
            width: 12px;
            font-size: 0.75rem;
        }

        /* Guide Availability and Booking */
        .guide-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--background-gray);
        }

        .guide-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .guide-available {
            color: #10b981;
            font-weight: 600;
        }

        .guide-available i {
            color: #10b981;
        }

        .guide-unavailable {
            color: var(--text-light);
        }

        .guide-unavailable i {
            color: var(--text-light);
        }

        .guide-booked {
            color: #10b981;
            font-weight: 600;
        }

        .guide-booked i {
            color: #10b981;
        }

        .guide-booking-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .guide-booking-btn:hover {
            background: #005a61;
            transform: translateY(-1px);
        }

        .guide-booking-btn.booked {
            background: #10b981;
            cursor: default;
        }

        .guide-booking-btn.booked:hover {
            background: #10b981;
            transform: none;
        }

        .guide-booking-btn:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .guide-details {
            margin-top: 8px;
            padding: 8px;
            background: var(--background-gray);
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .guide-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .guide-rating {
            display: flex;
            align-items: center;
            gap: 2px;
            color: #f59e0b;
            margin-bottom: 2px;
        }

        .guide-price {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Event type specific colors for time section */
        .event-card[data-type="flight"] .event-time-section {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .event-card[data-type="accommodation"] .event-time-section {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .event-card[data-type="activity"] .event-time-section {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .event-card[data-type="meal"] .event-time-section {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .event-card[data-type="transport"] .event-time-section {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .event-card[data-type="other"] .event-time-section {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }

        /* Event type specific colors for image section */
        .event-card[data-type="flight"] .event-image {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .event-card[data-type="accommodation"] .event-image {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .event-card[data-type="activity"] .event-image {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .event-card[data-type="meal"] .event-image {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .event-card[data-type="transport"] .event-image {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .event-card[data-type="other"] .event-image {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }

        .empty-events {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }

        .empty-events i {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 16px;
        }

        .empty-events h3 {
            margin-bottom: 8px;
            color: var(--text-light);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }

            .trip-details-card {
                padding: 20px; /* Reduced padding for mobile */
            }

            .trip-title {
                font-size: 1.4rem; /* Smaller title on mobile */
            }

            .trip-image {
                height: 150px;
                margin-bottom: 15px;
            }

            .trip-image .image-overlay {
                padding: 15px;
            }

            .trip-image .image-title {
                font-size: 1rem;
            }

            .trip-image .image-subtitle {
                font-size: 0.8rem;
            }

            .trip-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .trip-dates-grid {
                flex-direction: column;
                gap: 12px;
            }

            .date-nav-grid {
                gap: 8px;
            }

            .date-nav-item {
                min-width: 100px;
                padding: 10px;
            }

            .events-section {
                padding: 16px;
            }

            .events-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .event-details {
                flex-direction: column;
                gap: 8px;
            }

            .guide-section {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .guide-booking-btn {
                width: 100%;
                justify-content: center;
            }

            .guide-details {
                width: 100%;
            }
        }

        /* Animation */
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

        .trip-details-card {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="trip-details-card">
            <div class="trip-header">
                <div class="trip-title-row">
                    <div class="trip-title-section">
                        <h2 class="trip-title">Amazing Sri Lanka Adventure</h2>
                    </div>
                    <div class="trip-status-section">
                        <span class="trip-status">
                            <i class="fas fa-calendar-check"></i>
                            Scheduled
                        </span>
                    </div>
                </div>
            </div>

            <div class="trip-image">
                <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop&crop=center" alt="Sri Lanka Landscape" loading="lazy">
                <div class="image-overlay">
                    <div class="image-title">Sri Lanka Adventure</div>
                    <div class="image-subtitle">Explore the Pearl of the Indian Ocean</div>
                </div>
            </div>

            <div class="trip-description">
                Embark on an unforgettable journey through the stunning landscapes and rich cultural heritage of Sri Lanka From the koh njnkjs djskndskd 
            </div>

            <div class="trip-dates-grid">
                <div class="date-card">
                    <div class="date-label">Start Date</div>
                    <div class="date-value">
                        <i class="fas fa-plane-departure"></i>
                        December 15, 2024
                    </div>
                </div>

                <div class="date-card">
                    <div class="date-label">End Date</div>
                    <div class="date-value">
                        <i class="fas fa-plane-arrival"></i>
                        December 25, 2024
                    </div>
                </div>

                <div class="date-card">
                    <div class="date-label">Duration</div>
                    <div class="date-value">
                        <i class="fas fa-clock"></i>
                        10 Days
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Navigation Bar -->
        <div class="date-navigation">
            <div class="date-nav-header">
                <h3 class="date-nav-title">Trip Timeline</h3>
                <div class="date-nav-controls">
                    <button class="nav-btn">← Prev</button>
                    <button class="nav-btn">Today</button>
                    <button class="nav-btn">Next →</button>
                </div>
            </div>
            <div class="date-nav-grid">
                <div class="date-nav-item active">
                    <div class="date-nav-day">FRI</div>
                    <div class="date-nav-date">15</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">SAT</div>
                    <div class="date-nav-date">16</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">SUN</div>
                    <div class="date-nav-date">17</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">MON</div>
                    <div class="date-nav-date">18</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">TUE</div>
                    <div class="date-nav-date">19</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">WED</div>
                    <div class="date-nav-date">20</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">FRI</div>
                    <div class="date-nav-date">22</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">SAT</div>
                    <div class="date-nav-date">23</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">SUN</div>
                    <div class="date-nav-date">24</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">MON</div>
                    <div class="date-nav-date">25</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                <div class="date-nav-item">
                    <div class="date-nav-day">THU</div>
                    <div class="date-nav-date">21</div>
                    <div class="date-nav-month">Dec</div>
                </div>
                
            </div>
        </div>

        <!-- Events Section -->
        <div class="events-section">
            <div class="events-header">
                <div>
                    <h3 class="events-title">Events Schedule</h3>
                    <p class="selected-date-info">Friday, December 15, 2024</p>
                </div>
                <button class="add-event-btn">
                    <i class="fas fa-plus"></i>
                    Add Event
                </button>
            </div>

            <div class="events-container">
                <!-- Sample Event Card -->
                <div class="event-card" data-type="flight">
                    <div class="event-time-section">
                        <div class="time-label">START</div>
                        <div class="event-start-time">08:00</div>
                        <div class="time-label">END</div>
                        <div class="event-end-time">10:00</div>
                    </div>
                    <div class="event-image">
                        <i class="fas fa-plane"></i>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">Airport Departure</h4>
                            </div>
                            <span class="event-type-badge">Flight</span>
                        </div>
                        <p class="event-description">
                            Departure from Colombo International Airport. Please arrive 2 hours early for international flights.
                        </p>
                        <div class="event-details">
                            <div class="event-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Colombo Airport</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-dollar-sign"></i>
                                <span>$450</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Event Card -->
                <div class="event-card" data-type="accommodation">
                    <div class="event-time-section">
                        <div class="time-label">START</div>
                        <div class="event-start-time">14:00</div>
                        <div class="time-label">END</div>
                        <div class="event-end-time">15:00</div>
                    </div>
                    <div class="event-image">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">Hotel Check-in</h4>
                            </div>
                            <span class="event-type-badge">Accommodation</span>
                        </div>
                        <p class="event-description">
                            Check into the Grand Oriental Hotel. Luxury accommodation with ocean view and world-class amenities.
                        </p>
                        <div class="event-details">
                            <div class="event-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Grand Oriental Hotel</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-dollar-sign"></i>
                                <span>$120/night</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-star"></i>
                                <span>5 Star</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Event Card -->
                <div class="event-card" data-type="activity">
                    <div class="event-time-section">
                        <div class="time-label">START</div>
                        <div class="event-start-time">16:00</div>
                        <div class="time-label">END</div>
                        <div class="event-end-time">19:00</div>
                    </div>
                    <div class="event-image">
                        <i class="fas fa-walking"></i>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">City Walking Tour</h4>
                            </div>
                            <span class="event-type-badge">Activity</span>
                        </div>
                        <p class="event-description">
                            Guided walking tour through the historic districts of Colombo, visiting temples and local markets.
                        </p>
                        <div class="event-details">
                            <div class="event-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Colombo Fort</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-users"></i>
                                <span>Group Tour</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-dollar-sign"></i>
                                <span>$45</span>
                            </div>
                        </div>
                        <div class="guide-section">
                            <div class="guide-info guide-available">
                                <i class="fas fa-user"></i>
                                <span>Chaminda Silva</span>
                            </div>
                            <button class="guide-booking-btn" onclick="bookGuide(this, 'activity-1')">
                                <i class="fas fa-plus"></i>
                                Add Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample Event Card -->
                <div class="event-card" data-type="meal">
                    <div class="event-time-section">
                        <div class="time-label">START</div>
                        <div class="event-start-time">19:30</div>
                        <div class="time-label">END</div>
                        <div class="event-end-time">21:30</div>
                    </div>
                    <div class="event-image">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">Traditional Sri Lankan Dinner</h4>
                            </div>
                            <span class="event-type-badge">Meal</span>
                        </div>
                        <p class="event-description">
                            Authentic Sri Lankan cuisine experience with traditional dishes and cultural performances.
                        </p>
                        <div class="event-details">
                            <div class="event-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Ministry of Crab</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-dollar-sign"></i>
                                <span>$85</span>
                            </div>
                        </div>
                        <div class="guide-section">
                            <div class="guide-info guide-booked">
                                <i class="fas fa-user"></i>
                                <span>Priya Fernando</span>
                            </div>
                            <button class="guide-booking-btn booked" onclick="bookGuide(this, 'meal-1')" disabled>
                                <i class="fas fa-check"></i>
                                Guide Booked
                            </button>
                        </div>
                        <div class="guide-details">
                            <div class="guide-name">Priya Fernando</div>
                            <div class="guide-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>4.5</span>
                            </div>
                            <div class="guide-price">$35/hour</div>
                        </div>
                    </div>
                </div>

                <!-- Empty State (hidden when events exist) -->
                <!-- <div class="empty-events" style="display: none;">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>No events scheduled</h3>
                    <p>Click "Add Event" to start planning your day</p>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript for date navigation
        document.querySelectorAll('.date-nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                document.querySelectorAll('.date-nav-item').forEach(i => i.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Update selected date info
                const day = this.querySelector('.date-nav-day').textContent;
                const date = this.querySelector('.date-nav-date').textContent;
                const month = this.querySelector('.date-nav-month').textContent;
                
                const selectedDateInfo = document.querySelector('.selected-date-info');
                selectedDateInfo.textContent = `${day}, ${month} ${date}, 2024`;
                
                // Here you would typically load events for the selected date
                console.log('Loading events for:', `${day}, ${month} ${date}, 2024`);
            });
        });

        // Add event button functionality
        document.querySelector('.add-event-btn').addEventListener('click', function() {
            alert('Add Event functionality would open a modal/form here');
        });

        window.bookGuide = bookGuide;

        // Guide booking functionality
        function bookGuide(button, eventId) {
            if (button.classList.contains('booked')) {
                return; // Already booked
            }

            // Show loading state
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
            button.disabled = true;

            // Simulate API call (replace with actual AJAX call)
            setTimeout(() => {
                // Mock successful booking
                button.classList.add('booked');
                button.innerHTML = '<i class="fas fa-check"></i> Guide Booked';
                button.disabled = true;

                // Update guide info - keep the guide name but change icon to indicate booked
                const guideSection = button.parentElement;
                const guideInfo = guideSection.querySelector('.guide-info');
                guideInfo.classList.remove('guide-available');
                guideInfo.classList.add('guide-booked');
                const guideName = guideInfo.querySelector('span').textContent;
                guideInfo.innerHTML = '<i class="fas fa-user-check"></i><span>' + guideName + '</span>';

                // Add guide details (in real app, this would come from API response)
                const guideDetails = document.createElement('div');
                guideDetails.className = 'guide-details';
                guideDetails.innerHTML = `
                    <div class="guide-name">Chaminda Silva</div>
                    <div class="guide-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>5.0</span>
                    </div>
                    <div class="guide-price">$40/hour</div>
                `;

                // Insert guide details after the guide section
                guideSection.parentElement.appendChild(guideDetails);

                // Show success message
                showNotification('Guide booked successfully!', 'success');
            }, 1500);
        }

        // Notification system
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#f59e0b'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                font-weight: 500;
                animation: slideInRight 0.3s ease;
            `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add notification animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
