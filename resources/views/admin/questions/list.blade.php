<x-app-layout>
    <x-slot name="header">QUESTIONS LIST OF {{ $exercise->title }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title" >
                <a href="{{ route('exercises_list',$exercise->theme_id) }}" class="btn btn-sm btn-secondary" title="Back to Exercises List"><i
                        class="fa fa-arrow-left"></i> Back to Exercises List</a>
                <a href="#" class="btn btn-sm btn-primary"
                    title="Add Question"><i class="fa fa-plus"></i> Add Question</a>
            </h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">QUESTIONS</th>
                        <th scope="col">PHOTO</th>
                        <th scope="col">ANSWER 1</th>
                        <th scope="col">ANSWER 2</th>
                        <th scope="col">ANSWER 3</th>
                        <th scope="col">ANSWER 4</th>
                        <th scope="col">ANSWER 5</th>
                        <th scope="col">CORRECT ANSWER</th>
                        <th scope="col">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exercise->questions as $question)
                        <tr>
                            <th scope="row">{{ $question->question }}</th>
                            <td>
                                @if ($question->image)
                                    <a href="{{ asset($question->image) }}" target="_blank" class=" btn btn-sm btn-light">Show
                                        Image</a>
                                @else
                                    <button type="button" class="btn btn-sm btn-light" disabled>No Image</button>
                                @endif
                            </td>
                            <td>{{ $question->answer1 }}</td>
                            <td>{{ $question->answer2 }}</td>
                            <td>{{ $question->answer3 }}</td>
                            <td>{{ $question->answer4 }}</td>
                            <td>{{ $question->answer5 }}</td>
                            <td>{{ substr($question->correct_answer, -1) }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" title="Edit Question"><i class="fa fa-pen"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" title="Delete Question"><i class="fa fa-times"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>