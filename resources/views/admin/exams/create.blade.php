<x-app-layout>
    <x-slot name="header">CREATE EXAM</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-secondary" title="CANCEL"><i
                        class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exams.store') }}">
                @csrf
                <div class="form-group">
                    <label>EXAM TITLE</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>EXAM DESCRIPTION</label>
                    <textarea name="description" class="form-control"rows="4">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <input id="isFinished" @if (old('finished_at')) checked @endif type="checkbox" >
                    <label>Add exam end date?</label>
                </div>
                <div id="finished_input" @if (!old('finished_at')) style="display:none" @endif class="form-group" >
                    <label>EXAM END DATE</label>
                    <input type="datetime-local" name="finished_at" value="{{ old('finished_at') }}" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm btn-block">EXAM CREATE</button>
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