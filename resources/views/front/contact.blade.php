@extends('front.master')
@section('title', __('dictt.ala') . ' - ' . __('dictt.contact'))
@section('middle_section')

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <!-- <h1 class="display-3 mb-3 animated slideInDown" style="color: #25bebc;">{{__('dictt.contact')}}</h1> -->
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container-xxl py-5">
        <!-- Success Start -->
        @if ((session('modalSuccessTitle') && session('modalSuccessContent')) || ($modalSuccessTitle && $modalSuccessContent))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">
                    <i class="fas fa-check-circle me-2"></i>
                    {!! session('modalSuccessTitle') ?? $modalSuccessTitle !!}
                </h4>
                <p>{!! session('modalSuccessContent') ?? $modalSuccessContent !!}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Success End  -->
        <!-- Errors Start -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <h5 class="alert-heading d-flex align-items-center mb-2">
                    <i class="fas fa-times-circle me-2"></i>
                    {{ __('dictt.errors') }}
                </h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Errors End -->

        <div class="container">
            <div class="row g-4">
                <div class="card-header">
                    <h6>
                        {{ __('dictt.contactus') }}
                    </h6>
                </div>
                <div class="my-5">
                    <form method="post" action="{{route('contactpost')}}" id="contactForm">
                        @csrf
                        <div class="form-floating mb-2">
                            <input class="form-control" name="fullname" type="text"
                                placeholder="{{ __('dictt.fullname') }}" value="{{ old('fullname') }}"/>
                            <label>{{ __('dictt.fullname') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="email" type="email" placeholder="{{ __('dictt.email') }}"value= "{{ old('email') }}"/>
                            <label>{{ __('dictt.email') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="telephone" type="tel" placeholder="{{ __('dictt.phone') }}" value="{{ old('telephone') }}"/>
                            <label>{{ __('dictt.phone') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="subject" type="text"
                                placeholder="{{ __('dictt.subject') }}" value="{{ old('subject') }}"/>
                            <label>{{ __('dictt.subject') }} :</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control" name="message" placeholder="{{ __('dictt.message') }}"
                                style="height: 12rem">{{ old('message') }}</textarea>
                            <label>{{ __('dictt.message') }} :</label>
                        </div>
                        <br />
                        <button class="btn btn-primary text-uppercase" type="submit">{{ __('dictt.send') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection