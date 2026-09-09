<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipBranch;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\ViewErrorBag;

class ScholarshipDefinitionAdminTest extends ScholarshipTestCase
{
    use InteractsWithAuthentication;
    use InteractsWithSession;
    use MakesHttpRequests;

    protected Application $app;

    private string $originalLocale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = app();
        $this->originalLocale = $this->app->getLocale();
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->withSession(['locale' => 'tr']);
        $this->app->setLocale('tr');
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    protected function tearDown(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->app->setLocale($this->originalLocale);
        $this->app['view']->share('errors', new ViewErrorBag);
        parent::tearDown();
    }

    public function test_all_definition_routes_require_admin_and_reject_unknown_types_and_records(): void
    {
        $requests = [];
        foreach ($this->fixtures() as $type => $record) {
            foreach ([['GET', 'index'], ['GET', 'create'], ['POST', 'store'], ['GET', 'edit'], ['PUT', 'update'], ['PATCH', 'status.update'], ['DELETE', 'destroy']] as [$method, $action]) {
                $requests[] = [$method, $this->url($action, $type, in_array($action, ['index', 'create', 'store'], true) ? null : $record->id)];
            }
        }
        foreach ($requests as [$method, $url]) {
            $this->call($method, $url)->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($requests as [$method, $url]) {
            $this->call($method, $url)->assertForbidden();
        }
        $this->actingAs($this->admin);
        $this->get('/admin/scholarship/definitions/users')->assertNotFound();
        $this->post('/admin/scholarship/definitions/users', ['name' => 'Unknown'])->assertNotFound();
        $this->get('/admin/scholarship/definitions/branch/not-an-id/edit')->assertNotFound();
        foreach ([['GET', 'edit'], ['PUT', 'update'], ['PATCH', 'status.update'], ['DELETE', 'destroy']] as [$method, $action]) {
            $this->call($method, $this->url($action, 'branch', 99999999), ['is_active' => '1'])->assertNotFound();
        }
        self::assertSame(2, ScholarshipBranch::query()->count());
    }

    public function test_admin_can_create_edit_toggle_and_delete_each_definition_type(): void
    {
        $this->actingAs($this->admin);
        foreach ($this->fixtures() as $type => $fixture) {
            $data = $this->form($type);
            $this->get($this->url('create', $type))->assertOk()->assertSee($this->url('store', $type), false);
            $this->post($this->url('store', $type), $data)
                ->assertRedirect($this->url('index', $type))->assertSessionHas('modalSuccessContent');
            $created = $fixture::query()->where('name', $data['name'])->sole();
            self::assertTrue($created->is_active);
            self::assertSame(4, $created->sort_order);
            $edit = $this->get($this->url('edit', $type, $created->id))
                ->assertOk()->assertSee($this->url('update', $type, $created->id), false)
                ->assertSee('name="_token"', false)->assertSee('value="PUT"', false);
            if ($type === 'school') {
                $edit->assertDontSee('name="code"', false)->assertDontSee('name="address"', false);
            } elseif ($type === 'branch') {
                $edit->assertSee('name="code"', false)->assertSee('name="address"', false);
                self::assertSame('Synthetic branch address', $created->address);
            } else {
                $edit->assertSee('name="code"', false)->assertDontSee('name="address"', false);
            }
            $this->put($this->url('update', $type, $created->id), [...$data, 'name' => 'Updated '.$type, 'is_active' => '0'])
                ->assertRedirect($this->url('index', $type));
            self::assertSame('Updated '.$type, $created->fresh()->name);
            self::assertFalse($created->fresh()->is_active);
            $this->patch($this->url('status.update', $type, $created->id), ['is_active' => '1', 'name' => 'Forged rename'])
                ->assertRedirect($this->url('index', $type));
            self::assertTrue($created->fresh()->is_active);
            self::assertSame('Updated '.$type, $created->fresh()->name);
            $this->delete($this->url('destroy', $type, $created->id))
                ->assertRedirect($this->url('index', $type))->assertSessionHas('modalSuccessContent');
            self::assertNull($created->fresh());
            self::assertNotNull($fixture->fresh());
        }
    }

    public function test_invalid_forms_preserve_input_and_reject_duplicate_codes_and_invalid_status(): void
    {
        $this->actingAs($this->admin);
        $create = $this->url('create', 'branch');
        $data = $this->form('branch');
        $this->from($create)->post($this->url('store', 'branch'), [...$data, 'code' => $this->branch->code])
            ->assertRedirect($create)->assertSessionHasErrors(['code'])->assertSessionHasInput('name', $data['name']);
        $this->get($create)->assertOk()->assertSee('value="New branch"', false)->assertSee('is-invalid', false);
        $this->from($create)->post($this->url('store', 'branch'), [...$data, 'sort_order' => '2.5'])
            ->assertRedirect($create)->assertSessionHasErrors(['sort_order']);
        $this->from($create)->post($this->url('store', 'branch'), [...$data, 'name' => '', 'is_active' => 'invalid'])
            ->assertRedirect($create)->assertSessionHasErrors(['name', 'is_active']);
        $index = $this->url('index', 'branch');
        foreach ([[], ['is_active' => 'invalid']] as $invalid) {
            $this->from($index)->patch($this->url('status.update', 'branch', $this->branch->id), $invalid)
                ->assertRedirect($index)->assertSessionHasErrors(['is_active']);
        }
        self::assertTrue($this->branch->fresh()->is_active);
        self::assertSame(2, ScholarshipBranch::query()->count());
    }

    public function test_referenced_definitions_cannot_be_deleted_and_renames_preserve_student_snapshots(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->actingAs($this->admin);
        foreach ($this->fixtures() as $type => $record) {
            $index = $this->url('index', $type);
            $this->get($index)->assertOk()->assertSee(__('scholarship.definition_has_dependents'));
            $this->from($index)->delete($this->url('destroy', $type, $record->id))
                ->assertRedirect($index)->assertSessionHasErrors(['definition']);
            self::assertNotNull($record->fresh());
            $data = [...$this->form($type), 'name' => 'Renamed '.$type];
            if ($type !== 'school') {
                $data['code'] = $record->code;
            }
            $this->put($this->url('update', $type, $record->id), $data)->assertRedirect($index);
        }
        $application->refresh();
        self::assertSame('Current school', $application->school_name_snapshot);
        self::assertSame('1. Sınıf', $application->student_level_name_snapshot);
        self::assertSame('unreached', $application->application_contact_status);
        self::assertSame('Renamed school', $this->school->fresh()->name);
        self::assertSame('Renamed student_level', $this->level->fresh()->name);
    }

    public function test_translated_pages_escape_content_and_keep_definitions_separate(): void
    {
        $this->actingAs($this->admin);
        $unsafe = '<script>alert("definition")</script>';
        $this->catalog->saveDefinition($this->admin, 'branch', ['name' => $unsafe, 'address' => $unsafe], $this->branch->id);
        foreach (['tr' => 'ALA Şubeleri', 'en' => 'ALA Branches'] as $locale => $label) {
            $this->withSession(['locale' => $locale]);
            $this->get($this->url('index', 'branch'))
                ->assertOk()->assertSee($label)->assertSee($unsafe)->assertDontSee($unsafe, false)
                ->assertSee($this->url('edit', 'branch', $this->branch->id), false);
            $this->get($this->url('edit', 'branch', $this->branch->id))
                ->assertOk()->assertSee($unsafe)->assertDontSee($unsafe, false);
            $this->get($this->url('index', 'school'))->assertOk()->assertSee('Current school')->assertDontSee($unsafe);
            $this->get($this->url('index', 'student_level'))->assertOk()->assertSee('1. Sınıf');
            $this->get($this->url('index', 'exam_group'))->assertOk()->assertSee('Lise');
        }
    }

    public function test_list_filters_order_and_pagination_preserve_the_requested_scope(): void
    {
        $this->actingAs($this->admin);
        $this->catalog->saveDefinition($this->admin, 'branch', ['is_active' => false], $this->otherBranch->id);
        $index = $this->url('index', 'branch');
        $this->get($index.'?status=active')->assertOk()->assertSee('Branch A')->assertDontSee('Branch B');
        $this->get($index.'?status=inactive')->assertOk()->assertSee('Branch B')->assertDontSee('Branch A');
        $this->get($index.'?q=Branch%20B')->assertOk()->assertSee('Branch B')->assertDontSee('Branch A');
        $this->get($index.'?q=No%20matching%20branch')->assertOk()->assertSee(__('scholarship.definitions_empty'));
        foreach (range(1, 21) as $number) {
            $this->catalog->saveDefinition($this->admin, 'branch', [
                'name' => 'Paged branch '.$number, 'code' => 'paged-'.$number, 'sort_order' => $number,
            ]);
        }
        $this->get($index.'?status=active&q=Paged')
            ->assertOk()->assertSeeInOrder(['Paged branch 1', 'Paged branch 2', 'Paged branch 3'])
            ->assertSee('page=2', false)->assertSee('status=active', false)->assertSee('q=Paged', false)
            ->assertDontSee('Paged branch 21')->assertDontSee('Branch B');
        $this->get($index.'?status=active&q=Paged&page=2')->assertOk()->assertSee('Paged branch 21')->assertDontSee('Branch B');
    }

    private function fixtures(): array
    {
        return ['branch' => $this->branch, 'school' => $this->school, 'student_level' => $this->level, 'exam_group' => $this->group];
    }

    private function url(string $action, string $type, ?int $id = null): string
    {
        return route('admin.scholarship.definitions.'.$action, ['type' => $type, ...($id === null ? [] : ['definition' => $id])]);
    }

    private function form(string $type): array
    {
        return [
            'name' => 'New '.$type, 'sort_order' => '4', 'is_active' => '1',
            ...($type === 'school' ? [] : ['code' => 'http-'.$type]),
            ...($type === 'branch' ? ['address' => 'Synthetic branch address'] : []),
        ];
    }
}
