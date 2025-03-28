<x-app-layout>
    <x-slot name="header">YOUR RESULT FOR EXERCISES {{strtoupper($exercises->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <h5><strong>Your Point: {{$exercises->my_result->point  }}</strong></h5>
            @foreach ($exercises->questions as $question)
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
                    @if ('answer1' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('answer1' == $question->my_answer->user_select)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->answer1}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('answer2' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('answer2' == $question->my_answer->user_answer)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->answer2}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('answer3' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('answer3' == $question->my_answer->user_answer)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->answer3}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('answer4' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('answer4' == $question->my_answer->user_answer)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->answer4}}
                    </label>
                </div>
                <div class="form-check">
                    @if ('answer5' == $question->correct_answer)
                        <i class="fa fa-check text-success"></i>
                    @elseif('answer5' == $question->my_answer->user_answer)
                        <i class="fa fa-times text-danger"></i>
                    @endif
                    <label class="form-check-label">
                        {{$question->answer5}}
                    </label>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
</x-app-layout>