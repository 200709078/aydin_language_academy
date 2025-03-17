<x-app-layout>
    <x-slot name="header">CREATE NEW QUESTION FOR {{ $exam->title }}</x-slot>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('questions.store',$exam->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Question</label>
                    <textarea name="question" class="form-control" rows="4">{{ old('question') }}</textarea>
                </div>
                <div class="form-group">
                    <label>PHOTO</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select 1</label>
                            <textarea name="select1" class="form-control" rows="2">{{ old('select1') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select 2</label>
                            <textarea name="select2" class="form-control" rows="2">{{ old('select2') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select 3</label>
                            <textarea name="select3" class="form-control" rows="2">{{ old('select3') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select 4</label>
                            <textarea name="select4" class="form-control" rows="2">{{ old('select4') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select 5</label>
                            <textarea name="select5" class="form-control" rows="2">{{ old('select5') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <select name="correct_answer" class="form-control">
                                <option @if (old('correct_answer')==='select1') selected @endif value="select1">Select 1</option>
                                <option @if (old('correct_answer')==='select2') selected @endif value="select2">Select 2</option>
                                <option @if (old('correct_answer')==='select3') selected @endif value="select3">Select 3</option>
                                <option @if (old('correct_answer')==='select4') selected @endif value="select4">Select 4</option>
                                <option @if (old('correct_answer')==='select5') selected @endif value="select5">Select 5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm btn-block">ADD QUESTION</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>