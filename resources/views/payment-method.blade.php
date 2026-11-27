@extends('layouts.front')

@section('title', 'Select Payment Method - ToursTravel Kenya')

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero tt-page-hero-sm">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/place-1.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart') }}">Cart</a></li>
                <li class="breadcrumb-item active">Select Payment Method</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Choose <span class="accent">Payment Method</span></h1>
    </div>
</section>

<!-- Payment Method Selection -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card">
                    <h4 class="mb-1"><i class="fas fa-lock me-2"></i> Secure Payments</h3>

                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- M-PESA Option -->
                        <div class="col-md-6">
                            <div class="payment-card">
                                <div class="payment-icon mb-3">
                                    <i class="fas fa-mobile-alt text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <h5 class="payment-title">M-PESA</h5>
                                <p class="payment-desc">Pay via your M-PESA account using STK Push. Enter your M-PESA phone number.</p>
                                <ul class="payment-benefits list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Instant confirmation</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>No card required</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Secure M-PESA encryption</li>
                                </ul>
                                <a href="{{ route('mpesa.stkpush') }}" class="btn-tt-primary w-100 mt-3">
                                    <i class="fas fa-mobile me-2"></i> Pay with M-PESA
                                </a>
                            </div>
                        </div>

                        <!-- Card Option -->
                        <div class="col-md-6">
                            <div class="payment-card">
                                <div class="payment-icon mb-3">
                                    <i class="fas fa-credit-card text-success" style="font-size: 2.5rem;"></i>
                                </div>
                                <h5 class="payment-title">Credit/Debit Card</h5>
                                <p class="payment-desc">Pay securely using your credit or debit card via Stripe.</p>
                                <ul class="payment-benefits list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Global payment method</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Encrypted SSL transactions</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Instant booking confirmation</li>
                                </ul>
                                <a href="{{ route('stripe') }}" class="btn-tt-success w-100 mt-3">
                                    <i class="fas fa-credit-card me-2"></i> Pay with Card
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            <i class="fas fa-shield-alt me-1"></i> All transactions are secure and encrypted
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
