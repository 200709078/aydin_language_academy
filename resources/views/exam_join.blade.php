<x-app-layout>
    <x-slot name="header">DETAIL: {{strtoupper($exam->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('exam.result', $exam->slug) }}">
                @csrf
                @foreach ($exam->questions as $question)
                    <strong>{{ $loop->iteration }}) </strong>{{ $question->question }}
                    @if ($question->image)
                        <img src="{{ asset($question->image) }}" style="width:20%" class="img-responsive">
                    @endif
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="{{$question->id}}" id="exam_{{$question->id}}_1"
                            value="select1" required>
                        <label class="form-check-label" for="exam_{{$question->id}}_1">
                            {{$question->select1}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}" id="exam_{{$question->id}}_2"
                            value="select2" required>
                        <label class="form-check-label" for="exam_{{$question->id}}_2">
                            {{$question->select2}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}" id="exam_{{$question->id}}_3"
                            value="select3" required>
                        <label class="form-check-label" for="exam_{{$question->id}}_3">
                            {{$question->select3}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}" id="exam_{{$question->id}}_4"
                            value="select4" required>
                        <label class="form-check-label" for="exam_{{$question->id}}_4">
                            {{$question->select4}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{$question->id}}" id="exam_{{$question->id}}_5"
                            value="select5" required>
                        <label class="form-check-label" for="exam_{{$question->id}}_5">
                            {{$question->select5}}
                        </label>
                    </div>
                    <hr>
                @endforeach
                <button type="SUBMIT" class="btn btn-success btn-sm btn-block" style="width: 100%;">Check Exam</button>
            </form>
        </div>
    </div>
</x-app-layout>