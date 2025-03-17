<x-app-layout>
    <x-slot name="header">ANA SAYFA</x-slot>
    <div class="row">
        <div class="col-md-8">
            <div class="list-group">
                @foreach ($exams as $exam)
                    <a href="{{ route('exam.detail', $exam->slug) }}"
                        class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $exam->title }}</h5>
                            <small>{{ $exam->finished_at ? $exam->finished_at->diffForHumans() : null}}</small>
                        </div>
                        <p class="mb-1">
                            {{ Str::limit($exam->description, 100)}}
                        </p>
                        <small>{{ $exam->questions_count }} question</small>
                    </a>
                @endforeach
                <div class="mt-2">{{ $exams->links() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    RESULTS OF THE EXAMS YOU JOINED
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($my_results as $my_result)
                        <li class="list-group-item">
                            <a href="{{ route('exam.detail', $my_result->exam->slug) }}">{{$my_result->exam->title}}</a><br>
                            <strong> ({{ $my_result->point }} point)</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>