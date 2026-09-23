<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ScholarshipApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScholarshipApplicationController extends Controller
{
    public function index(Request $request, ScholarshipApplicationService $service): View
    {
        $applications = $service->listForMember($request->user(), 10)
            ->through(fn (array $application) => [
                ...Arr::only($application, ['id', 'application_number', 'student', 'period', 'session', 'application']),
                'result' => $application['result'],
            ]);

        return view('frontend.scholarship.applications.index', compact('applications'));
    }

    public function show(Request $request, int $application, ScholarshipApplicationService $service): View
    {
        $data = $service->forMember($request->user(), $application);
        $application = [
            ...Arr::only($data, ['id', 'application_number', 'student', 'period', 'session', 'application', 'can_edit', 'can_delete']),
            'result' => $data['result'],
            // An unpublished approval must not be revealed by its restriction.
            'restriction' => $data['restriction'] === 'read_only' && $data['application']['state'] !== 'approved'
                ? null : $data['restriction'],
        ];

        return view('frontend.scholarship.applications.show', compact('application'));
    }

    public function update(Request $request, int $application, ScholarshipApplicationService $service): RedirectResponse
    {
        $service->forMember($request->user(), $application);
        $data = Arr::only($request->post(), ['session_id', 'school_id', 'student_level_id']);

        try {
            $service->update($request->user(), $application, $data, asMember: true);
        } catch (ValidationException $exception) {
            return to_route('frontend.scholarship.applications.edit', $application)
                ->withErrors($exception->errors())
                ->withInput(array_filter($data, fn ($value) => is_string($value) || is_int($value)));
        }

        return to_route('frontend.scholarship.applications.show', $application)
            ->with('scholarship_notice', __('scholarship.application_updated'));
    }

    public function destroy(Request $request, int $application, ScholarshipApplicationService $service): RedirectResponse
    {
        $service->forMember($request->user(), $application);

        try {
            $service->delete($request->user(), $application, asMember: true);
        } catch (ValidationException $exception) {
            return to_route('frontend.scholarship.applications.show', $application)->withErrors($exception->errors());
        }

        return to_route('frontend.scholarship.applications.index')
            ->with('scholarship_notice', __('scholarship.application_deleted'));
    }
}
