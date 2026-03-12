<?php

namespace Tests\Feature;

use App\Livewire\Admin\Applications\Show as ApplicationShow;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;

class AdminApplicationsShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_component_and_loads_expected_relations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()
            ->for($user, 'student')
            ->for(AttachmentOpportunity::factory()->create(), 'opportunity')
            ->create();

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()])
            ->assertStatus(200);

        $loaded = $component->get('application');
        $this->assertTrue($loaded->relationLoaded('student'));
        $this->assertTrue($loaded->student->relationLoaded('studentProfile'));
        $this->assertTrue($loaded->student->relationLoaded('mentorships'));
        $this->assertTrue($loaded->student->relationLoaded('placements'));
        $this->assertTrue($loaded->relationLoaded('opportunity'));
        $this->assertTrue($loaded->opportunity->relationLoaded('organization'));
        $this->assertTrue($loaded->opportunity->relationLoaded('applications'));
        $this->assertTrue($loaded->relationLoaded('placement'));
        $this->assertTrue($loaded->relationLoaded('history'));
    }

    /** @test */
    public function load_activity_logs_merges_activity_and_history_and_limits_to_20_sorted_desc()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()->create();

        // Fake ActivityLog model if exists in app; otherwise create records
        // We'll create 3 activity logs newer than 2 history entries to assert ordering and merge
        ActivityLog::factory()->count(3)->create([
            'subject_id' => $application->id,
            'subject_type' => Application::class,
            'created_at' => now()->subMinutes(5),
        ]);

        // Create 25 history records to test limit and ordering
        $application->history()->createMany(collect(range(1, 25))->map(function ($i) use ($user) {
            return [
                'user_id' => $user->id,
                'old_status' => 'pending',
                'new_status' => 'approved',
                'old_status_label' => 'Pending',
                'new_status_label' => 'Approved',
                'action' => 'status_changed',
                'action_icon' => 'fa-check',
                'notes' => 'Note '.$i,
                'metadata' => ['i' => $i],
                'created_at' => now()->subMinutes($i),
            ];
        })->toArray());

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);

        $component->call('loadActivityLogs');

        $activityLogs = $component->get('activityLogs');
        $this->assertInstanceOf(Collection::class, $activityLogs);
        $this->assertLessThanOrEqual(20, $activityLogs->count());

        // Ensure items have required shape (merged from two sources)
        $first = $activityLogs->first();
        $this->assertTrue(isset($first->id) && isset($first->type) && isset($first->created_at));

        // Ensure sorted desc by created_at
        $timestamps = $activityLogs->pluck('created_at')->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->timestamp : strtotime($d));
        $sorted = $timestamps->sortDesc()->values();
        $this->assertSame($sorted->all(), $timestamps->values()->all());
    }

    /** @test */
    public function activity_log_defaults_icon_and_color_when_missing_properties()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()->create();

        // Create activity log with no icon/color in properties
        ActivityLog::factory()->create([
            'subject_id' => $application->id,
            'subject_type' => Application::class,
            'properties' => [],
        ]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->call('loadActivityLogs');
        $log = $component->get('activityLogs')->firstWhere('type', 'activity');
        $this->assertNotNull($log);
        $this->assertEquals('fa-history', $log->icon);
        $this->assertEquals('secondary', $log->color);
    }

    /** @test */
    public function history_items_map_expected_properties_and_color()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()->create();

        $history = $application->history()->create([
            'user_id' => $user->id,
            'old_status' => 'pending',
            'new_status' => 'approved',
            'old_status_label' => 'Pending',
            'new_status_label' => 'Approved',
            'action' => 'status_changed',
            'action_icon' => 'fa-check',
            'notes' => 'Note',
            'metadata' => ['k' => 'v'],
        ]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->call('loadActivityLogs');

        $item = $component->get('activityLogs')->firstWhere('id', 'history_'.$history->id);
        $this->assertNotNull($item);
        $this->assertEquals('history', $item->type);
        $this->assertEquals('fa-check', $item->icon);
        $this->assertEquals('status_changed', $item->properties['action']);
        $this->assertArrayHasKey('color', $item->properties);
    }

    /** @test */
    public function render_returns_expected_view()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()->create();

        Livewire::test(ApplicationShow::class, ['application' => $application])
            ->assertViewIs('livewire.admin.applications.show');
    }

    /** @test */
    public function open_status_modal_with_invalid_status_does_not_open_modal()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()
            ->for($user, 'student')
            ->for(AttachmentOpportunity::factory()->create(), 'opportunity')
            ->create();

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->call('openStatusModal', 'not_a_real_status');

        $this->assertFalse($component->get('showStatusModal'));
        $this->assertNull($component->get('newStatus'));
    }

    /** @test */
    public function open_status_modal_disallowed_transition_does_not_open_modal()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()
            ->for($user, 'student')
            ->for(AttachmentOpportunity::factory()->create(), 'opportunity')
            ->create(['status' => \App\Enums\ApplicationStatus::REJECTED->value]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->call('openStatusModal', \App\Enums\ApplicationStatus::UNDER_REVIEW->value);

        $this->assertFalse($component->get('showStatusModal'));
        $this->assertNull($component->get('newStatus'));
    }

    /** @test */
    public function update_status_to_under_review_sets_reviewed_at_and_adds_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $application = Application::factory()
            ->for($user, 'student')
            ->for(AttachmentOpportunity::factory()->create(), 'opportunity')
            ->create(['status' => \App\Enums\ApplicationStatus::SUBMITTED->value]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->set('newStatus', \App\Enums\ApplicationStatus::UNDER_REVIEW->value);
        $component->set('statusNotes', 'Reviewed quickly');
        $component->call('updateStatus');

        $application->refresh();
        $this->assertNotNull($application->reviewed_at);
        $this->assertEquals(\App\Enums\ApplicationStatus::UNDER_REVIEW->value, $application->status->value);
        $this->assertGreaterThanOrEqual(1, $application->history()->count());
    }

    /** @test */
    public function update_status_to_hired_updates_student_profile_to_placed_and_sets_dates_from_offer_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->studentProfile()->create([
            'attachment_status' => 'applied',
        ]);

        $application = Application::factory()
            ->for($user, 'student')
            ->for(AttachmentOpportunity::factory()->create(), 'opportunity')
            ->create([
                'status' => \App\Enums\ApplicationStatus::OFFER_ACCEPTED->value,
                'offer_details' => [
                    'start_date' => now()->addWeek()->toDateString(),
                    'end_date' => now()->addWeeks(8)->toDateString(),
                ],
            ]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $application->fresh()]);
        $component->set('newStatus', \App\Enums\ApplicationStatus::HIRED->value);
        $component->set('statusNotes', 'Welcome aboard');
        $component->call('updateStatus');

        $profile = $user->studentProfile()->first()->fresh();
        $this->assertEquals('placed', $profile->attachment_status);
        $this->assertEquals($application->offer_details['start_date'], optional($profile->attachment_start_date)->toDateString());
        $this->assertEquals($application->offer_details['end_date'], optional($profile->attachment_end_date)->toDateString());
    }

    /** @test */
    public function similar_applications_excludes_rejected_and_includes_same_opportunity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $opportunity = AttachmentOpportunity::factory()->create();
        $base = Application::factory()->for($user, 'student')->for($opportunity, 'opportunity')->create([
            'status' => \App\Enums\ApplicationStatus::SUBMITTED->value,
        ]);

        $allowed = Application::factory()->for(User::factory(), 'student')->for($opportunity, 'opportunity')->create([
            'status' => \App\Enums\ApplicationStatus::UNDER_REVIEW->value,
        ]);
        $rejected = Application::factory()->for(User::factory(), 'student')->for($opportunity, 'opportunity')->create([
            'status' => \App\Enums\ApplicationStatus::REJECTED->value,
        ]);

        $component = Livewire::test(ApplicationShow::class, ['application' => $base->fresh()]);
        $similar = $component->get('similarApplications');

        $this->assertTrue($similar->contains('id', $allowed->id));
        $this->assertFalse($similar->contains('id', $rejected->id));
    }
}
