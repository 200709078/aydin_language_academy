<x-app-layout>
    <x-slot name="header">{{ __('dictt.exerciseslist') }} - {{ Str::limit($theme->name,20) }}</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary"><i
                    class="fa fa-arrow-left"></i> {{ __('dictt.backtothemeslist') }} </a>
            <a href="{{ route('exercise_create',$theme->id) }}" class="btn btn-sm btn-primary float-right"><i class="fa fa-plus"></i> {{ __('dictt.addnewexercises') }}</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.title') }}</th>
                        <th scope="col">{{ __('dictt.textofquestions') }}</th>
                        <th scope="col">{{ __('dictt.image') }}</th>
                        <th scope="col">{{ __('dictt.videolink') }}</th>
                        <th scope="col">{{ __('dictt.voicelink') }}</th>
                        <th scope="col">{{ __('dictt.numberofquestions') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($theme->exercises as $exercise)
                        <tr class="align-middle">
                            <th class="col-md-2" scope="row">{{ $exercise->title }}</th>
                            <th class="col-md-2" scope="row">{{ Str::limit($exercise->qtext, 20) }}</th>
                            <th class="col-md-2" scope="row">
                                @if($exercise->image)
                                    <a href="{{ asset('photos/' . $exercise->image) }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ asset('photos/' . $exercise->image) }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                    <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                        style="width:120px" class="img-responsive">
                                @endif
                            </th>
                            <th class="col-md-2" scope="row">{{ $exercise->video }}</th>
                            <th class="col-md-2" scope="row">{{ $exercise->voice }}</th>
                            <th class="col-md-2" scope="row">{{ $exercise->details['questions_count'] }}</th>
                            <th>
                                <!-- exercises.detail -->
<!--                                 <a href="#" @if($exercise->details['questions_count']==0) class="btn btn-sm btn-secondary disabled" @else class="btn btn-sm btn-secondary" @endif title="Show Detail">
                                    <i class="fa fa-info w-4"></i>
                                </a> -->
                                <!-- questions.index -->
                                <a href="{{ route('questions_list',$exercise->id) }}" class="btn btn-sm btn-warning" title="{{ __('dictt.questionslist') }}">
                                    <i class="fa fa-list w-4"></i></a>
                                <a href="{{ route('exercise_edit',$exercise->id) }}" class="btn btn-sm btn-primary" title="{{ __('dictt.edit') }}">
                                    <i class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('exercises_destroy',$exercise->id) }}" class="btn btn-sm btn-danger" title="{{ __('dictt.delete') }}">
                                    <i class="fa fa-trash w-4"></i></a>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>