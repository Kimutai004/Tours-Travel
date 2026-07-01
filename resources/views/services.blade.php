@extends('layouts.front')

@section('title', 'Our Services - ToursTravel Kenya')

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/services-banner.jpg') }}');"></div>

    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item active">Services</li>
            </ol>
        </nav>

        <h1 class="tt-page-title">
            Our <span class="accent">Travel Services</span>
        </h1>

        <p class="tt-page-subtitle">
            We provide complete travel solutions designed to make every journey
            seamless, memorable, and stress-free. Whether you're planning a safari,
            family holiday, honeymoon, business trip, or weekend getaway, we've got
            you covered.
        </p>
    </div>
</section>

<!-- Services -->
<section class="py-5">
    <div class="container">

        <div class="row g-4">

            <!-- Safari Tours -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-paw fa-3x text-warning mb-3"></i>
                        <h4>Safari Tours</h4>
                        <p class="text-muted">
                            Experience Kenya's breathtaking wildlife through
                            customized safari packages to renowned national parks
                            and game reserves.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hotel Booking -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-hotel fa-3x text-primary mb-3"></i>
                        <h4>Hotel Booking</h4>
                        <p class="text-muted">
                            We partner with top hotels, lodges, and resorts to
                            provide comfortable accommodation at competitive rates.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flight Booking -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-plane fa-3x text-danger mb-3"></i>
                        <h4>Flight Booking</h4>
                        <p class="text-muted">
                            Get affordable domestic and international flight
                            bookings with flexible travel options.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Airport Transfers -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-shuttle-van fa-3x text-success mb-3"></i>
                        <h4>Airport Transfers</h4>
                        <p class="text-muted">
                            Reliable airport pick-up and drop-off services for
                            individuals, families, and corporate travelers.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Holiday Packages -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-map-marked-alt fa-3x text-info mb-3"></i>
                        <h4>Holiday Packages</h4>
                        <p class="text-muted">
                            Discover carefully crafted local and international
                            holiday packages tailored to your preferences.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Car Hire -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-car fa-3x text-secondary mb-3"></i>
                        <h4>Car Hire</h4>
                        <p class="text-muted">
                            Rent comfortable vehicles with or without professional
                            drivers for business or leisure travel.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5 bg-light">
    <div class="container">

        <div class="text-center mb-5">
            <h2>Why Travel With Us?</h2>
            <p class="text-muted">
                We are committed to delivering unforgettable travel experiences.
            </p>
        </div>

        <div class="row text-center">

            <div class="col-md-3 mb-4">
                <i class="fas fa-award fa-3x text-warning mb-3"></i>
                <h5>Experienced Team</h5>
                <p>Professional travel consultants with years of industry experience.</p>
            </div>

            <div class="col-md-3 mb-4">
                <i class="fas fa-wallet fa-3x text-success mb-3"></i>
                <h5>Affordable Pricing</h5>
                <p>Competitive travel packages without compromising quality.</p>
            </div>

            <div class="col-md-3 mb-4">
                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                <h5>24/7 Support</h5>
                <p>Dedicated customer support before, during, and after your trip.</p>
            </div>

            <div class="col-md-3 mb-4">
                <i class="fas fa-globe-africa fa-3x text-danger mb-3"></i>
                <h5>Tailor-Made Trips</h5>
                <p>Personalized itineraries designed around your travel goals.</p>
            </div>

        </div>

    </div>
</section>

<!-- CTA -->
<section class="tt-cta" data-aos="zoom-in">
	<div class="container text-center">
		<i class="fas fa-compass fa-3x mb-3" style="color: var(--tt-accent);"></i>
		<h2>Ready to Explore Kenya?</h2>
		<p>Let our local expertise guide you through an authentic Kenyan adventure.</p>
		<div class="d-flex gap-3 justify-content-center flex-wrap">
			<a href="{{ route('packages') }}" class="btn-tt-white"><i class="fas fa-globe-africa me-2"></i> View Destinations</a>
			<a href="{{ route('contact') }}" class="btn-tt-outline" style="border-color:white;color:white;"><i class="fas fa-phone me-2"></i> Plan My Trip</a>
		</div>
	</div>
</section>

@include('partials.footer')
@endsection