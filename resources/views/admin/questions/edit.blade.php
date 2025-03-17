<x-app-layout>
    <x-slot name="header">{{ $question->question }} EDIT</x-slot>
    <div class="card">
        <div class="card-body">
             <form method="POST" action="{{ route('questions.update',[$question->exams_model_id,$question->id]) }}" enctype="multipart/form-data"> 
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
                            <label style="font-weight: bold;">Select 1</label>
                            <textarea name="select1" class="form-control" rows="2">{{ $question->select1 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Select 2</label>
                            <textarea name="select2" class="form-control" rows="2">{{ $question->select2 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Select 3</label>
                            <textarea name="select3" class="form-control" rows="2">{{ $question->select3 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Select 4</label>
                            <textarea name="select4" class="form-control" rows="2">{{ $question->select4 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Select 5</label>
                            <textarea name="select5" class="form-control" rows="2">{{ $question->select5 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: bold;">Correct Answer</label>
                            <select name="correct_answer" class="form-control">
                                <option @if ($question->correct_answer==='select1') selected @endif value="select1">Select 1</option>
                                <option @if ($question->correct_answer==='select2') selected @endif value="select2">Select 2</option>
                                <option @if ($question->correct_answer==='select3') selected @endif value="select3">Select 3</option>
                                <option @if ($question->correct_answer==='select4') selected @endif value="select4">Select 4</option>
                                <option @if ($question->correct_answer==='select5') selected @endif value="select5">Select 5</option>
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