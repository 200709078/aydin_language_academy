@extends('front.master')
@section('title', "AYDIN LANGUAGE ACADEMY")
@section('middle_section')

  <div class="container-xxl py-5">
    <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width: 600px;">
      <h1>{{ __('dictt.contact') }}</h1>
    </div>
    <div class="row g-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @else
            <div class="alert alert-success">
                {{ __('dictt.contact') }}
            </div>
        @endif
        <div class="my-5">
            <form method="post" action="{{route('contactpost')}}" id="contactForm">
                @csrf
                <div class="form-floating mb-2">
                    <input class="form-control" name="fullname" type="text"
                           placeholder="Fullname..." required/>
                    <label>{{ __('dictt.fullname') }} :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="email" type="email" placeholder="E-Mail..." required/>
                    <label>{{ __('dictt.email') }} :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="telephone" type="tel" placeholder="Phone number..."/>
                    <label>{{ __('dictt.phone') }} :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="subject" type="text" placeholder="Subject..." required/>
                    <label>{{ __('dictt.subject') }} :</label>
                </div>
                <div class="form-floating">
                    <textarea class="form-control" name="message" placeholder="Message..."
                              style="height: 12rem" required></textarea>
                    <label>{{ __('dictt.message') }} :</label>
                </div>
                <br/>
                <button class="btn btn-primary text-uppercase" type="submit">{{ __('dictt.send') }}</button>
            </form>
        </div>













    </div>
    </div>
  </div>

@endsection