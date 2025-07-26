@extends('front.master')
@section('title', "AYDIN LANGUAGE ACADEMY")
@section('middle_section')

  <div class="container-xxl py-5">
    <div class="container">
    @if ($themes->first() != null)
    <div class="text-center mx-auto">
      <h4>{{strtoupper($themes->first()->levels->name)}} <i class="bi bi-arrow-right"></i>
      {{ strtoupper($themes->first()->sub_levels->name)}} <i class="bi bi-arrow-right"></i> {{strtoupper( __('dictt.themes')) }}
      </h4>
    </div>
    <div class="row g-4">
      @foreach ($themes as $theme)
      <div class="col-lg-4 col-md-6">
      <div class="service-item bg-light rounded h-100 p-5">
      <h4 class="mb-3">
      {{Str::limit($theme->name, 20) }}
      </h4>
      <p class="mb-4">
      <img class="img-fluid rounded align-self-end" src="{{ asset('photos/' . $theme->image) }}"
      style="height:120px" class="img-responsive" />
      </p>
      <a class="btn" href="{{ route('tab1', $theme->id) }}"><i class="fa fa-plus text-primary me-3"></i>{{ __('dictt.details') }}</a>
      </div>
      </div>
    @endforeach
    </div>
    @else
    <div class="text-center mx-auto">
      <h4>{{ __('dictt.themenotfound') }}</h4>
    </div>
    @endif
    </div>
  </div>

@endsection