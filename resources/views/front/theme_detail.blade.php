@extends('front.master')
@section('title', 'AYDIN LANGUAGE ACADEMY - THEME DETAIL')
@section('middle_section')

  <div class="container mt-5">
    <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link {{ Request::segment(1) === 'tab1' ? 'active' : null }}" data-toggle="tab" href="{{route('tab1', $theme->id) }}" role="tab">First Panel</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Request::segment(1) === 'tab2' ? 'active' : null }}" data-toggle="tab" href="{{route('tab2', $theme->id)}}" role="tab">Second Panel</a>
    </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
    <div class="tab-pane p-3 {{ Request::segment(1) === 'tab1' ? 'active' : null }}" id="tabs-1" role="tabpanel">
      <p>{{$theme->title}}</p>
    </div>
    <div class="tab-pane p-3 {{ Request::segment(1) === 'tab2' ? 'active' : null }}" id="tabs-2" role="tabpanel">
      <p>{{$theme->slug}}</p>
    </div>
    </div>
  </div>
@endsection