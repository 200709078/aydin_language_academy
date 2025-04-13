<x-app-layout>
    <x-slot name="header">UPDATE QUESTION</x-slot>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('question_update',$question->id) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>Question</label>
                    <textarea name="question" class="form-control" rows="4">{{ $question->question }}</textarea>
                </div>
                <div class="form-group">
                    <label>PHOTO</label>
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
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer 1</label>
                            <textarea name="answer1" class="form-control" rows="2">{{ $question->answer1 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer 2</label>
                            <textarea name="answer2" class="form-control" rows="2">{{ $question->answer2 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer 3</label>
                            <textarea name="answer3" class="form-control" rows="2">{{ $question->answer3 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer 4</label>
                            <textarea name="answer4" class="form-control" rows="2">{{ $question->answer4 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer 5</label>
                            <textarea name="answer5" class="form-control" rows="2">{{ $question->answer5 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correct Answer</label>
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
                    <button type="submit" class="btn btn-success btn-sm btn-block mt-3">UPDATE QUESTION</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>