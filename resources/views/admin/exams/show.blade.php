<x-app-layout>
    <x-slot name="header">DETAIL: {{strtoupper($exam->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <p class="card-text">
            <h5 class="card-title">
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-secondary" title="Back to Exam List"><i
                        class="fa fa-arrow-left"></i> Back to Exam List</a>
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Number of Question: <span class="badge badge-socondary badge-pill"
                                style="color:red;">{{ $exam->questions_count }}</span>
                        </li>
                        @if ($exam->finished_at)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Last Date of Participation: <span title="{{ $exam->finished_at }}"
                                    class="badge badge-socondary badge-pill"
                                    style="color:red;">{{ $exam->finished_at->diffForHumans() }}</span>
                            </li>
                        @endif
                        @if ($exam->details)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Number of Participants: <span class="badge badge-socondary badge-pill"
                                    style="color:red;">{{ $exam->details['join_count']}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Avarage Points: <span class="badge badge-socondary badge-pill"
                                    style="color:red;">{{ $exam->details['average']}}</span>
                            </li>
                        @endif
                    </ul>
                    @if (count($exam->topTen) > 0)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">TOP TEN</h5>
                                <ul class="list-group">
                                    @foreach ($exam->topTen as $tUsers)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong class="h5">{{ $loop->iteration }}. </strong>
                                            <img class="w-8 h-8 rounded-full" src="{{ $tUsers->user->profile_photo_url }}">
                                            <span @if (auth()->user()->id == $tUsers->user_id) class="text-success"
                                            @endif>{{ $tUsers->user->name }}</span>
                                            <span class="badge badge-socondary badge-pill"
                                                style="color:green;">{{ $tUsers->point}}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    {{$exam->description}}

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Point</th>
                                <th scope="col">Number of Correct</th>
                                <th scope="col">Number of Wrong</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exam->results as $result)
                                <tr>
                                    <td>{{ $result->user->name }}</td>
                                    <td>{{ $result->point }}</td>
                                    <td>{{ $result->correct_number }}</td>
                                    <td>{{ $result->wrong_number }}</td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>

                </div>
            </div>
            </p>
        </div>
    </div>
</x-app-layout>