<x-app-layout>
    <x-slot name="header">{{ $question->question }} EDIT</x-slot>
    <div class="card">
        <div class="card-body">
             <form method="POST" action="{{ route('questions.update',[$question->exercises_model_id,$question->id]) }}" enctype="multipart/form-data"> 
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label style="font-weight: bold;">Question</label>
                    <textarea name="question" class="form-control" rows="4">{{ $question->question}}</textarea>
                </div>
                <div class="form-group">
                    <label style="font-weight: bold;">QUESTION PHOTO</label>
                    @if ($question->image)
                    <a href="{{ asset($question->image) }}" target="blank"><img src="{{ asset($question->image) }}" class="img-responsive" style="width: 300px;"></a>
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Answer 1</label>
                            <textarea name="answer1" class="form-control" rows="2">{{ $question->answer1 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Answer 2</label>
                            <textarea name="answer2" class="form-control" rows="2">{{ $question->answer2 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Answer 3</label>
                            <textarea name="answer3" class="form-control" rows="2">{{ $question->answer3 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Answer 4</label>
                            <textarea name="answer4" class="form-control" rows="2">{{ $question->answer4 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Answer 5</label>
                            <textarea name="answer5" class="form-control" rows="2">{{ $question->answer5 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Correct Answer</label>
                            <select name="correct_answer" class="form-control">
                                <option @if ($question->correct_answer==='answer1') selected @endif value="answer1">Answer 1</option>
                                <option @if ($question->correct_answer==='answer2') selected @endif value="answer2">Answer 2</option>
                                <option @if ($question->correct_answer==='answer3') selected @endif value="answer3">Answer 3</option>
                                <option @if ($question->correct_answer==='answer4') selected @endif value="answer4">Answer 4</option>
                                <option @if ($question->correct_answer==='answer5') selected @endif value="answer5">Answer 5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm" style="width:100%; font-weight: bold;">UPDATE QUESTION</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>