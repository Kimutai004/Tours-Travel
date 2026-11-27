@extends('layouts.front')

@section('title', 'MPESA Payment - ToursTravel Kenya')

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
                <li class="breadcrumb-item active">M-PESA Payment</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">M-PESA <span class="accent">Payment</span></h1>
    </div>
</section>

<!-- M-PESA Payment Form -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card">
                    <h4 class="mb-1"><i class="fas fa-mobile-alt me-2"></i> M-PESA Payment Details</h3>

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

                    <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-1"></i> Order Summary</h6>
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ $title }}</span>
                        <span><strong>KSh {{ number_format($amount, 2) }}</strong></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Party Name</span>
                        <span><strong>{{ $party_name }}</strong></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Transaction ID</span>
                        <span><code>{{ $transaction_id }}</code></span>
                    </div>

                    <form id="mpesa-form" method="POST" action="{{ route('mpesa.stkpush.submit') }}">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="party_name" value="{{ $party_name }}">

                        <div class="mb-3">
                            <label class="tt-label">M-PESA Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone', $phone) }}"
                                   class="tt-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                   placeholder="+254 7XX XXX XXX" required>
                            @if($errors->has('phone'))
                                <div class="tt-error">{{ $errors->first('phone') }}</div>
                            @endif
                            <small class="text-muted">Enter your M-PESA registered phone number (with country code, e.g. +254700000000)</small>
                        </div>

                        <div class="mb-3">
                            <label class="tt-label">Account Reference / Party Name *</label>
                            <input type="text" name="party_name" value="{{ old('party_name', $party_name) }}"
                                   class="tt-input {{ $errors->has('party_name') ? 'is-invalid' : '' }}"
                                   placeholder="e.g. TripToMara" required>
                            @if($errors->has('party_name'))
                                <div class="tt-error">{{ $errors->first('party_name') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn-tt-primary w-100 text-center">
                            <i class="fas fa-paper-plane me-2"></i> Initiate M-PESA Payment
                        </button>

                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i> You will receive an SMS prompt from M-PESA. Enter your M-PESA PIN to complete the payment.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection

@push('scripts')
<script>
    document.getElementById('mpesa-form').addEventListener('submit', function(e) {
        // Client-side phone number validation
        const phone = this.querySelector('input[name="phone"]').value;
        const phoneRegex = /^(\+?[0-9\s\-\+\(\)]{10,})$/;
        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Please enter a valid M-PESA phone number.');
        }
    });
</script>
@endpush
