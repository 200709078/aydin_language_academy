<x-app-layout>
    <x-slot name="header">DETAIL: {{strtoupper($exercises->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('exercises.result', $exercises->slug) }}">
                @csrf
                @foreach ($exercises->questions as $question)
                    <strong>{{ $loop->iteration }}) </strong>{{ $question->question }}
                    @if ($question->image)
                        <img src="{{ asset('photos/' . $question->image) }}" style="width:20%" class="img-responsive">
                    @endif
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="{{$question->id}}"
                            id="exercises_{{$question->id}}_1" value="answer1" required>
                        <label class="form-check-label" for="exercises_{{$question->id}}_1">
                            {{$question->answer1}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}"
                            id="exercises_{{$question->id}}_2" value="answer2" required>
                        <label class="form-check-label" for="exercises_{{$question->id}}_2">
                            {{$question->answer2}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}"
                            id="exercises_{{$question->id}}_3" value="answer3" required>
                        <label class="form-check-label" for="exercises_{{$question->id}}_3">
                            {{$question->answer3}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}"
                            id="exercises_{{$question->id}}_4" value="answer4" required>
                        <label class="form-check-label" for="exercises_{{$question->id}}_4">
                            {{$question->answer4}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}"
                            id="exercises_{{$question->id}}_5" value="answer5" required>
                        <label class="form-check-label" for="exercises_{{$question->id}}_5">
                            {{$question->answer5}}
                        </label>
                    </div>
                    <hr>
                @endforeach
                <button type="SUBMIT" class="btn btn-success btn-sm btn-block" style="width: 100%;">Check
                    Exercises</button>
            </form>
        </div>
    </div>
</x-app-layout>