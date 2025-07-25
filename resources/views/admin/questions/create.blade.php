<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewquestion') }} - {{ $exercise->title }}</x-slot>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('question_store',$exercise->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.question') }}</label>
                    <textarea name="question" class="form-control" rows="4">{{ old('question') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.answer') }} 1</label>
                            <textarea name="answer1" class="form-control" rows="2">{{ old('answer1') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.answer') }} 2</label>
                            <textarea name="answer2" class="form-control" rows="2">{{ old('answer2') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.answer') }} 3</label>
                            <textarea name="answer3" class="form-control" rows="2">{{ old('answer3') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.answer') }} 4</label>
                            <textarea name="answer4" class="form-control" rows="2">{{ old('answer4') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.answer') }} 5</label>
                            <textarea name="answer5" class="form-control" rows="2">{{ old('answer5') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('dictt.correctanswer') }}</label>
                            <select name="correct_answer" class="form-control">
                                <option @if (old('correct_answer')==='answer1') selected @endif value="answer1">Answer 1</option>
                                <option @if (old('correct_answer')==='answer2') selected @endif value="answer2">Answer 2</option>
                                <option @if (old('correct_answer')==='answer3') selected @endif value="answer3">Answer 3</option>
                                <option @if (old('correct_answer')==='answer4') selected @endif value="answer4">Answer 4</option>
                                <option @if (old('correct_answer')==='answer5') selected @endif value="answer5">Answer 5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm btn-block mt-3">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>