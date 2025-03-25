@extends('front.master')
@section('title', 'AYDIN LANGUAGE ACADEMY - CONTACT')
@section('middle_section')

  <div class="container-xxl py-5">
    <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width: 600px;">
      <h1>CONTACT PAGE</h1>
    </div>
    <div class="row g-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @else
            <div class="alert alert-success">
                Contact Us...!
            </div>
        @endif
        <div class="my-5">
            <form method="post" action="{{route('contactpost')}}" id="contactForm">
                @csrf
                <div class="form-floating mb-2">
                    <input class="form-control" name="fullname" type="text"
                           placeholder="Fullname..." required/>
                    <label>Name & Surname :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="email" type="email" placeholder="E-Mail..." required/>
                    <label>Email Adress :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="telephone" type="tel" placeholder="Phone number..."/>
                    <label>Telephone Number :</label>
                </div>
                <div class="form-floating mb-2">
                    <input class="form-control" name="subject" type="text" placeholder="Subject..." required/>
                    <label>Subject :</label>
                </div>
                <div class="form-floating">
                    <textarea class="form-control" name="message" placeholder="Message..."
                              style="height: 12rem" required></textarea>
                    <label>Message :</label>
                </div>
                <br/>
                <button class="btn btn-primary text-uppercase" type="submit">Send</button>
            </form>
        </div>













    </div>
    </div>
  </div>

@endsection