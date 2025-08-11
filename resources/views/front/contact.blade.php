@extends('front.master')
@section('title', __('dictt.ala') . ' - ' . __('dictt.contact'))
@section('middle_section')

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 mb-3 animated slideInDown" style="color: #25bebc;">{{__('dictt.contact')}}</h1>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container-xxl py-5">
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
                            <input class="form-control" name="fullname" type="text" placeholder="{{ __('dictt.fullname') }}"
                                required />
                            <label>{{ __('dictt.fullname') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="email" type="email" placeholder="{{ __('dictt.email') }}"
                                required />
                            <label>{{ __('dictt.email') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="telephone" type="tel" placeholder="{{ __('dictt.phone') }}" />
                            <label>{{ __('dictt.phone') }} :</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input class="form-control" name="subject" type="text" placeholder="{{ __('dictt.subject') }}"
                                required />
                            <label>{{ __('dictt.subject') }} :</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control" name="message" placeholder="{{ __('dictt.message') }}"
                                style="height: 12rem" required></textarea>
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