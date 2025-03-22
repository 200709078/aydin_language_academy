@extends('front.master')
@section('title', 'AYDIN LANGUAGE ACADEMY - THEMES')
@section('middle_section')

  <div class="container-xxl py-5">
    <div class="container">
    <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
      <h1>THEMES</h1>
    </div>
    <div class="row g-4">
      @foreach ($themes as $theme)
          <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
          <div class="service-item bg-light rounded h-100 p-5">
          <h4 class="mb-3">
          {{Str::limit($theme->name, 20) }}
          </h4>
          <p class="mb-4">
          <img class="img-fluid rounded align-self-end" src="{{ $theme->image }}" style="height: 120px;" />
          </p>
          <a class="btn" href="{{ route('tab1', $theme->id) }}"><i class="fa fa-plus text-primary me-3"></i>Show Detail</a>
          </div>
          </div>
      @endforeach
    </div>
    </div>
  </div>

@endsection