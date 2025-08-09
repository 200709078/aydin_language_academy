@extends('front.master')
@section('title', __('dictt.ala') . ' - ' . __('dictt.home'))
@section('middle_section')
  <!-- Page Header Start -->
  <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
    <h1 class="display-3 text-white mb-3 animated slideInDown">{{__('dictt.home')}}</h1>
    {{--
    <nav aria-label="breadcrumb animated slideInDown">
      <ol class="breadcrumb text-uppercase mb-0">
      <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
      <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
      <li class="breadcrumb-item text-primary active" aria-current="page">Contact</li>
      </ol>
    </nav>
    --}}

    </div>
  </div>
  <!-- Page Header End -->

      <!-- Courses Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
              {{-- <p class="d-inline-block border rounded-pill py-1 px-4">Doctors</p> --}}
                <h1>{{__('dictt.ourcourses')}}</h1>
            </div>
            <div class="row g-4">
              @foreach ($courses as $course)
                  <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item position-relative rounded overflow-hidden">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="{{ asset('photos/' . $course->image) }}">
                        </div>
                        <div class="team-text bg-light text-center p-2">
                          @if (Session::get('locale') == 'en')
                          <h5>{{ $course->title_en }}</h5>
                            <p class="text-primary">{{$course->slogan_en}}</p>
                          @else
                          <h5>{{ $course->title_tr }}</h5>
                            <p class="text-primary">{{$course->slogan_tr}}</p>
                          @endif
                            <div class="team-social text-center">
                                <a class="btn btn-square rounded-pill py-3 px-5" href="{{ route('course_detail', $course->id) }}">{{ __('dictt.details') }}</i></a>
                            </div>
                        </div>
                    </div>
                </div>
              @endforeach
            </div>
        </div>
    </div>
    <!-- Courses End -->
@endsection