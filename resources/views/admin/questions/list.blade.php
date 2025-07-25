<x-app-layout>
    <x-slot name="header">{{ __('dictt.questionslist') }} - {{ $exercise->title }} </x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exercises_list', $exercise->theme_id) }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.backtoexerciseslist') }}</a>
                <a href="{{ route('question_create', $exercise->id) }}" class="btn btn-sm btn-primary float-right">
                    <i class="fa fa-plus"></i> {{ __('dictt.addnewquestion') }}</a>
            </h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.questions') }}</th>
                        <th scope="col">{{ __('dictt.image') }}</th>
                        <th scope="col">{{ __('dictt.answer') }} 1</th>
                        <th scope="col">{{ __('dictt.answer') }} 2</th>
                        <th scope="col">{{ __('dictt.answer') }} 3</th>
                        <th scope="col">{{ __('dictt.answer') }} 4</th>
                        <th scope="col">{{ __('dictt.answer') }} 5</th>
                        <th scope="col">{{ __('dictt.correctanswer') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exercise->questions as $question)
                        <tr>
                            <th scope="row">{{ $question->question }}</th>
                            <td>
                                @if($question->image)
                                    <a href="{{ asset('photos/' . $question->image) }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ asset('photos/' . $question->image) }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                            style="width:120px" class="img-responsive">
                                @endif
                            </td>
                            <td>{{ $question->answer1 }}</td>
                            <td>{{ $question->answer2 }}</td>
                            <td>{{ $question->answer3 }}</td>
                            <td>{{ $question->answer4 }}</td>
                            <td>{{ $question->answer5 }}</td>
                            <td>{{ $question->correct_answer }}</td>
                            <td>
                                <a href="{{ route('question_edit',$question->id) }}" class="btn btn-sm btn-primary" title="{{ __('dictt.edit') }}"><i
                                        class="fa fa-pen"></i></a>
                                <a href="{{ route('question_destroy', [$exercise->id, $question->id]) }}"
                                    class="btn btn-sm btn-danger" title="{{ __('dictt.delete') }}"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>