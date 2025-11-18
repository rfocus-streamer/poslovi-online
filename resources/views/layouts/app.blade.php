<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Default SEO -->
    <title>@yield('title', 'Poslovi Online | Platforma za freelance usluge')</title>
    <meta name="description" content="@yield('meta_description', 'Poslovi Online | Pronađite ili ponudite digitalne usluge na najbržoj domaćoj freelance platformi.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- OpenGraph -->
    <meta property="og:title" content="@yield('og_title', 'Poslovi Online | Platforma za freelance usluge')">
    <meta property="og:description" content="@yield('og_description', 'Poslovi Online | Pronađite ili ponudite digitalne usluge na najbržoj domaćoj freelance platformi.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:type" content="website">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Poslovi Online | Platforma za freelance usluge')">
    <meta name="twitter:description" content="@yield('og_description', 'Poslovi Online | Pronađite ili ponudite digitalne usluge na najbržoj domaćoj freelance platformi.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">

    <meta name="google-site-verification" content="sbt7BLiUQl1OCgkhcEY9oMlj_hPM4vjnyK-jKosZFCU" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @auth
        <meta name="user_id" content="{{ auth()->user()->id }}">
        @vite(['resources/js/app.js'])
    @endauth

    <!-- Dodaj favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Canonical URL za SEO -->
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Facebook Domain Verification (opciono) -->
    <!-- <meta property="fb:domain_verification" content="YOUR_DOMAIN_VERIFICATION_CODE" /> -->

    <!-- Facebook App ID (opciono) -->
    <!-- <meta property="fb:app_id" content="YOUR_FACEBOOK_APP_ID"> -->

    @yield('head') <!-- Za dodatne head skripte/stilove po stranici -->

    <!-- JSON-LD Strukturirani podaci -->
    <!-- @yield('structured-data') -->
