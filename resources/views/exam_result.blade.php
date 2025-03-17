<x-app-layout>
    <x-slot name="header">YOUR RESULT FOR EXAM {{strtoupper($exam->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <h5><strong>Your Point: {{$exam->my_result->point  }}</strong></h5>
            @foreach ($exam->questions as $question)
                <strong><small>Correct Percent: %{{ $question->percent_true }}</small></strong><br>
                @if ($question->correct_answer == $question->my_answer->user_select)
                    <i class="fa fa-check text-success"></i>
                @else
                    <i class="fa fa-times text-danger"></i>
                @endif
                <strong>{{ $loop->iteration }}) </strong>
                {{ $question->question }}
                @if ($question->image)
                    <img src="{{ asset($question->image) }}" style="width:20%" class="img-responsive">
                @endif
                <div class="form-check mt-2">
                    @if ('select1' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('select1' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->select1}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('select2' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('select2' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->select2}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('select3' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('select3' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->select3}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('select4' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('select4' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->select4}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('select5' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('select5' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->select5}}
                    </label>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
</x-app-layout>