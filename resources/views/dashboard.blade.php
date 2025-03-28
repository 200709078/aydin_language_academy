<x-app-layout>
    <x-slot name="header">MAIN PAGE</x-slot>
    <div class="row">
        <div class="col-md-8">
            <div class="list-group">
                @foreach ($exercises as $exer)
                    <a href="{{ route('exercises.detail', $exer->slug) }}"
                        class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $exer->title }}</h5>
                            <small>FINISHED AT</small>
                        </div>
                        <p class="mb-1">
                            {{ Str::limit($exer->description, 100)}}
                        </p>
                        <small>{{ $exer->questions_count }} question</small>
                    </a>
                @endforeach
                <div class="mt-2">{{ $exercises->links() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    RESULTS OF THE EXERCISES YOU JOINED
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($my_results as $my_result)
                        <li class="list-group-item">
                            <a href="{{ route('exercises.detail', $my_result->exercises->slug) }}">{{$my_result->exercises->title}}</a><br>
                            <strong> ({{ $my_result->point }} point)</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>