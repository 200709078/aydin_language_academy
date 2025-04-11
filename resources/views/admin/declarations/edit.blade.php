<x-app-layout>
    <x-slot name="header">EDIT DECLARATIONS FOR {{Str::upper(Str::limit($theme->name, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('declarations_list', $theme->id) }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('declaration_update', $theme->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>TITLE</label>
                    <input type="text" name="title" class="form-control">
                </div>
                <div class="form-group">
                    <label>CONTENTS</label>
                    <input type="text" name="contents" class="form-control">
                </div>
                <div class="form-group">
                    <label>IMAGE</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>PDF</label>
                    <input type="file" name="pdf" class="form-control">
                </div>
                <div class="form-group">
                    <label>VODEO LINK</label>
                    <input type="text" name="video" class="form-control">
                </div>
                <div class="form-group">
                    <label>VOICE LINK</label>
                    <input type="text" name="voice" class="form-control">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">DECLARATION UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>