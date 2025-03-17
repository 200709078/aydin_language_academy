<x-app-layout>
    <x-slot name="header">EDIT EXAM {{$exam->title }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-secondary" title="CANCEL"><i
                        class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exams.update',$exam->id) }}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label style="font-weight: bold;">EXAM TITLE</label>
                    <input type="text" name="title" class="form-control" value="{{ $exam->title }}">
                </div>
                <div class="form-group">
                    <label style="font-weight: bold;">EXAM DESCRIPTION</label>
                    <textarea name="description" class="form-control"rows="4">{{ $exam->description }}</textarea>
                </div>
                <div class="form-group">
                    <label style="font-weight: bold;">EXAM STATUS</label>
                    <select name="status" class="form-control">
                        <option @if($exam->questions_count<4) disabled @endif @if($exam->status==='publish') selected @endif value="publish">
                            Publish
                        </option>
                        <option @if($exam->status==='unpublish') selected @endif value="unpublish">Unpublish</option>
                        <option @if($exam->status==='draft') selected @endif value="draft">Draft</option>
                    </select>
                </div>
                <div class="form-group">
                    <input id="isFinished" @if ($exam->finished_at) checked @endif type="checkbox" >
                    <label style="font-weight: bold;">Add exam end date?</label>
                </div>
                <div id="finished_input" @if (!$exam->finished_at) style="display:none" @endif class="form-group" >
                    <label style="font-weight: bold;">EXAM END DATE</label>
                    <input type="datetime-local" name="finished_at" value="{{ $exam-> finished_at }}" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm" style="width:100%; font-weight: bold;">EXAM UPDATE</button>
                </div>
            </form>
        </div>
    </div>
    <x-slot name="js">
        <script type="module">
            $('#isFinished').change(function () {
                if ($('#isFinished').is(':checked')) {
                    $('#finished_input').show();
                } else {
                    $('#finished_input').hide();
                }
            })
        </script>
    </x-slot>
</x-app-layout>