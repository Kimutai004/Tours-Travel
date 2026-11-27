@extends('layouts.front')

@section('title', 'Confirm M-PESA Payment - ToursTravel Kenya')

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
                <li class="breadcrumb-item active">Confirm M-PESA Payment</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Confirm <span class="accent">M-PESA Payment</span></h1>
    </div>
</section>

<!-- M-PESA Confirmation -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card">
                    <h4 class="mb-1"><i class="fas fa-mobile-alt me-2"></i> M-PESA Confirmation</h3>

                    @if(session('mpesa_success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('mpesa_success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-1"></i> Payment Summary</h6>
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ $title }}</span>
                        <span><strong>KSh {{ number_format($amount, 2) }}</strong></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Destination</span>
                        <span><strong>{{ $title }}</strong></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Party Name</span>
                        <span><strong>{{ $party_name }}</strong></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Transaction ID</span>
                        <span><code>{{ $transaction_id }}</code></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phone Number</span>
                        <span><strong>{{ $phone }}</strong></span>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2"><i class="fas fa-hand-pointer me-2"></i> Next Steps</h6>
                        <ol class="mb-0" style="margin-bottom: 0;">
                            <li>You will receive an SMS prompt on your phone</li>
                            <li>Open M-PESA on your phone</li>
                            <li>Enter your M-PESA PIN when prompted</li>
                            <li>Payment will be confirmed automatically</li>
                            <li>You will be redirected to the success page</li>
                        </ol>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('mpesa.stkpush') }}" class="btn-tt-outline">
                            <i class="fas fa-arrow-left me-2"></i> Back to Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
