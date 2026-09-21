<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ScholarshipApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ScholarshipApplicationController extends Controller
{
    public function index(Request $request, ScholarshipApplicationService $service): View
    {
        $applications = $service->listForMember($request->user(), 10)
            ->through(fn (array $application) => [
                ...Arr::only($application, ['id', 'application_number', 'student', 'period', 'session', 'application']),
                'result' => ['published' => $application['result']['published']],
            ]);

        return view('frontend.scholarship.applications.index', compact('applications'));
    }

    public function show(Request $request, int $application, ScholarshipApplicationService $service): View
    {
        $data = $service->forMember($request->user(), $application);
        $application = [
            ...Arr::only($data, ['id', 'application_number', 'student', 'period', 'session', 'application']),
            'result' => ['published' => $data['result']['published']],
            // An unpublished approval must not be revealed by its restriction.
            'restriction' => $data['restriction'] === 'read_only' && $data['application']['state'] !== 'approved'
                ? null : $data['restriction'],
        ];

        return view('frontend.scholarship.applications.show', compact('application'));
    }
}
