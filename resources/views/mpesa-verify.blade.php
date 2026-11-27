@extends('layouts.front')

@section('title', 'Verify M-PESA Payment - ToursTravel Kenya')

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero tt-page-hero-sm">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/place-1.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Verify M-PESA Payment</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Verify <span class="accent">M-PESA Payment</span></h1>
    </div>
</section>

<!-- M-PESA Verification -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card">
                    <h4 class="mb-1"><i class="fas fa-check-circle me-2"></i> Verify Payment</h3>

                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form id="verify-form" method="POST" action="{{ route('mpesa.verify') }}">
                        @csrf
                        <input type="hidden" name="m_pesa_receipt_number" value="{{ $receipt_number }}">

                        <div class="mb-3">
                            <label class="tt-label">M-PESA Receipt Number</label>
                            <input type="text" class="tt-input" value="{{ $receipt_number }}"
                                   placeholder="e.g. 1234567890" readonly disabled>
                            <small class="text-muted">This is the receipt number from your M-PESA payment.</small>
                        </div>

                        <button type="submit" class="btn-tt-success w-100 text-center">
                            <i class="fas fa-check me-2"></i> Verify Payment
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('mpesa.stkpush.confirm') }}" class="btn-tt-outline">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection

@push('scripts')
<script>
    document.getElementById('verify-form').addEventListener('submit', function(e) {
        // Verify the receipt number
        const receipt = this.querySelector('input[name="m_pesa_receipt_number"]').value;
        if (!receipt || receipt.length < 10) {
            e.preventDefault();
            alert('Please enter a valid M-PESA receipt number.');
        }
    });
</script>
@endpush