</head>
<style>
    /* ================================
       VARIJABLE I TEME
    ================================= */
    :root {
        --primary-color: #2f9b4b;
        --secondary-color: #9c1c2c;
        --text-color: rgba(0, 0, 0, .5);
        --bg-color: #fff;
        --menu-bg: #f8f9fa;
        --border-color: #ddd;
        --dropdown-bg: #fff;
        --dropdown-text: #333;
        --dropdown-hover: #f8f9fa;
    }

    .dark-theme {
        --primary-color: #2f9b4b;
        --secondary-color: #9c1c2c;
        --text-color: #f0f0f0;
        --bg-color: #121212;
        --menu-bg: #1e1e1e;
        --border-color: #444;
        --dropdown-bg: #2d2d2d;
        --dropdown-text: #f0f0f0;
        --dropdown-hover: #3d3d3d;
    }

    /* ================================
       GLOBALNI RESET I BAZA
    ================================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        line-height: 1.6;
    }

    ul, li {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    /* ================================
       NAVIGACIJA I HEADER
    ================================= */
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: var(--menu-bg);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
    }

    .logo {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .search-container {
        display: flex;
        align-items: center;
        background-color: var(--bg-color);
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        border-radius: 15px; /* ako želiš i div da zaobliš */
    }

    .search-input {
        padding: 3px 7px;
        border: none;
        outline: none;
        background-color: transparent;
        color: var(--text-color);
        width: 215px;
        border-radius: 20px; /* ili koliko god hoćeš */
    }

    /* Stil za search modal koji se preklapa preko postojećeg sadržaja */
    #searchModal {
        display: none;
        margin-left: -260px !important;
    }

    /* Povećaj z-index za sticky header da bude iznad ostalog sadržaja */
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: var(--menu-bg);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .closeSearch {
        margin-top: -11px !important;
        font-size: 18px;
        cursor: pointer;
        position: absolute;
        margin-left: 210px;
    }

    .search-btn {
        padding: 3px 9px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-btn:hover {
        background-color: var(--secondary-color);
    }

    .user-menu {
         margin-top: 15px !important;
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .user-menu li a {
        /*margin-top: 15px;*/
        text-decoration: none;
        color: var(--text-color);
        padding: 8px 15px;
        border-radius: 4px;
        transition: background-color 0.3s;
        margin-bottom: 13px;
    }

    .user-menu li a:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .theme-toggle {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        color: var(--text-color);
        padding: 5px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theme-toggle:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .open-search-modal-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 14px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    .open-search-modal-btn:hover {
        background-color: var(--secondary-color);
        transform: scale(1.02);
    }

    .open-search-modal-btn:active {
        transform: scale(0.98);
    }

    /* ================================
       KATEGORIJE
    ================================= */
    .categories-navbar {
        background-color: var(--menu-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        padding: 0;
    }

    .categories-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 10px;
    }

    .categories-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
    }

    .categories-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin: 0;
        padding: 5px 0;
    }

    .category-item {
        position: relative;
    }

    .category-link {
        text-decoration: none;
        color: var(--text-color);
        padding: 4px 10px;
        border-radius: 4px;
        transition: background-color 0.3s, color 0.3s;
        display: flex;
        align-items: center;
        font-size: 0.89rem;
    }

    .category-link:hover {
        background-color: rgba(0, 0, 0, 0.05);
        color: var(--primary-color);
    }

    .category-link .dropdown-icon {
        margin-left: 4px;
        font-size: 0.7rem;
    }

    .subcategories-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: var(--dropdown-bg);
        min-width: 180px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(5px);
        transition: opacity 0.3s, visibility 0.3s, transform 0.3s;
        z-index: 1000;
        border: 1px solid var(--border-color);
        margin: 0;
        padding: 5px 0;
    }

    .category-item:hover .subcategories-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .subcategory-link {
        display: block;
        padding: 6px 12px;
        text-decoration: none;
        color: var(--dropdown-text);
        transition: background-color 0.3s;
        font-size: 0.8rem;
    }

    .subcategory-link:hover {
        background-color: var(--dropdown-hover);
        color: var(--primary-color);
    }

    /* Postojeći stilovi */
        .switch {
            position: relative;
            display: inline-block;
            width: 160px;
            height: 20px;
            top: -8px !important;
            cursor: pointer;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
            background: linear-gradient(to right, #ccc 50%, #4CAF50 50%);
            justify-content: space-between;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            border-radius: 50%;
            left: 2px;
            bottom: 1px;
            background-color: white;
            transition: 0.4s;
        }

        .label-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            margin-left: 27px;
        }

        .label-text.left {
            left: 12px;
        }

        .label-text.right {
            right: 22px;
        }

        input:checked + .slider {
            background: linear-gradient(to right, #ccc 50%, #4CAF50 50%);
        }

        input:checked + .slider:before {
            transform: translateX(138px);
        }

        input:checked + .slider .label-text.left {
            color: #ccc;
        }

        input:checked + .slider .label-text.right {
            color: #fff;
        }

        input:not(:checked) + .slider {
            background: linear-gradient(to right, #9c1c2c 50%, #ccc 50%);
        }

        input:not(:checked) + .slider:before {
            transform: translateX(0px);
        }

        input:not(:checked) + .slider .label-text.left {
            color: #fff;
        }

        input:not(:checked) + .slider .label-text.right {
            color: #ccc;
        }

        .add-service-title {
            color: #9c1c2c;
            font-weight: bold;
            position: relative;
            top: 7px;
            font-size: 0.81rem;
        }

        .add-service-title:hover {
            color: #4CAF50;
            text-decoration: none;
        }

        .add-service-title mark {
            background: linear-gradient(120deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 0 10px;
            border-radius: 5px;
        }

        .add-service-title2 {
            color: #9c1c2c;
            font-weight: bold;
            position: relative;
            top: 7px;
            font-size: 0.81rem;
        }

        .add-service-title2:hover {
            color: #4CAF50;
            text-decoration: none;
        }

        .add-service-title2 mark {
            background: linear-gradient(120deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 0 10px;
            border-radius: 5px;
        }

    /* ================================
       GLAVNI SADRŽAJ
    ================================= */
    .main-content {
        padding: 30px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .section-title {
        margin-bottom: 15px;
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 8px;
        font-size: 1.5rem;
    }

    .job-categories {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .category-card {
        background-color: var(--menu-bg);
        border-radius: 6px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .category-card:hover {
        transform: translateY(-3px);
    }

    .category-card h3 {
        color: var(--primary-color);
        margin-bottom: 8px;
        font-size: 1.1rem;
    }

    .category-card p {
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 0;
    }

    /* ================================
       DROPDOWN STILOVI
    ================================= */
    .dropdown-menu {
        background-color: var(--dropdown-bg);
        border: 1px solid var(--border-color);
    }

    .dropdown-item {
        color: var(--dropdown-text);
    }

    .dropdown-item:hover {
        background-color: var(--dropdown-hover);
        color: var(--dropdown-text);
    }

    a.sixteen-font-size {
        color: var(--text-color); /* Nasleđuje se na SVG */
        font-size: 16px;
        transition: color 0.3s ease;
    }

    a.sixteen-font-size:hover {
        color: var(--primary-color); /* Promeni boju na hover */
    }

    a.sixteen-font-size-modal {
        color: var(--text-color); /* Nasleđuje se na SVG */
        font-size: 16px;
        transition: color 0.3s ease;
    }

    a.sixteen-font-size-modal:hover {
        color: var(--primary-color); /* Promeni boju na hover */
    }

    /* ================================
       INCOMING CALL MODAL STYLES
    ================================= */
    .incoming-call-modal {
        max-width: 480px;
    }

    .incoming-call-modal .modal-content {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .incoming-call-modal .modal-header {
        background: linear-gradient(135deg, var(--primary-color), #267c3e);
        color: white;
        border-bottom: none;
        padding: 1rem 1.25rem;
        position: relative;
    }

    .incoming-call-modal .modal-header .btn-close {
        filter: invert(1);
        opacity: 0.8;
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
    }

    .incoming-call-modal .modal-header .btn-close:hover {
        opacity: 1;
    }

    .call-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-close{
        background-color: #9c1c2c !important;
    }

    .call-icon {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .call-icon i {
        font-size: 1rem;
    }

    .call-info h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .call-status {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    .incoming-call-modal .modal-body {
        padding: 1.5rem 1.25rem;
    }

    .caller-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .caller-avatar-container {
        position: relative;
    }

    .caller-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
    }

    .caller-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: #4CAF50;
        border: 2px solid var(--bg-color);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }

    .caller-details {
        flex: 1;
    }

    .caller-name {
        margin: 0 0 4px 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .call-service {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-color);
        opacity: 0.8;
    }


    /*  ================================
       HORIZONTAL CALL BUTTONS STYLES
    ================================= */

        .call-buttons-horizontal {
            display: flex;
            gap: 16px;
            justify-content: center;
            align-items: center;
        }

        .call-btn-horizontal {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 120px;
            justify-content: center;
            font-size: 0.95rem;
        }

        .call-btn-horizontal i {
            font-size: 1.1rem;
        }

        .call-btn-horizontal span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .decline-btn-horizontal {
            background: #dc3545;
            color: white;
            border: 2px solid #dc3545;
        }

        .decline-btn-horizontal:hover {
            background: #c82333;
            border-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .accept-btn-horizontal {
            background: var(--primary-color);
            color: white;
            border: 2px solid var(--primary-color);
        }

        .accept-btn-horizontal:hover {
            background: #267c3e;
            border-color: #267c3e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 155, 75, 0.3);
        }

        /* Dark theme adjustments */
        .dark-theme .call-btn-horizontal {
            color: white;
        }


        /* Dark theme adjustments */
        .dark-theme .incoming-call-modal .modal-content {
            background: var(--menu-bg);
            border-color: var(--border-color);
        }

        .dark-theme .caller-name,
        .dark-theme .call-service {
            color: var(--text-color);
        }

        .btn-poslovi {
            color: #fff !important;
            background-color: #9c1c2c !important;
            border-color: #9c1c2c !important;
        }

        /* ================================
           MOBILE SEARCH MODAL STYLES - FIXED
        ================================= */
        .mobile-search-modal {
            max-width: 95%;
            margin: 0 auto;
        }

        .mobile-search-modal .modal-content {
            background: var(--menu-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .mobile-search-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #267c3e);
            color: white;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
            border-radius: 16px 16px 0 0;
        }

        .search-header {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .search-modal-icon {
            font-size: 1.3rem;
            opacity: 0.9;
        }

        .mobile-search-modal .modal-title {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .mobile-search-modal .modal-body {
            padding: 1.5rem;
        }

        .search-container-mobile {
            display: flex;
            align-items: center;
            background: var(--bg-color);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .search-container-mobile:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(47, 155, 75, 0.1);
        }

        .search-input-mobile {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            color: var(--text-color);
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 500;
        }

        .search-input-mobile::placeholder {
            color: var(--text-color);
            opacity: 0.7;
        }

        .search-btn-mobile {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn-mobile:hover {
            background: #267c3e;
        }

        .search-btn-mobile i {
            font-size: 1.1rem;
        }

        .search-suggestions {
            max-height: 200px;
            overflow-y: auto;
        }

        .search-suggestion-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .search-suggestion-item:hover {
            background: var(--dropdown-hover);
            color: var(--primary-color);
            padding-left: 1.25rem;
        }

        .search-suggestion-item:last-child {
            border-bottom: none;
        }

        /* Dark theme adjustments */
        .dark-theme .mobile-search-modal .modal-content {
            background: var(--menu-bg);
            border-color: var(--border-color);
        }

        .dark-theme .search-container-mobile {
            background: var(--dropdown-bg);
            border-color: var(--border-color);
        }

        .dark-theme .search-input-mobile {
            color: var(--dropdown-text);
        }

        .dark-theme .search-suggestion-item {
            color: var(--dropdown-text);
            border-color: var(--border-color);
        }

        .dark-theme .search-suggestion-item:hover {
            background: var(--dropdown-hover);
            color: var(--primary-color);
        }

        /* Z-INDEX FIXES - CRITICAL */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        #searchModalMobile {
            z-index: 9999 !important;
        }

        .mobile-search-modal .modal-content {
            position: relative;
            z-index: 10000 !important;
        }

        /* Custom Close Button */
        .btn-close-custom {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 0;
        }

        .btn-close-custom:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            opacity: 1;
            transform: rotate(90deg);
        }

        .btn-close-custom i {
            font-size: 1.1rem;
        }

    /* ================================
       MOBILE BOTTOM NAVIGATION
       (Like the image provided)
    ================================= */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--menu-bg);
        border-top: 1px solid var(--border-color);
        padding: 8px 0 10px;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    .mobile-bottom-nav .nav-container {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        max-width: 100%;
        margin: 0 auto;
        position: relative;
    }

    .mobile-bottom-nav .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: var(--text-color);
        padding: 4px 8px;
        border-radius: 8px;
        transition: all 0.3s ease;
        min-width: 60px;
        position: relative;
        flex: 1;
    }

    .mobile-bottom-nav .nav-item.active {
        color: var(--primary-color);
        background-color: rgba(47, 155, 75, 0.1);
    }

    /* Centrirano i podignuto dugme za pretragu */
    .mobile-bottom-nav .nav-item.search-item {
        position: absolute;
        left: 50%;
        top: -25px;
        transform: translateX(-50%);
        background: var(--menu-bg);
        color: var(--text-color);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        flex: 0 0 auto;
    }

    .mobile-bottom-nav .nav-icon {
        position: relative;
        margin-bottom: 4px;
    }

    .mobile-bottom-nav .nav-icon i {
        font-size: 1.2rem;
    }

    .mobile-bottom-nav .nav-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .mobile-bottom-nav .nav-label {
        font-size: 0.7rem;
        font-weight: 500;
        text-align: center;
        line-height: 1.2;
    }

    /* Podešavanje ostalih itema da budu ravnomerno raspoređeni */
    .mobile-bottom-nav .nav-item:not(.search-item) {
        margin-bottom: 5px;
    }

    /* Adjust main content padding to account for bottom nav */
    @media (max-width: 768px) {
        main.py-4 {
            padding-bottom: 90px !important;
        }
    }

    /* Ensure footer doesn't overlap with bottom nav */
    @media (max-width: 768px) {
        footer {
            margin-bottom: 80px;
        }
    }

    /* ================================
       MEDIA QUERY (MAX 768px)
    ================================= */
    @media (max-width: 768px) {
        .hamburger {
            display: flex;
        }

        .nav-links {
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            background-color: var(--menu-bg);
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-100%);
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
            z-index: 999;
        }

        .nav-links.active {
            transform: translateY(0);
            opacity: 1;
        }

        .search-container {
            width: 100%;
            margin-bottom: 15px;
        }

        .search-input {
            width: 100%;
        }

        .user-menu {
            flex-direction: column;
            width: 100%;
        }

        .user-menu li {
            width: 100%;
        }

        .user-menu li a {
            display: block;
            width: 100%;
        }

        .theme-toggle {
            align-self: flex-start;
        }

        .categories-list {
            display: contents;
        }

        .category-item {
            flex: 0 0 auto;
            border-bottom: none;
            position: relative;
            width: 100%;
        }

        .category-link {
            white-space: nowrap;
            padding: 10px 18px;
            background-color: var(--menu-bg);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            font-size: 0.85rem;
            width: 100%;
            justify-content: space-between;
        }

        .category-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .category-link.has-subcategories::after {
            content: '▼';
            font-size: 0.6rem;
            margin-left: 6px;
        }

        .category-link.active.has-subcategories::after {
            content: '▲';
        }

        .subcategories-menu {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            border: none;
            border-radius: 0;
            display: none;
            width: 100%;
            background-color: var(--dropdown-bg);
        }

        .category-item.active .subcategories-menu {
            display: block;
        }

        .subcategory-link {
            padding: 6px 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .mobile-categories-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 8px 15px;
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 0.85rem;
            cursor: pointer;
        }

        .mobile-categories-toggle .icon {
            transition: transform 0.3s;
        }

        .category-item.active .mobile-categories-toggle .icon {
            transform: rotate(180deg);
        }

        .subcategories-container {
            width: 100%;
            background-color: var(--menu-bg);
            border-top: 1px solid var(--border-color);
            display: none;
            padding: 12px 0;
        }

        .subcategories-container.active {
            display: block;
        }

        .subcategories-scroll {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding: 0 15px;
            gap: 8px;
        }

        .subcategories-scroll::-webkit-scrollbar {
            display: none;
        }

        .subcategory-link {
            white-space: nowrap;
            padding: 8px 16px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.8rem;
            flex: 0 0 auto;
        }

        .subcategory-link:hover,
        .subcategory-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .desktop-category {
            display: none !important;
        }

        .mobile-category {
            display: flex !important;
        }

        .category-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding: 0 15px;
            gap: 8px;
        }

        .category-container::-webkit-scrollbar {
            display: none;
        }

        /* ================================
        MOBILE CATEGORIES HORIZONTAL SCROLL
        ================================= */
        .categories-nav {
            display: block;
        }

        .categories-list {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 10px 15px;
            margin: 0;
            gap: 8px;
            max-height: 60px; /* Ograničava visinu da ne zauzima previše prostora */
        }

        .categories-list::-webkit-scrollbar {
            display: none;
        }

        .category-item {
            flex: 0 0 auto;
            width: auto;
            position: relative;
        }

        .category-link.mobile-category {
            white-space: nowrap;
            padding: 8px 16px;
            background-color: var(--menu-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: max-content;
        }

        .category-link.mobile-category.has-subcategories::after {
            content: '▼';
            font-size: 0.6rem;
            margin-left: 6px;
        }

        .category-link.mobile-category.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .category-link.mobile-category.active.has-subcategories::after {
            content: '▲';
        }

        /* Sakrij desktop kategorije na mobilnom */
        .desktop-category {
            display: none !important;
        }

        /* Prikaži mobile kategorije */
        .mobile-category {
            display: inline-flex !important;
        }

        /* Poboljšan stil za mobilne podkategorije */
        .mobile-subcategory {
            white-space: nowrap;
            padding: 8px 16px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.8rem;
            flex: 0 0 auto;
            text-decoration: none;
            color: var(--text-color);
            transition: all 0.3s ease;
            display: inline-block;
        }

        .mobile-subcategory:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            text-decoration: none;
        }

        .mobile-subcategory.selected {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        .subcategory-link.selected {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        /* Scroll indikator za kategorije */
        .categories-list {
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) transparent;
        }

        .categories-list::-webkit-scrollbar {
            height: 4px;
        }

        .categories-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .categories-list::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        /* ================================
           ZELENA LINIJA ZA PODKATEGORIJE
        ================================= */
        .subcategories-scroll {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) transparent;
            padding: 0 15px 8px 15px; /* Dodaj padding dole za scrollbar */
            gap: 8px;
            position: relative;
        }

        .subcategories-scroll::-webkit-scrollbar {
            height: 4px;
        }

        .subcategories-scroll::-webkit-scrollbar-track {
            background: transparent;
            margin: 0 15px;
        }

        .subcategories-scroll::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        /* Stil za pokazivanje da ima još sadržaja za skrolovanje */
        .subcategories-scroll:after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg,
                transparent 0%,
                var(--primary-color) 20%,
                var(--primary-color) 80%,
                transparent 100%);
            opacity: 0.3;
            pointer-events: none;
            display: none;
        }

        .subcategories-scroll.scrollable:after {
            display: block;
        }

        .add-service-title2 {
          margin-top: -30px !important;
          margin-left: 5px !important;
          color: var(--text-color);
          text-decoration: none;
          font-size: 0.9rem;
        }

        .justify-content-center a{
            color: var(--text-color);
            text-decoration: none;
        }

        /* ================================
           OFFCANVAS PROFILE MENU STYLES
        ================================= */
        .offcanvas {
            background-color: var(--menu-bg);
            color: var(--text-color);
        }

        .offcanvas-body li {
            background-color: var(--menu-bg);
        }

        .offcanvas-body li a{
            color: var(--text-color) !important;
            text-decoration: none !important;
        }

        .offcanvas-header button{
            color: var(--text-color) !important;
        }

        .mobile-search-modal {
            max-width: 100%;
            margin: 1rem;
            z-index: 9999 !important;
        }

        .mobile-search-modal .modal-header {
            padding: 1rem 1.25rem;
        }

        .mobile-search-modal .modal-body {
            padding: 1.25rem;
        }

        .search-input-mobile {
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
        }

        .nav-link{
            padding: inherit !important;
        }
    }

    /* ================================
       MEDIA QUERY (MIN 769px)
    ================================= */
    @media (min-width: 769px) {
        .category-container,
        .subcategories-container,
        .mobile-category {
            display: none !important;
        }

        .desktop-category {
            display: flex !important;
        }

        .mobile-bottom-nav {
            display: none !important;
        }
    }

    /* Responsive */
    @media (max-width: 480px) {
        .incoming-call-modal {
            margin: 1rem;
        }

        .call-buttons {
            gap: 8px;
        }

        .call-btn {
            min-width: 70px;
            padding: 8px 12px;
        }

        .mobile-bottom-nav .nav-item {
            min-width: 50px;
            padding: 4px 6px;
        }

        .mobile-bottom-nav .nav-item.search-item {
            width: 55px;
            height: 55px;
            top: -30px;
        }

        .mobile-bottom-nav .nav-item.search-item .nav-icon i {
            font-size: 1.3rem;
        }

        .mobile-bottom-nav .nav-label {
            font-size: 0.65rem;
        }
    }

    /* ================================
       MOBILE SEARCH MODAL FIXES - CRITICAL
    ================================= */
    #searchModalMobile {
        z-index: 9999 !important;
    }

    .modal-backdrop.show {
        z-index: 9998 !important;
    }

    .mobile-search-modal .modal-content {
        z-index: 10000 !important;
        position: relative;
    }

    /* Osiguraj da sticky header ne preklapa modal */
    .sticky-header {
        z-index: 1000;
    }

    /* Osiguraj da je modal iznad svega */
    .modal {
        z-index: 9999 !important;
    }

    /* Povećaj z-index za search dugme */
    .mobile-bottom-nav .nav-item.search-item {
        z-index: 1001 !important;
    }

    /* Spreči preklapanje sa bottom nav */
    @media (max-width: 768px) {
        .modal {
            z-index: 9999 !important;
        }

        .mobile-bottom-nav {
            z-index: 1000;
        }
    }

    /* Osiguraj da je modal vidljiv i klikabilan */
    .modal.show {
        display: block !important;
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
</head>
<body>
    <header class="sticky-header">
        <div class="container">
            <nav class="navbar">
                <div class="logo d-none d-md-flex">
                  <a class="navbar-brand" href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Poslovi Online Logo" width="116">
                  </a>
                </div>

                <div class="nav-links d-none d-md-flex">
                    <a href="javascript:void(0);" class="sixteen-font-size" onclick="openSearchModal()" id="showSearchBTN" title="Pretraži kategorije i ponude">
                        <svg width="16" height="17" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"></path>
                        </svg>
                    </a>

                    <!-- Modal za pretragu -->
                    <div id="searchModal" class="search-container" style="display: none;">
                        <span class="closeSearch" onclick="closeSearchModal()">&times;</span>
                        <form action="{{ route('home') }}" method="GET">
                            <input type="text" class="search-input" name="search" placeholder="Pretraži kategorije i ponude" value="{{ (isset($searchTerm) && empty($searchCategory)) ? $searchTerm : '' }}">
                        </form>
                    </div>

                    <ul class="user-menu">
                         @auth
                            <li class="nav-item">
                                @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                    @if(Auth::user()->package)
                                        @if($seller['countPublicService'] < Auth::user()->package->quantity)
                                            <a href="{{ route('services.create') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                        @else
                                            <a href="{{ route('packages.index') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                        @endif
                                    @else
                                            <a href="{{ route('packages.index') }}" class="add-service-title">Dodaj <mark>ponudu</mark></a>
                                    @endif
                                @endif
                            </li>

                            @if(Auth::user()->role == 'support' || Auth::user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('complaints.index') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                                        @if(isset($complaintCount) && $complaintCount > 0)
                                            <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža <span class="badge bg-danger">{{ $complaintCount }}</span>
                                        @else
                                            <i class="fas fa-balance-scale {{ request()->routeIs('complaints.index') ? 'text-danger' : '' }}"></i> Arbitraža
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                                        <i class="fas fa-ticket {{ request()->routeIs('tickets.index') ? 'text-danger' : '' }}"></i> Tiketi
                                        @if($ticketCount > 0)
                                            <span class="badge bg-danger">{{ $ticketCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            @if(Auth::user()->role == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('favorites.index') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                                        @if(isset($favoriteCount) && $favoriteCount > 0)
                                            <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno <span class="badge bg-danger">{{ $favoriteCount }}</span>
                                        @else
                                            <i class="fas fa-heart {{ request()->routeIs('favorites.index') ? 'text-danger' : '' }}"></i> Omiljeno
                                        @endif
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item" id="messages">
                                <a class="nav-link {{ request()->routeIs('messages.index') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                                    <i class="fas fa-envelope {{ request()->routeIs('messages.index') ? 'text-danger' : '' }}"></i> Poruke
                                    <!-- Dodajemo span za broj novih poruka -->
                                    <span class="badge bg-danger" id="unread-count-id-{{ Auth::user()->id }}" style="display: {{ $messagesCount > 0 ? 'inline-block' : 'none' }}">{{$messagesCount}}</span>
                                </a>
                            </li>
                            @if(Auth::user()->role == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}"" href="{{ route('cart.index') }}">
                                        @if(isset($cartCount) && $cartCount > 0)
                                            <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa <span class="badge bg-danger">{{ $cartCount }}</span>
                                        @else
                                            <i class="fas fa-shopping-cart {{ request()->routeIs('cart.index') ? 'text-danger' : '' }}"></i> Korpa
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    @if(isset($projectCount) && $projectCount > 0)
                                        <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi <span class="badge bg-danger">{{ $projectCount }}</span></a>
                                    @else
                                        <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}"><i class="fas fa-handshake {{ request()->routeIs('projects.index') ? 'text-danger' : '' }}"></i> Poslovi</a>
                                    @endif
                                </li>
                            @endif
                            @if(Auth::user()->role == 'seller')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('services.index', 'services.view') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="fas fa-file-signature {{ request()->routeIs('services.index', 'services.view') ? 'text-danger' : '' }}"></i> Ponude</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('projects.jobs') ? 'active' : '' }}" href="{{ route('projects.jobs') }}">
                                        <i class="fas fa-handshake {{ request()->routeIs('projects.jobs') ? 'text-danger' : '' }}"></i> Poslovi
                                        @if(isset($seller['countProjects']) and $seller['countProjects'] > 0)
                                            <span class="badge bg-danger">{{ $seller['countProjects'] }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('packages.index') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                                        <i class="fas fa-calendar-alt {{ request()->routeIs('packages.index') ? 'text-danger' : '' }}"></i> Plan
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link {{ request()->routeIs('deposit.form', 'profile.edit', 'invoices.index', 'affiliate.index','tickets.index') ? 'active' : '' }}" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user {{ request()->routeIs('deposit.form', 'profile.edit', 'invoices.index', 'affiliate.index','tickets.index') ? 'text-danger' : '' }}"></i> Profil
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Uredi profil</a></li>
                                    @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                        <li><a class="dropdown-item" href="{{ route('deposit.form') }}">Depozit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('invoices.index') }}">Računi</a></li>
                                        <li><a class="dropdown-item" href="{{ route('affiliate.index') }}">Preporuči i Zaradi</a></li>
                                        <li><a class="dropdown-item" href="{{ route('tickets.index') }}">Tiketi</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('subscriptions.index') }}">Pretplate</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Odjava</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->role == 'seller' or Auth::user()->role == 'buyer')
                                <!-- Switch za izbor Kupac/Prodavac -->
                                <li class="nav-item">
                                    <label class="switch">
                                        <input type="checkbox" id="roleSwitch"
                                            {{ Auth::user()->role == 'seller' ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                        <span class="label-text left">Kupac</span>
                                        <span class="label-text right">Prodavac</span>
                                    </label>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> Prijava
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> Registracija
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>

                <!-- Kartice za mobilne uređaje -->
                <div class="d-block d-md-none w-100">
                    <div class=" d-flex">
                        <a class="navbar-brand" href="/">
                            <img src="{{ asset('images/logo.png') }}" alt="Poslovi Online Logo" width="116">
                        </a>

                        @guest
                            <div class="d-flex mt-3 gap-3 ms-auto mr-3">
                                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> Prijava
                                </a>

                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> Registracija
                                </a>
                            </div>
                        @else
                            <!-- Hamburger meni ikona -->
                            <div class="d-flex justify-content-end mt-2 ms-auto mr-2">
                                <button class="theme-toggle mt-2" id="themeToggleMobile">
                                    <i class="fas fa-moon"></i>
                                </button>
                                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas" style="color: var(--text-color) !important;">
                                    Profil <i class="fas fa-bars"></i>
                                </button>
                            </div>

                            <!-- Offcanvas meni sa profil opcijama -->
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="profileOffcanvas" aria-labelledby="profileOffcanvasLabel">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title text-danger" id="profileOffcanvasLabel">
                                        <i class="fas fa-user me-2"></i>Profil
                                    </h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body p-2">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('profile.edit') }}">
                                                <i class="fas fa-user-circle me-2"></i> Uredi profil
                                            </a>
                                        </li>

                                        @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('deposit.form') }}">
                                                <i class="fas fa-wallet me-2"></i> Depozit
                                            </a>
                                        </li>
                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('invoices.index') }}">
                                                <i class="fas fa-file-invoice me-2"></i> Računi
                                            </a>
                                        </li>
                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('affiliate.index') }}">
                                                <i class="fas fa-share-alt me-2"></i> Preporuči i Zaradi
                                            </a>
                                        </li>
                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('tickets.index') }}">
                                                <i class="fas fa-ticket-alt me-2"></i> Tiketi
                                            </a>
                                        </li>
                                        @endif

                                        <li class="list-group-item border-0  py-1 px-2">
                                            <a class="text-dark" href="{{ route('subscriptions.index') }}">
                                                <i class="fas fa-calendar-check me-2"></i> Pretplate
                                            </a>
                                        </li>

                                        <li class="list-group-item border-0 mt-3">
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-poslovi w-100">
                                                    <i class="fas fa-sign-out-alt me-2"></i> Odjava
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endguest
                    </div>

                    @auth
                        <div class="d-flex justify-content-between align-items-center">
                            @if(Auth::user()->role == 'seller' || Auth::user()->role == 'buyer')
                                <div class="d-flex align-items-center ml-4">
                                    @if(!in_array(Auth::user()->role, ['support', 'admin']))
                                        @if(Auth::user()->package)
                                            @if($seller['countPublicService'] < Auth::user()->package->quantity)
                                                <a href="{{ route('services.create') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                                            @else
                                                <a href="{{ route('packages.index') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                                            @endif
                                        @else
                                            <a href="{{ route('packages.index') }}" class="add-service-title2">Dodaj <mark>ponudu</mark></a>
                                        @endif
                                    @endif
                                </div>

                                <!-- Switch za izbor Kupac/Prodavac -->
                                <div class="ms-auto mr-3">
                                    <label class="switch">
                                        <input type="checkbox" id="roleSwitch2"
                                            {{ Auth::user()->role == 'seller' ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                        <span class="label-text left">Kupac</span>
                                        <span class="label-text right">Prodavac</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endauth

                </div>
            </nav>
        </div>

        <!-- Drugi navbar: Kategorije -->
        <nav class="categories-navbar">
            <div class="categories-container">
                <div class="categories-nav">
                    <ul class="categories-list" id="categoriesList">
                        @foreach ($categories as $category)
                            <li class="category-item" data-category-id="{{ $category->id }}">
                                <!-- Desktop verzija -->
                                <a href="#" class="category-link desktop-category">
                                    {{ $category->name }}
                                    <i class="fas fa-chevron-down dropdown-icon"></i>
                                </a>

                                <!-- Mobilna verzija -->
                                <a href="#" class="category-link mobile-category
                                    {{ count($category->subcategories) > 0 ? 'has-subcategories' : '' }}"
                                    data-category-id="{{ $category->id }}"
                                    data-category-name="{{ $category->name }}">
                                    {{ $category->name }}
                                </a>

                                <!-- Desktop subcategories menu -->
                                <ul class="subcategories-menu">
                                    @foreach ($category->subcategories as $subcategory)
                                        <li class="subcategory-item">
                                            <a class="subcategory-link"
                                               href="{{ route('home', ['search' => $subcategory->name, 'category' => $category->name]) }}"
                                               data-subcategory-name="{{ $subcategory->name }}">
                                                {{ $subcategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Container za mobilne podkategorije -->
                <div class="subcategories-container" id="subcategoriesContainer">
                    <div class="subcategories-scroll" id="subcategoriesScroll">
                        <!-- Podkategorije će biti dinamički dodate ovde -->
                    </div>
                </div>
            </div>
        </nav>
    </header>

<!-- Glavni sadržaj -->
    <main class="py-4">
        @yield('content')

        <!-- Incoming Call Modal -->
        <div class="modal fade" id="incomingCallModal" tabindex="-1" aria-labelledby="incomingCallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered incoming-call-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="call-header"">
                            <div class="call-icon py-1 px-2">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="call-info">
                                <h6 class="modal-title" id="incomingCallModalLabel">Dolazni poziv</h6>
                                <span class="call-status">Poziva vas...</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="caller-info">
                            <div class="caller-avatar-container">
                                <img id="callerAvatar" src="" alt="Caller" class="caller-avatar" onerror="this.src='/storage/user/poslovi_user_avatar.png'">
                                <div class="caller-status"></div>
                            </div>
                            <div class="caller-details">
                                <h5 id="callerName" class="caller-name"></h5>
                                <p class="call-service" id="callService"></p>
                            </div>
                        </div>
                        <div class="call-buttons mt-4 d-flex justify-content-center">
                            <button class="btn btn-success me-3" id="acceptCallBtn">
                                <i class="fas fa-phone me-2"></i> Prihvati
                            </button>
                            <button class="btn btn-danger" id="declineCallBtn">
                                <i class="fas fa-phone-slash me-2"></i> Odbij
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Search Modal - PREMEŠTENO VAN HEADERA -->
    <div class="modal fade" id="searchModalMobile" tabindex="-1" aria-labelledby="searchModalMobileLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mobile-search-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="search-header">
                        <i class="fas fa-search search-modal-icon"></i>
                        <h5 class="modal-title" id="searchModalMobileLabel">Pretraži kategorije i ponude</h5>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="search-container-mobile">
                        <input type="text" class="search-input-mobile" id="searchInputMobile" placeholder="Pretraži kategorije i ponude..." value="{{ (isset($searchTerm) && empty($searchCategory)) ? $searchTerm : '' }}">
                        <button class="search-btn-mobile" type="button" onclick="performSearchMobile()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="search-suggestions" id="searchSuggestionsMobile">
                        <!-- Sugestije će biti dinamički dodate -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-block d-md-none">
        <div class="nav-container">
            <!-- Centrirano i podignuto dugme za pretragu -->
            <a class="nav-item search-item" href="javascript:void(0);" onclick="openSearchModalMobile()" title="Pretraži">
                <div class="nav-icon">
                    <i class="fas fa-search"></i>
                </div>
            </a>

            @if(Auth::user()->role == 'support' || Auth::user()->role == 'admin')
                <a class="nav-item {{ request()->routeIs('complaints.index') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-balance-scale"></i>
                        @if(isset($complaintCount) && $complaintCount > 0)
                            <span class="nav-badge">{{ $complaintCount }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Arbitraža</span>
                </a>

                <a class="nav-item {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-ticket"></i>
                        @if($ticketCount > 0)
                            <span class="nav-badge">{{ $ticketCount }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Tiketi</span>
                </a>
            @endif

            @if(Auth::user()->role == 'buyer')
                <a class="nav-item {{ request()->routeIs('favorites.index') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-heart"></i>
                        @if(isset($favoriteCount) && $favoriteCount > 0)
                            <span class="nav-badge">{{ $favoriteCount }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Omiljeno</span>
                </a>
            @endif

            <a class="nav-item {{ request()->routeIs('messages.index') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                <div class="nav-icon">
                    <i class="fas fa-envelope"></i>
                    @if(isset($messagesCount) && $messagesCount > 0)
                        <span class="nav-badge" id="unread-count-bottom-{{ Auth::user()->id }}">{{$messagesCount}}</span>
                    @endif
                </div>
                <span class="nav-label">Poruke</span>
            </a>

            @if(Auth::user()->role == 'buyer')
                <a class="nav-item {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-shopping-cart"></i>
                        @if(isset($cartCount) && $cartCount > 0)
                            <span class="nav-badge">{{ $cartCount }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Korpa</span>
                </a>

                <a class="nav-item {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-handshake"></i>
                        @if(isset($projectCount) && $projectCount > 0)
                            <span class="nav-badge">{{ $projectCount }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Poslovi</span>
                </a>
            @endif

            @if(Auth::user()->role == 'seller')
                <a class="nav-item {{ request()->routeIs('services.index', 'services.view') ? 'active' : '' }}" href="{{ route('services.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <span class="nav-label">Ponude</span>
                </a>

                <a class="nav-item {{ request()->routeIs('projects.jobs') ? 'active' : '' }}" href="{{ route('projects.jobs') }}">
                    <div class="nav-icon">
                        <i class="fas fa-handshake"></i>
                        @if(isset($seller['countProjects']) and $seller['countProjects'] > 0)
                            <span class="nav-badge">{{ $seller['countProjects'] }}</span>
                        @endif
                    </div>
                    <span class="nav-label">Poslovi</span>
                </a>

                <a class="nav-item {{ request()->routeIs('packages.index') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                    <div class="nav-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="nav-label">Plan</span>
                </a>
            @endif
        </div>
    </nav>
    @else
        <!-- Mobile Bottom Navigation ZA NEPRIJAVLJENE KORISNIKE -->
        <nav class="mobile-bottom-nav d-block d-md-none">
            <div class="nav-container">
                <!-- Theme toggle levo -->
                <button class="theme-toggle" id="themeToggleBottom">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- Search dugme sredina -->
                <a class="nav-item search-item" href="javascript:void(0);" onclick="openSearchModalMobile()" title="Pretraži kategorije i ponude">
                    <div class="nav-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </a>

                <!-- Tekst ispod search dugmeta -->
                <div class="nav-item search-text-item" style="z-index: 1010 !important">
                    <span class="search-label">Pretraži kategorije i ponude...</span>
                </div>
            </div>
        </nav>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer -->
    <footer class="text-white mt-auto" style="background-color: #9c1c2c;">
        <div class="container py-5">
            <div class="row justify-content-center"> <!-- Centrira sve kolone -->

                 <!-- Pravne informacije -->
                <div class="col-md-6 text-center">
                    <h5>Pravne informacije</h5>
                    <ul class="list-unstyled d-flex justify-content-center gap-3">
                        <li><a class="text-white" href="{{ route('terms') }}"><i class="fas fa-file-alt me-2"></i>Uslovi korišćenja</a></li>
                        <li><a class="text-white" href="{{ route('privacy-policy') }}"><i class="fas fa-shield-alt me-2"></i>Politika privatnosti</a></li>
                        <li><a class="text-white" href="{{ route('cookies') }}"><i class="fas fa-cookie-bite me-2"></i>Politika kolačića</a></li>
                    </ul>
                </div>

                <!-- Socijalne mreže -->
                <div class="col-md-3 col-10 mb-4 text-center">
                    <h5>Pratite nas</h5>
                    <div class="social-links d-flex justify-content-center gap-3 mt-4">
                        <a href="https://www.facebook.com/profile.php?id=61551626835206" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fwww.instagram.com%2Fposlovionline%2F%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExR3JyTW9lRDlqR1dCOUZzTQEeL1mP9GyDIUexLr0pTEfePBh2SH2CdFgNAzXVvXfCKZx-9FcdjEXeHQGFH6Y_aem_Oipf62RZ-wvm0udGZmYSRQ&h=AT1FjqUsQTkzNnJy8pWYebNPKfQrnquUie8OeayO4RR9IloHbZTi3_kHOVTRbAnVrm6kAopEGALeiuRPEsK5-IPbGUDn6j_aAuPPepYzyZ5mitBUFd83kI4emE3bLbtVCXE7Mw" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fwww.linkedin.com%2Fcompany%2Fposlovi-online%2F%3Ffbclid%3DIwZXh0bgNhZW0CMTAAYnJpZBExR3JyTW9lRDlqR1dCOUZzTQEeq3K6W7hPnx8d8UMx4wyI1oDXDJFZ2uwobG5a3hFl4xW33nlCEx7dglStslI_aem_qnZQKQXyLatU2oVhLNnF7A&h=AT1FjqUsQTkzNnJy8pWYebNPKfQrnquUie8OeayO4RR9IloHbZTi3_kHOVTRbAnVrm6kAopEGALeiuRPEsK5-IPbGUDn6j_aAuPPepYzyZ5mitBUFd83kI4emE3bLbtVCXE7Mw" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-top pt-4">
                <p class="text-center text-light mb-0">
                    &copy; {{ date('Y') }} Poslovi Online. Sva prava zadržana.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // jQuery adapter za Bootstrap 5 - omogućava staru sintaksu za close dugme na modalima
        $(document).ready(function() {
            // Omogući data-dismiss za modale
            $('[data-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });

            // Omogući data-toggle za modale
            $('[data-toggle="modal"]').on('click', function() {
                var target = $(this).data('target');
                $(target).modal('show');
            });
        });
    </script>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleMobile = document.getElementById('themeToggleMobile');
        const themeIcon = themeToggle.querySelector('i');

        // Proveri da li postoji element themeToggleMobile i da li sadrži ikonu
        const themeIconMobile = themeToggleMobile ? themeToggleMobile.querySelector('i') : null;

        // Sada možeš da proveriš da li postoji themeIconMobile pre nego što nastaviš sa daljim radnjama
        if (themeIconMobile) {
            const themeIconMobile = themeToggleMobile.querySelector('i');
            themeToggleMobile.addEventListener('click', () => {
                document.body.classList.toggle('dark-theme');

                if (document.body.classList.contains('dark-theme')) {
                    themeIconMobile.classList.remove('fa-moon');
                    themeIconMobile.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIconMobile.classList.remove('fa-sun');
                    themeIconMobile.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');

            if (document.body.classList.contains('dark-theme')) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });


        // Provera lokalne pohrane za temu
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Mobile categories toggle
        const mobileCategoryToggles = document.querySelectorAll('.mobile-categories-toggle');

        mobileCategoryToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const categoryItem = toggle.parentElement;
                categoryItem.classList.toggle('active');
            });
        });

        // Sakrij desktop kategorije na mobilnim uređajima
        function adjustCategoriesForScreenSize() {
            const desktopCategories = document.querySelectorAll('.desktop-category');
            const mobileToggles = document.querySelectorAll('.mobile-categories-toggle');

            if (window.innerWidth <= 768) {
                desktopCategories.forEach(cat => cat.style.display = 'none');
                mobileToggles.forEach(toggle => toggle.style.display = 'flex');
            } else {
                desktopCategories.forEach(cat => cat.style.display = 'flex');
                mobileToggles.forEach(toggle => toggle.style.display = 'none');
            }
        }

        // Pozovi funkciju pri učitavanju i promeni veličine prozora
        window.addEventListener('load', adjustCategoriesForScreenSize);
        window.addEventListener('resize', adjustCategoriesForScreenSize);

        // Inicijalno pozovi funkciju
        adjustCategoriesForScreenSize();

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            // Zatvori sve kategorije ako kliknemo van njih na mobilnom
            if (window.innerWidth <= 768) {
                const categoryItems = document.querySelectorAll('.category-item');
                if (!e.target.closest('.category-item')) {
                    categoryItems.forEach(item => {
                        item.classList.remove('active');
                    });
                }
            }
        });
    </script>

    <script type="text/javascript">
    // Mobile categories horizontal scroll functionality
    function initMobileCategories() {
        if (window.innerWidth <= 768) {
            const mobileCategories = document.querySelectorAll('.mobile-category');
            const subcategoriesContainer = document.getElementById('subcategoriesContainer');
            const subcategoriesScroll = document.getElementById('subcategoriesScroll');

            mobileCategories.forEach(category => {
                category.addEventListener('click', (e) => {
                    e.preventDefault();

                    const categoryId = category.getAttribute('data-category-id');
                    const isActive = category.classList.contains('active');

                    // Remove active class from all categories
                    mobileCategories.forEach(cat => {
                        cat.classList.remove('active');
                    });

                    // Toggle current category
                    if (!isActive) {
                        category.classList.add('active');
                        loadSubcategories(categoryId, category.textContent.trim());
                        subcategoriesContainer.classList.add('active');
                    } else {
                        subcategoriesContainer.classList.remove('active');
                    }
                });
            });

            // Close subcategories when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.categories-navbar') && !e.target.closest('#subcategoriesContainer')) {
                    mobileCategories.forEach(cat => cat.classList.remove('active'));
                    subcategoriesContainer.classList.remove('active');
                }
            });
        }
    }

    // Function to load subcategories
    function loadSubcategories(categoryId, categoryName) {
        const subcategoriesScroll = document.getElementById('subcategoriesScroll');

        // Clear previous subcategories
        subcategoriesScroll.innerHTML = '';

        // Find the desktop subcategories for this category
        const desktopSubmenu = document.querySelector(`.category-item .subcategories-menu`);
        const subcategories = desktopSubmenu ? desktopSubmenu.querySelectorAll('.subcategory-link') : [];

        if (subcategories.length > 0) {
            subcategories.forEach(subcategory => {
                const clone = subcategory.cloneNode(true);
                clone.classList.add('mobile-subcategory');
                subcategoriesScroll.appendChild(clone);
            });
        } else {
            // If no subcategories, show a message
            subcategoriesScroll.innerHTML = `<div class="text-center p-3" style="color: var(--text-color);">Nema podkategorija za ${categoryName}</div>`;
        }
    }

    // Initialize on load and resize
    window.addEventListener('load', initMobileCategories);
    window.addEventListener('resize', initMobileCategories);
    </script>

<script>
$(document).ready(function() {
    // Podaci o kategorijama i podkategorijama
    const categories = @json($categories->keyBy('id'));

    // Pročitaj selektovanu kategoriju i podkategoriju iz URL-a
    const urlParams = new URLSearchParams(window.location.search);
    const selectedCategoryName = urlParams.get('category');
    const selectedSubcategoryName = urlParams.get('search');

    // Flag da označimo da li je inicijalno učitavanje
    let initialLoad = true;

    // Funkcija za prikaz podkategorija
    function showSubcategories(categoryId, shouldCenterSubcategory = false) {
        const category = categories[categoryId];
        let subcategoriesHtml = '';

        if (category && category.subcategories && category.subcategories.length > 0) {
            category.subcategories.forEach(subcategory => {
                const isSelected = subcategory.name === selectedSubcategoryName &&
                                 category.name === selectedCategoryName;

                subcategoriesHtml += `
                    <a href="{{ route('home') }}?search=${encodeURIComponent(subcategory.name)}&category=${encodeURIComponent(category.name)}"
                       class="subcategory-link mobile-subcategory ${isSelected ? 'selected' : ''}"
                       data-subcategory-name="${subcategory.name}">
                        ${subcategory.name}
                    </a>
                `;
            });
            $('#subcategoriesScroll').html(subcategoriesHtml);
            $('#subcategoriesContainer').show();

            // Centriraj selektovanu podkategoriju samo pri inicijalnom učitavanju
            if (initialLoad && shouldCenterSubcategory && selectedSubcategoryName) {
                setTimeout(() => {
                    $(`.mobile-subcategory[data-subcategory-name="${selectedSubcategoryName}"]`).addClass('selected');
                    centerElement($(`.mobile-subcategory[data-subcategory-name="${selectedSubcategoryName}"]`), $('#subcategoriesScroll'));
                }, 100);
            }
        } else {
            $('#subcategoriesContainer').hide();
        }
    }

    // Funkcija za centriranje elementa u kontejneru
    function centerElement(element, container) {
        const containerWidth = container.width();
        const elementOffset = element.offset().left - container.offset().left;
        const elementWidth = element.outerWidth();
        const scrollPosition = elementOffset - (containerWidth / 2) + (elementWidth / 2);

        container.animate({
            scrollLeft: scrollPosition
        }, 300);
    }

    // Funkcija za centriranje kategorije - POBOLJŠANA VERZIJA
    function centerCategory(element) {
        const container = $('#categoriesList');
        const containerWidth = container.width();
        const elementOffset = element.offset().left - container.offset().left;
        const elementWidth = element.outerWidth();
        const scrollPosition = elementOffset - (containerWidth / 2) + (elementWidth / 2);

        // Koristimo instant scroll umesto animate da bi se odmah centriralo
        container[0].scrollLeft = scrollPosition;
    }

    // Inicijalno postavljanje - centriramo selektovanu kategoriju i podkategoriju
    if (selectedCategoryName) {
        let foundCategory = false;

        $('.mobile-category').each(function() {
            const categoryId = $(this).data('category-id');
            const category = categories[categoryId];

            if (category && category.name === selectedCategoryName) {
                foundCategory = true;
                $(this).addClass('selected active');

                // Centriraj selektovanu kategoriju samo pri inicijalnom učitavanju
                setTimeout(() => {
                    centerCategory($(this));
                }, 50);

                showSubcategories(categoryId, true);
                return false;
            }
        });

        if (!foundCategory) {
            const firstCategoryId = $('.mobile-category').first().data('category-id');
            $('.mobile-category').first().addClass('selected active');

            // Centriraj prvu kategoriju pri inicijalnom učitavanju
            setTimeout(() => {
                centerCategory($('.mobile-category').first());
            }, 50);

            showSubcategories(firstCategoryId);
        }
    } else {
        // Ako nema selektovane kategorije, centriraj prvu kategoriju
        setTimeout(() => {
            centerCategory($('.mobile-category').first());
        }, 50);
    }

    // Klik na mobilnu kategoriju - POBOLJŠANO
    $(document).on('click', '.mobile-category', function(e) {
        e.preventDefault();

        $('.mobile-category').removeClass('selected active');
        $(this).addClass('selected active');

        const categoryId = $(this).data('category-id');
        showSubcategories(categoryId);
    });

    // Klik na desktop kategoriju (ostaje originalno ponašanje)
    $(document).on('click', '.desktop-category', function(e) {
        e.preventDefault();
        // Ostavljamo originalno hover ponašanje za desktop
    });

    // Klik na podkategoriju - odmah preusmeravamo na novu stranicu
    $(document).on('click', '.mobile-subcategory', function(e) {
        // Ne radimo ništa posebno, koristimo standardno ponašanje linka
    });

    // Nakon inicijalnog učitavanja, postavljamo flag na false
    setTimeout(() => {
        initialLoad = false;
    }, 500);

    // Funkcije za skrolovanje slajdera strelicama (opciono)
    $('.slider-prev').click(function() {
        $('#categoriesList').animate({ scrollLeft: '-=150' }, 300);
    });

    $('.slider-next').click(function() {
        $('#categoriesList').animate({ scrollLeft: '+=150' }, 300);
    });

    // Responsive ponašanje
    function handleResponsiveLayout() {
        if (window.innerWidth <= 768) {
            $('.desktop-category').hide();
            $('.mobile-category').show();
            $('#subcategoriesContainer').show();
        } else {
            $('.desktop-category').show();
            $('.mobile-category').hide();
            $('#subcategoriesContainer').hide();
        }
    }

    // Pozovi na učitavanju i promeni veličine prozora
    handleResponsiveLayout();
    $(window).resize(handleResponsiveLayout);
});
</script>

<script type="text/javascript">
if(document.querySelector('.add-service-title')){
    document.querySelector('.add-service-title').addEventListener('click', function(event) {
        event.preventDefault(); // Sprečava default akciju (navigaciju)

        // Proveri da li je korisnik ulogovan
        @auth
            var currentRole = '{{ Auth::user()->role }}'; // PHP varijabla za trenutnu ulogu
        @endauth

        // Proveri da li korisnik nije ulogovan
        @guest
            var currentRole = ''; // Prazna varijabla ako korisnik nije ulogovan
        @endguest

        if (currentRole === 'buyer') {
            // Ako je trenutna uloga 'buyer', promeni je u 'seller'

            // Slanje AJAX zahteva za promenu uloge korisnika
            fetch('/update-role', {
                method: 'POST',
                body: JSON.stringify({ role: 'seller' }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())  // Parsira JSON odgovor
            .then(data => {
                if (data.success) {
                    console.log('Uloga uspešno promenjena!');

                    // Ažuriraj switcher da prikazuje 'seller' stanje
                    var roleSwitch = document.getElementById('roleSwitch');
                    var leftLabel = document.querySelector('.label-text.left');
                    var rightLabel = document.querySelector('.label-text.right');

                    // Postavi 'checked' za 'seller' (desna strana)
                    roleSwitch.checked = true;

                    // Ažuriraj tekst na switcher-u
                    leftLabel.textContent = 'Kupac';
                    rightLabel.textContent = 'Prodavac';

                    // Nakon uspešne promene uloge, nastavi sa navigacijom
                    var link = event.target.closest('a'); // Selektuj 'a' tag
                    if (link && link.href) {
                        window.location.href = link.href;  // Redirektuje na originalni link
                    }
                } else {
                    console.error('Došlo je do greške.');
                }
            })
            .catch(error => {
                console.error('Greška u AJAX pozivu:', error);
            });
        } else {
            // Ako je trenutna uloga 'seller' ili neka druga, samo nastavi sa navigacijom
            var link = event.target.closest('a'); // Selektuj 'a' tag
            if (link && link.href) {
                window.location.href = link.href;  // Redirektuje na originalni link
            }
        }
    });
}

if(document.querySelector('.add-service-title2')){
    document.querySelector('.add-service-title2').addEventListener('click', function(event) {
        event.preventDefault(); // Sprečava default akciju (navigaciju)

        // Proveri da li je korisnik ulogovan
        @auth
            var currentRole = '{{ Auth::user()->role }}'; // PHP varijabla za trenutnu ulogu
        @endauth

        // Proveri da li korisnik nije ulogovan
        @guest
            var currentRole = ''; // Prazna varijabla ako korisnik nije ulogovan
        @endguest

        if (currentRole === 'buyer') {
            // Ako je trenutna uloga 'buyer', promeni je u 'seller'

            // Slanje AJAX zahteva za promenu uloge korisnika
            fetch('/update-role', {
                method: 'POST',
                body: JSON.stringify({ role: 'seller' }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())  // Parsira JSON odgovor
            .then(data => {
                if (data.success) {
                    console.log('Uloga uspešno promenjena!');

                    // Ažuriraj switcher da prikazuje 'seller' stanje
                    var roleSwitch = document.getElementById('roleSwitch2');
                    var leftLabel = document.querySelector('.label-text.left');
                    var rightLabel = document.querySelector('.label-text.right');

                    // Postavi 'checked' za 'seller' (desna strana)
                    roleSwitch.checked = true;

                    // Ažuriraj tekst na switcher-u
                    leftLabel.textContent = 'Kupac';
                    rightLabel.textContent = 'Prodavac';

                    // Nakon uspešne promene uloge, nastavi sa navigacijom
                    var link = event.target.closest('a'); // Selektuj 'a' tag
                    if (link && link.href) {
                        window.location.href = link.href;  // Redirektuje na originalni link
                    }
                } else {
                    console.error('Došlo je do greške.');
                }
            })
            .catch(error => {
                console.error('Greška u AJAX pozivu:', error);
            });
        } else {
            // Ako je trenutna uloga 'seller' ili neka druga, samo nastavi sa navigacijom
            var link = event.target.closest('a'); // Selektuj 'a' tag
            if (link && link.href) {
                window.location.href = link.href;  // Redirektuje na originalni link
            }
        }
    });
}

if(document.getElementById('roleSwitch')){
    document.getElementById('roleSwitch').addEventListener('change', function() {
        var newRole = this.checked ? 'seller' : 'buyer';

        // Slanje AJAX zahteva za promenu uloge korisnika
        fetch('/update-role', {
            method: 'POST',
            body: JSON.stringify({ role: newRole }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())  // Parsira JSON odgovor
        .then(data => {
            if (data.success) {
                console.log('Uloga uspešno promenjena!');
                // Preusmeri korisnika na "profile.edit" rutu nakon promene uloge
                window.location.href = "{{ route('profile.edit') }}";  // Redirektuje na profil
            } else {
                console.error('Došlo je do greške.');
            }
        })
        .catch(error => {
            console.error('Greška u AJAX pozivu:', error);
        });
    });
}


if(document.getElementById('roleSwitch2')){
    document.getElementById('roleSwitch2').addEventListener('change', function() {
        var newRole = this.checked ? 'seller' : 'buyer';

        // Slanje AJAX zahteva za promenu uloge korisnika
        fetch('/update-role', {
            method: 'POST',
            body: JSON.stringify({ role: newRole }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())  // Parsira JSON odgovor
        .then(data => {
            if (data.success) {
                console.log('Uloga uspešno promenjena!');
                // Preusmeri korisnika na "profile.edit" rutu nakon promene uloge
                window.location.href = "{{ route('profile.edit') }}";  // Redirektuje na profil
            } else {
                console.error('Došlo je do greške.');
            }
        })
        .catch(error => {
            console.error('Greška u AJAX pozivu:', error);
        });
    });
}

function openSearchModal() {
    document.getElementById('searchModal').style.display = 'block';
    document.getElementById('showSearchBTN').style.display = 'none';
    setTimeout(() => {
        document.getElementById('searchInput').focus();
    }, 100); // Fokus na input
}

function closeSearchModal() {
    document.getElementById('searchModal').style.display = 'none';
    document.getElementById('showSearchBTN').style.display = 'block';
}

function performSearch() {
    const query = document.getElementById('searchInput').value.trim();
    if(query) {
        console.log('Pretraži:', query);
        // Ovde pozovi AJAX / redirect ili šta god ti odgovara
        closeSearchModal();
    }
}

// Zatvori modal klikom van sadržaja
window.onclick = function(event) {
    const modal = document.getElementById('searchModal');
    if (event.target === modal) {
        closeSearchModal();
    }
}
</script>

<!-- Audio/Video poziv js -->

<audio id="ringtone" loop preload="auto">
    <source src="{{ asset('storage/sounds/marimba_soft.mp3') }}" type="audio/mpeg">
</audio>

<script>
// Globalne varijable
let incomingCall = null;
let callModal = null;
let callTimeout = null;

// Inicijalizacija
document.addEventListener('DOMContentLoaded', function() {
    initializeCallSystem();
});

function initializeCallSystem() {
    // Inicijalizacija modala
    const modalElement = document.getElementById('incomingCallModal');
    if (modalElement) {
        callModal = new bootstrap.Modal(modalElement);

        // Event listeneri
        document.getElementById('acceptCallBtn').addEventListener('click', acceptIncomingCall);
        document.getElementById('declineCallBtn').addEventListener('click', declineIncomingCall);

        modalElement.addEventListener('hidden.bs.modal', function() {
            if (incomingCall) declineIncomingCall();
        });
    }
}

// Funkcije za upravljanje pozivima (iste kao gore)
function showIncomingCall(message) {
    if (incomingCall) declineIncomingCall();

    incomingCall = message;

    document.getElementById('callerAvatar').src = JSON.parse(message.call_data).caller_avatar;
    document.getElementById('callerName').textContent = JSON.parse(message.call_data).caller_name;
    document.getElementById('callService').textContent = JSON.parse(message.call_data).service_title;

    playRingtone();

    if (callModal) callModal.show();

    callTimeout = setTimeout(() => {
        if (incomingCall) {
            declineIncomingCall('missed');
            Toastify({ text: "Poziv automatski odbijen", duration: 4000 }).showToast();
        }
    }, 45000);
}

function playRingtone() {
    const ringtone = document.getElementById('ringtone');
    if (ringtone) ringtone.play().catch(e => console.log('Greška pri ringtone-u:', e));
}

function stopRingtone() {
    const ringtone = document.getElementById('ringtone');
    if (ringtone) {
        ringtone.pause();
        ringtone.currentTime = 0;
    }
}

function acceptIncomingCall() {
    if (!incomingCall) return;

    stopRingtone();
    if (callModal) callModal.hide();

    const roomUrl = incomingCall.call_data.room_url;
    // Koristimo postojeći modal za video poziv
    openExistingVideoCallModal(JSON.parse(incomingCall.call_data));

    clearTimeout(callTimeout);
    updateCallStatus(incomingCall.id, 'answered');
    incomingCall = null;
}

function openExistingVideoCallModal(call_data) {

    // Kreiraj URL za messages stranicu sa parametrima
    let redirectUrl = '{{ route("messages.index") }}?openVideoCall=true';

    redirectUrl += '&room_url='+call_data.room_url;

    // Redirektuj na messages stranicu
    window.location.href = redirectUrl;
}

function declineIncomingCall(status = null) {
    if (!incomingCall) return;

    stopRingtone();
    if (callModal) callModal.hide();

    if (typeof Toastify !== "undefined") {
        Toastify({ text: "Poziv odbijen", duration: 3000 }).showToast();
    }

    clearTimeout(callTimeout);
    if(status == 'missed'){
        updateCallStatus(incomingCall.id, 'missed');
    }else{
        updateCallStatus(incomingCall.id, 'rejected');
    }
    incomingCall = null;
}

/**
 * Ažuriraj status poziva na serveru
 */
async function updateCallStatus(messageId, callStatus, callDuration = null) {
    try {
        const response = await fetch('/messages/update-call-status', {
            method: 'POST',
            headers: {
                //'Authorization': `Bearer ${apiToken}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message_id: messageId,
                call_status: callStatus,
                call_duration: callDuration
            })
        });

        const data = await response.json();

        if (data.success) {
            console.log('Status poziva ažuriran:', callStatus);
            return true;
        } else {
            console.error('Greška pri ažuriranju statusa poziva:', data.message);
            return false;
        }
    } catch (error) {
        console.error('Greška u komunikaciji sa serverom:', error);
        return false;
    }
}
</script>

<script type="text/javascript">
// Mobile Search Modal Functions - FIXED
function openSearchModalMobile() {
    console.log('Opening mobile search modal'); // Debug
    const searchModalElement = document.getElementById('searchModalMobile');

    if (searchModalElement) {
        const searchModal = new bootstrap.Modal(searchModalElement);
        searchModal.show();

        // Fokusiraj input nakon što se modal prikaže
        setTimeout(() => {
            const searchInput = document.getElementById('searchInputMobile');
            if (searchInput) {
                searchInput.focus();
            }
        }, 500);
    } else {
        console.error('Search modal element not found');
    }
}

function closeSearchModalMobile() {
    const searchModalElement = document.getElementById('searchModalMobile');
    if (searchModalElement) {
        const searchModal = bootstrap.Modal.getInstance(searchModalElement);
        if (searchModal) {
            searchModal.hide();
        }
    }
}

function performSearchMobile() {
    const query = document.getElementById('searchInputMobile').value.trim();
    if (query) {
        console.log('Mobile pretraga:', query);
        // Ovde dodajte vašu search logiku
        window.location.href = '/?search=' + encodeURIComponent(query);
        closeSearchModalMobile();
    }
}

// Event listener za Enter tipku
document.addEventListener('DOMContentLoaded', function() {
    const searchInputMobile = document.getElementById('searchInputMobile');
    if (searchInputMobile) {
        searchInputMobile.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearchMobile();
            }
        });
    }

    // Dodatni debug
    const searchButtons = document.querySelectorAll('[onclick*="openSearchModalMobile"]');
    searchButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Search button clicked');
        });
    });
});

// Event listener za zatvaranje modala klikom van njega
document.getElementById('searchModalMobile').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSearchModalMobile();
    }
});
</script>

<script type="text/javascript">
    // Theme toggle za donju navigaciju
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBottom = document.getElementById('themeToggleBottom');
        if (themeToggleBottom) {
            themeToggleBottom.addEventListener('click', function() {
                document.body.classList.toggle('dark-theme');

                const themeIcon = this.querySelector('i');
                if (document.body.classList.contains('dark-theme')) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });

            // Ažuriraj ikonicu teme pri učitavanju
            if (localStorage.getItem('theme') === 'dark') {
                const themeIcon = themeToggleBottom.querySelector('i');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        }
    });
</script>
</body>
</html>
