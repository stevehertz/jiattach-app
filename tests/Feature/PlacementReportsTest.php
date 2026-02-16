<?php

namespace Tests\Feature;

use App\Livewire\Admin\Reports\PlacementReports;
use App\Models\Application;
use App\Models\Organization;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class PlacementReportsTest extends TestCase
{
    use RefreshDatabase;

    private function seedBasicData()
    {
        // Create organizations and opportunities relation on the fly using query builder fakes
        // Minimal seeding for Applications and StudentProfiles sufficient for aggregation logic
        $users = User::factory()->count(4)->create();

        // Map genders on users for whereHas('student') queries
        foreach ($users as $index => $user) {
            StudentProfile::create([
                'user_id' => $user->id,
                'course_name' => $index % 2 === 0 ? 'CS' : 'IT',
                'institution_name' => $index < 2 ? 'Uni A' : 'Uni B',
                'gender' => $index === 0 ? 'male' : ($index === 1 ? 'female' : null),
            ]);
        }

        // Hired and non-hired applications across different months
        $now = Carbon::now();
        $dates = [
            $now->copy()->subMonths(1),
            $now->copy()->subMonths(2),
            $now->copy()->subMonths(3),
            $now->copy()->subMonths(4),
        ];

        foreach ($users as $i => $user) {
            // submitted_at for stats and duration analysis; updated_at for hired moment
            Application::create([
                'student_id' => $user->id,
                'status' => $i < 3 ? 'hired' : 'pending',
                'submitted_at' => $dates[$i]->copy()->subDays(10),
                'updated_at' => $dates[$i],
                'created_at' => $dates[$i]->copy()->subDays(10),
            ]);
        }

        // Minimal organizations graph for top companies calculation
        $org = Organization::create(['company_name' => 'Acme Inc']);
        // Create a fake relation records using DB since factories are not provided
        DB::table('attachment_opportunities')->insert([
            'id' => 1,
            'organization_id' => $org->id,
            'title' => 'Intern',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('applications')->insert([
            'student_id' => $users[0]->id,
            'attachment_opportunity_id' => 1,
            'status' => 'hired',
            'submitted_at' => now()->subDays(5),
            'created_at' => now()->subDays(5),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_initializes_with_default_dates_and_loads_data()
    {
        Livewire::test(PlacementReports::class)
            ->assertSet('viewType', 'monthly')
            ->assertSet('placementStats.total_applications', 0)
            ->assertSet('placementTrends.labels', function ($labels) { return is_array($labels) && count($labels) === 12; });
    }

    /** @test */
    public function filtering_by_dates_reloads_and_dispatches_event()
    {
        $now = Carbon::now();
        Livewire::test(PlacementReports::class)
            ->set('startDate', $now->copy()->subMonths(2)->format('Y-m-d'))
            ->set('endDate', $now->format('Y-m-d'))
            ->call('filter')
            ->assertDispatched('placementChartsUpdated');
    }

    /** @test */
    public function placement_stats_compute_conversion_and_avg_time_safely()
    {
        $this->seedBasicData();

        $component = Livewire::test(PlacementReports::class);
        $stats = $component->get('placementStats');

        $this->assertArrayHasKey('conversion_rate', $stats);
        $this->assertArrayHasKey('avg_placement_time', $stats);
        $this->assertIsNumeric($stats['conversion_rate']);
        $this->assertIsNumeric($stats['avg_placement_time']);
    }

    /** @test */
    public function view_type_weekly_generates_week_labels()
    {
        $component = Livewire::test(PlacementReports::class)
            ->set('viewType', 'weekly');

        $trends = $component->get('placementTrends');
        $this->assertArrayHasKey('labels', $trends);
        $this->assertNotEmpty($trends['labels']);
        $this->assertStringStartsWith('Week ', $trends['labels'][0]);
    }

    /** @test */
    public function duration_analysis_buckets_days_correctly()
    {
        $this->seedBasicData();

        $component = Livewire::test(PlacementReports::class);
        $durations = $component->get('placementDurationAnalysis');

        $this->assertSame(['0-7 days','8-14 days','15-30 days','31-60 days','61-90 days','91+ days'], $durations['labels']);
        $this->assertCount(6, $durations['data']);
        $this->assertEquals($durations['total'], array_sum($durations['data']));
    }

    /** @test */
    public function top_performing_month_and_gender_breakdown_are_computed()
    {
        $this->seedBasicData();
        $component = Livewire::test(PlacementReports::class);
        $stats = $component->get('placementStats');

        // top_performing_month can be null if no hires in window; gender breakdown always present
        $this->assertArrayHasKey('top_performing_month', $stats);
        $this->assertArrayHasKey('placement_by_gender', $stats);
        $gender = $stats['placement_by_gender'];
        $this->assertArrayHasKey('male', $gender);
        $this->assertArrayHasKey('female', $gender);
        $this->assertArrayHasKey('other', $gender);
        $this->assertArrayHasKey('total', $gender);
        $this->assertEquals(
            $gender['total'],
            $gender['male']['count'] + $gender['female']['count'] + $gender['other']['count']
        );
    }

    /** @test */
    public function top_companies_include_opportunities_and_success_rate()
    {
        $this->seedBasicData();
        $component = Livewire::test(PlacementReports::class);
        $companies = $component->get('topPlacementCompanies');

        $this->assertIsIterable($companies);
        if (count($companies) > 0) {
            $first = $companies[0];
            $this->assertArrayHasKey('name', $first);
            $this->assertArrayHasKey('placements_count', $first);
            $this->assertArrayHasKey('opportunities_count', $first);
            $this->assertArrayHasKey('success_rate', $first);
            $this->assertIsNumeric($first['success_rate']);
        }
    }

    /** @test */
    public function placement_by_course_and_institution_include_rates()
    {
        $this->seedBasicData();
        $component = Livewire::test(PlacementReports::class);

        $byCourse = $component->get('placementByCourse');
        $this->assertIsIterable($byCourse);
        foreach ($byCourse as $row) {
            $this->assertTrue(isset($row->course_name));
            $this->assertTrue(isset($row->placed_students));
            $this->assertTrue(isset($row->placement_rate));
            $this->assertIsNumeric($row->placement_rate);
        }

        $byInstitution = $component->get('placementByInstitution');
        $this->assertIsIterable($byInstitution);
        foreach ($byInstitution as $row) {
            $this->assertTrue(isset($row->institution_name));
            $this->assertTrue(isset($row->placed_students));
            $this->assertTrue(isset($row->placement_rate));
            $this->assertIsNumeric($row->placement_rate);
        }
    }

    /** @test */
    public function updated_hook_triggers_filter_on_relevant_properties()
    {
        $now = Carbon::now();
        Livewire::test(PlacementReports::class)
            ->set('startDate', $now->copy()->subMonths(1)->format('Y-m-d'))
            ->assertDispatched('placementChartsUpdated')
            ->set('endDate', $now->format('Y-m-d'))
            ->assertDispatched('placementChartsUpdated')
            ->set('viewType', 'weekly')
            ->assertDispatched('placementChartsUpdated');
    }

    /** @test */
    public function placement_trends_monthly_has_12_points_and_weekly_reduces()
    {
        $component = Livewire::test(PlacementReports::class);
        $monthly = $component->get('placementTrends');
        $this->assertCount(12, $monthly['datasets'][0]['data']);

        $component->set('viewType', 'weekly');
        $weekly = $component->get('placementTrends');
        $this->assertLessThan(13, count($weekly['datasets'][0]['data'])); // since it iterates by 4 weeks
    }
}
