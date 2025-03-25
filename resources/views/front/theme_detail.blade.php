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
      <div class="card text-center">
      <div class="card-header">
      <h5>{{$declaration->title}}</h5>

      </div>
      <div class="card-body">
      <p class="card-text">
        @if ($declaration->content)
        {{$declaration->contents}}<br>
        @endif
        @if ($declaration->image)
        <img class="img-fluid rounded align-self-end mt-2 mb-2" src="{{ asset('photos/' . $declaration->image) }}"
            style="height:120px" class="img-responsive" /><br>
        @endif
          @if ($declaration->pdf)
          <iframe src="{{ asset('pdfs/'.$declaration->pdf) }}" width="90%" height="600px"></iframe>
          @endif
      </p>
      @if ($declaration->video)
      <a href="{{$declaration->video}}" class="btn btn-primary"><i class="fab fa-youtube"></i> Video</a>
      @endif
      @if ($declaration->voice)
      <a href="{{$declaration->voice}}" class="btn btn-primary"><i class="fab fa-youtube"></i> Voice</a>
      @endif
      </div>
      <div class="card-footer text-muted">
      {{$declaration->created_at->diffForHumans()}}
      </div>
      </div>
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