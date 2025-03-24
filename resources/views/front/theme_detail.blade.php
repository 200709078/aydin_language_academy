@extends('front.master')
@section('title', 'AYDIN LANGUAGE ACADEMY - THEME DETAIL')
@section('middle_section')

  <div class="container mt-5">
    <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link {{ Request::segment(1) === 'tab1' ? 'active' : null }}" data-toggle="tab"
      href="{{route('tab1', $theme_id) }}" role="tab">DECLARATIONS</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Request::segment(1) === 'tab2' ? 'active' : null }}" data-toggle="tab"
      href="{{route('tab2', $theme_id)}}" role="tab">EXECISES</a>
    </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
    <div class="tab-pane p-3 {{ Request::segment(1) === 'tab1' ? 'active' : null }}" id="tabs-1" role="tabpanel">
      <p>
      @foreach ($declarations as $declaration)
        {{$declaration->title}}<br>
      @endforeach
      <div class="mt-2">{{ $declarations->links('pagination::bootstrap-4') }}</div>
      </p>
    </div>
    <div class="tab-pane p-3 {{ Request::segment(1) === 'tab2' ? 'active' : null }}" id="tabs-2" role="tabpanel">
      <p>
      @foreach ($exercises as $exercise)
        {{$exercise->title}}<br>
      @endforeach
      <div class="mt-2"> {{ $exercises->links('pagination::bootstrap-4') }}</div>
      </p>
    </div>
    </div>
  </div>
@endsection