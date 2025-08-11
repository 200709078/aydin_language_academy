@extends('front.master')
@section('title', __('dictt.ala') . ' - ' . __('dictt.ourcourses'))
@section('middle_section')

    <!-- Course Detail Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <h5>
                @if ((Session::get('locale') == 'en') || (Session::get('locale') == null))
                    {{$course->slogan_en}}
                @else
                    {{$course->slogan_tr}}
                @endif
            </h5>
            <div class="card mb-4">
                <div class="card-header">
                    <h6>
                        @if ((Session::get('locale') == 'en') || (Session::get('locale') == null))
                            {{$course->title_en}}
                        @else
                            {{$course->title_tr}}
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="{{ asset('photos/' . $course->image) }}" style="width: 40%;">
                        </div>
                        <p>
                            @if ((Session::get('locale') == 'en') || (Session::get('locale') == null))
                                {{$course->description_en}}
                            @else
                                {{$course->description_tr}}
                            @endif
                        </p>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
    <!-- Course Detail End -->
@endsection