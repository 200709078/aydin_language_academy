<x-app-layout>
    <x-slot name="header">LIST OF EXAMS</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title float-right">
                <a href="{{ route('exams.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Create Exam</a>
            </h5>
            <form method="GET">
                <div class="form-row">
                    <div class="col-md-2">
                        <input type="text" name="title" value="{{request()->get('title')}}" placeholder="Exam Name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" onchange="this.form.submit()" name="status">
                            <option value="">Select Status</option>
                            <option @if(request()->get('status')=='draft') selected @endif value="draft">Draft</option>
                            <option @if(request()->get('status')=='publish') selected @endif value="publish">Publish</option>
                            <option @if(request()->get('status')=='unpublish') selected @endif value="unpublish">Unpublish</option>
                        </select>
                    </div>
                    @if(request()->get('title')|| request()->get('status'))
                        <div class="col-md-2">
                        <a href="{{ route('exams.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                    @endif
                </div>
            </form>
            
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">EXAM TITLE</th>
                        <th scope="col">EXAM DESCRIPTION</th>
                        <th scope="col">NUMBER OF QUESTION</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">FINISHED AT</th>
                        <th scope="col">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exams as $exam)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $exam->title }}</th>
                            <td class="col-md-3">{{\Illuminate\Support\Str::limit($exam->description, 50, $end='...')}}</td>
                            <td class="col-md-2">{{ $exam->questions_count}}</td>
                            <td>
                                @switch($exam->status)
                                    @case('publish')
                                        @if (!$exam->finished_at || $exam->finished_at>now())
                                            <span class="badge bg-success">Publish</span>
                                        @else
                                            <span class="badge bg-secondary">Expired</span>
                                        @endif
                                        @break
                                    @case('unpublish')
                                        <span class="badge bg-danger">Unpublish</span>
                                        @break
                                    @case('draft')
                                        <span class="badge bg-warning text-dark">Draft</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <span title="{{ $exam->finished_at }}">
                                    {{$exam->finished_at ? $exam->finished_at->diffForHumans():'-'}}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('exams.details',$exam->id) }}" @if($exam->questions_count==0) class="btn btn-sm btn-secondary disabled" @else class="btn btn-sm btn-secondary" @endif title="Show Detail">
                                    <i class="fa fa-info w-4"></i>
                                </a>
                                <a href="{{ route('questions.index', $exam->id) }}" class="btn btn-sm btn-warning" title="List Questions"><i
                                        class="fa fa-list w-4"></i></a>
                                <a href="{{ route('exams.edit', $exam->id) }}" class="btn btn-sm btn-primary" title="Edit Exam"><i
                                        class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('exams.destroy', $exam->id) }}" class="btn btn-sm btn-danger" title="Delete Exam"><i
                                        class="fa fa-times w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $exams->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>