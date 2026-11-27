@extends('layouts.front')

@section('title', 'Payment Successful - ToursTravel Kenya')

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero tt-page-hero-sm">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/place-1.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Payment Successful</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Payment <span class="accent">Successful</span></h1>
    </div>
</section>

<!-- Success Message -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h4 class="mb-3">Payment Confirmed!</h4>
                    <p class="text-muted mb-4">Your payment has been processed successfully.</p>

                    @if($method)
                        <div class="payment-method-badge mb-3">
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i> {{ ucwords(str_replace('_', ' ', $method)) }}
                            </span>
                        </div>
                    @endif

                    @if(isset($payment) && $payment)
                        <div class="mb-3">
                            <small class="text-muted">Payment ID: <code>{{ $payment->payment_id }}</code></small>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">Amount: <strong>KSh {{ number_format($amount, 2) }}</strong></small>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">
                            @if($title)
                                Destination: <strong>{{ $title }}</strong>
                            @endif
                        </small>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('packages') }}" class="btn-tt-primary">
                            <i class="fas fa-globe me-2"></i> Browse More destinations
                        </a>
                        <a href="{{ route('home') }}" class="btn-tt-outline">
                            <i class="fas fa-home me-2"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
