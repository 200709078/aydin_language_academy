<x-app-layout>
    <x-slot name="header">DETAIL: {{strtoupper($exam->title) }} </x-slot>
    <div class="card">
        <div class="card-body">
            <p class="card-text">
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-group">
                        @if ($exam->my_rank)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Your Exam Rank: <span class="badge badge-socondary badge-pill"
                                    style="color:green;">{{$exam->my_rank }}</span>
                            </li>
                        @endif
                        @if ($exam->my_result)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Your Point: <span class="badge badge-socondary badge-pill"
                                    style="color:red;">{{$exam->my_result->point }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Correct Answers Number: <span class="badge badge-socondary badge-pill"
                                    style="color:red;">{{$exam->my_result->correct_number }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Wrong Answers Number: <span class="badge badge-socondary badge-pill"
                                    style="color:red;">{{$exam->my_result->wrong_number }}</span>
                            </li>
                        @endif
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
                    @if ($exam->my_result)
                        <a href="{{ route('exam.join', $exam->slug) }}" class="btn btn-warning btn-block btn-sm"
                            style="width: 100%;">See to Exam</a>
                    @elseif($exam->finished_at > now())
                        <a href="{{ route('exam.join', $exam->slug) }}" class="btn btn-primary btn-block btn-sm"
                            style="width: 100%;">Join to Exam</a>
                    @endif
                </div>
            </div>
            </p>
        </div>
    </div>
</x-app-layout>