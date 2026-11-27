@extends('layouts.front')

@section('title', 'Payment History - ToursTravel Kenya')

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero tt-page-hero-sm">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/place-1.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Payment History</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Payment <span class="accent">History</span></h1>
    </div>
</section>

<!-- Payment History -->
<section class="tt-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-10 mx-auto" data-aos="fade-up">
                <div class="tt-sidebar-card">
                    <h4 class="mb-1"><i class="fas fa-receipt me-2"></i> Your Payment History</h4>

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

                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Destination</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $payment->payment_method == 'mpesa' ? 'primary' : 'success' }}">
                                                    {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $payment->destination ? $payment->destination->title : 'N/A' }}</small>
                                            </td>
                                            <td class="fw-bold">KSh {{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ $payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <small>{{ $payment->payment_id ? substr($payment->payment_id, 0, 8) . '...' : 'N/A' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="icon mb-3">
                                <i class="fas fa-receipt text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-muted mb-2">No payments yet</h5>
                            <p class="text-muted mb-4">When you make a payment, it will appear here.</p>
                            <a href="{{ route('packages') }}" class="btn-tt-primary">
                                <i class="fas fa-globe me-2"></i> Browse Destinations
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
