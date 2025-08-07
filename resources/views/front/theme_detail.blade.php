@extends('front.master')
@section('title', __('dictt.ala') . ' - ' . __('dictt.themes'))
@section('middle_section')

    <div class="container mt-5">
        <div class="container">
            @if ($themes->first())
            <div class="text-center mx-auto">
                    <h4>
                        {{ strtoupper($themes->first()->levels->name)}} <i class="bi bi-arrow-right"></i>
                            {{ strtoupper($themes->first()->sub_levels->name)}} <i class="bi bi-arrow-right"></i>
                            {{ strtoupper(Str::limit($themes->first()->name, 20))}}
                        @if (Request::segment(1) == 'tab1')
                                    <i class="bi bi-arrow-right"></i> {{strtoupper( __('dictt.declarations')) }}
                    </h4>
                        @endif

                        @if (Request::segment(1) == 'tab2')
                        <i class="bi bi-arrow-right"></i> {{strtoupper( __('dictt.exercises')) }}
                    </h4>
                        @endif

            </div>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) === 'tab1' ? 'active' : null }}" data-toggle="tab"
                        href="{{route('tab1', $themes->first()->id) }}" role="tab">{{strtoupper( __('dictt.declarations')) }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) === 'tab2' ? 'active' : null }}" data-toggle="tab"
                        href="{{route('tab2', $themes->first()->id)}}" role="tab">{{strtoupper( __('dictt.exercises')) }}</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane p-3 {{ Request::segment(1) === 'tab1' ? 'active' : null }}" id="tabs-1"
                    role="tabpanel">
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
                                            <img class="img-fluid rounded align-self-end mt-2 mb-2"
                                                src="{{ asset('photos/' . $declaration->image) }}" style="height:120px"
                                                class="img-responsive" /><br>
                                        @endif
                                        @if ($declaration->pdf)
                                            <iframe src="{{ asset('pdfs/' . $declaration->pdf) }}" width="90%" height="600px"></iframe>
                                        @endif
                                    </p>
                                    @if ($declaration->video)
                                        <a href="{{$declaration->video}}" class="btn btn-primary" target="_blank"><i
                                                class="fab fa-youtube"></i>
                                            {{__('dictt.video') }}</a>
                                    @endif
                                    @if ($declaration->voice)
                                        <a href="{{$declaration->voice}}" class="btn btn-primary" target="_blank"><i
                                                class="fab fa-youtube"></i>
                                            {{ __('dictt.voice') }}</a>
                                    @endif
                                    @if ($declaration->answerkey)
                                        <a href="{{asset('pdfs/' . $declaration->answerkey) }}" class="btn btn-primary"
                                            target="_blank"><i class="fab fa-adobe"></i>
                                            {{ __('dictt.answerkey') }}</a>
                                    @endif
                                </div>
                                <div class="card-footer text-muted">
                                    {{$declaration->created_at->diffForHumans()}}
                                </div>
                            </div>
                        @endforeach
                    <div class="mt-2"> {{ $declarations->links('pagination::bootstrap-4') }}</div>
                    </p>
                </div>
                <div class="tab-pane p-3 {{ Request::segment(1) === 'tab2' ? 'active' : null }}" id="tabs-2"
                    role="tabpanel">
                    <p>
                        @foreach ($exercises as $exercise)
                            <div class="card">
                                <div class="card-body">
                                    @if ($exercise->image)
                                        <img src="{{ asset('photos/' . $exercise->image) }}" style="width:20%"
                                            class="img-responsive"><br>
                                    @endif
                                    @if($exercise->qtext) {{ $exercise->qtext }}<br> @endif
                                    @if ($exercise->video)
                                        <a href="{{$exercise->video}}" class="btn btn-primary" target="_blank"><i
                                                class="fab fa-youtube"></i>
                                            {{ __('dictt.video') }}</a>
                                    @endif
                                    @if ($exercise->voice)
                                        <a href="{{$exercise->voice}}" class="btn btn-primary" target="_blank"><i
                                                class="fab fa-youtube"></i>
                                            {{ __('dictt.voice') }}</a>
                                    @endif
                                    <form method="POST" action="{{ route('exercises.result', $exercise->id) }}">
                                        @csrf
                                        @foreach ($exercise->questions as $question)
                                            <strong>{{ $loop->iteration }}) </strong>{{ $question->question }}<br>
                                            @if ($question->image)
                                                <img src="{{ asset('photos/' . $question->image) }}" style="width:20%"
                                                    class="img-responsive">
                                            @endif
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="radio" name="{{$question->id}}"
                                                    id="exercises_{{$question->id}}_1" value="answer1">
                                                <label class="form-check-label" for="exercises_{{$question->id}}_1">
                                                    {{$question->answer1}}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="{{$question->id}}"
                                                    id="exercises_{{$question->id}}_2" value="answer2">
                                                <label class="form-check-label" for="exercises_{{$question->id}}_2">
                                                    {{$question->answer2}}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="{{$question->id}}"
                                                    id="exercises_{{$question->id}}_3" value="answer3">
                                                <label class="form-check-label" for="exercises_{{$question->id}}_3">
                                                    {{$question->answer3}}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="{{$question->id}}"
                                                    id="exercises_{{$question->id}}_4" value="answer4">
                                                <label class="form-check-label" for="exercises_{{$question->id}}_4">
                                                    {{$question->answer4}}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="{{$question->id}}"
                                                    id="exercises_{{$question->id}}_5" value="answer5">
                                                <label class="form-check-label" for="exercises_{{$question->id}}_5">
                                                    {{$question->answer5}}
                                                </label>
                                            </div>
                                            <hr>
                                        @endforeach
                                        <button type="SUBMIT" class="btn btn-success btn-sm btn-block" style="width: 100%;">{{ __('dictt.check') }}</button>
                                    </form>
                                </div>
                            </div>

                        @endforeach
                    <div class="mt-2"> {{ $exercises->links('pagination::bootstrap-4') }}</div>
                    </p>
                </div>

                @endif
            </div>
        </div>
@endsection