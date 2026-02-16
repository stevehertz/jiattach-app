<?php

namespace Tests\Feature;

use App\Livewire\Admin\Reports\OpportunityAnalytics;
use App\Models\AttachmentOpportunity;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class OpportunityAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create an opportunity with counts and relations.
     */
    private function makeOpportunity(array $overrides = []): AttachmentOpportunity
    {
        return AttachmentOpportunity::factory()->create(array_merge([
            'status' => 'published',
            'applications_count' => 0,
            'slots_available' => 10,
            'slots_filled' => 0,
            'created_at' => now()->subMonths(1),
        ], $overrides));
    }

    public function test_it_renders_component()
    {
        Livewire::test(OpportunityAnalytics::class)
            ->assertStatus(200);
    }

    public function test_calculates_basic_stats_within_date_range()
    {
        $inRange = $this->makeOpportunity(['created_at' => now()->subMonths(2), 'status' => 'published']);
        $filledInRange = $this->makeOpportunity(['created_at' => now()->subMonths(2), 'status' => 'filled']);
        $outOfRange = $this->makeOpportunity(['created_at' => now()->subYears(2)]);

        Livewire::test(OpportunityAnalytics::class)
            ->assertSet('opportunityStats.total_opportunities', 2)
            ->assertSet('opportunityStats.published_opportunities', 1)
            ->assertSet('opportunityStats.filled_opportunities', 1)
            ->assertSet('opportunityStats.fill_rate', 50.0);
    }

    public function test_active_opportunities_respects_deadline_and_slots()
    {
        $active = $this->makeOpportunity([
            'status' => 'published',
            'application_deadline' => now()->addDay(),
            'slots_available' => 10,
            'slots_filled' => 5,
        ]);

        $pastDeadline = $this->makeOpportunity([
            'status' => 'published',
            'application_deadline' => now()->subDay(),
        ]);

        $noSlots = $this->makeOpportunity([
            'status' => 'published',
            'application_deadline' => now()->addDay(),
            'slots_available' => 5,
            'slots_filled' => 5,
        ]);

        Livewire::test(OpportunityAnalytics::class)
            ->assertSet('opportunityStats.active_opportunities', 1);
    }

    public function test_type_analysis_includes_only_present_types_and_computes_averages()
    {
        $this->makeOpportunity(['opportunity_type' => 'internship', 'applications_count' => 6]);
        $this->makeOpportunity(['opportunity_type' => 'internship', 'applications_count' => 4]);
        $this->makeOpportunity(['opportunity_type' => 'attachment', 'applications_count' => 3]);

        $component = Livewire::test(OpportunityAnalytics::class);
        $analysis = $component->get('opportunityTypeAnalysis');

        $types = collect($analysis)->pluck('type');
        $this->assertTrue($types->contains('Internship'));
        $this->assertTrue($types->contains('Attachment'));

        $internship = collect($analysis)->firstWhere('type', 'Internship');
        $this->assertEquals(2, $internship['count']);
        $this->assertEquals(5.0, $internship['avg_applications']);
    }

    public function test_top_performing_opportunities_sorted_by_application_count_and_fill_rate_calculated()
    {
        $o1 = $this->makeOpportunity(['applications_count' => 2, 'slots_available' => 10, 'slots_filled' => 5]);
        $o2 = $this->makeOpportunity(['applications_count' => 7, 'slots_available' => 20, 'slots_filled' => 10]);
        $o3 = $this->makeOpportunity(['applications_count' => 5, 'slots_available' => 0, 'slots_filled' => 0]);

        $component = Livewire::test(OpportunityAnalytics::class);
        $top = $component->get('topPerformingOpportunities');

        $this->assertCount(3, $top);
        $this->assertEquals($o2->id, $top[0]['id']);
        $this->assertEquals(50.0, $top[0]['fill_rate']);
        $this->assertEquals(0.0, $top[2]['fill_rate']); // zero slots => 0
    }

    public function test_location_analysis_groups_and_calculates_metrics()
    {
        $this->makeOpportunity(['location' => 'Nairobi', 'county' => 'Nairobi', 'town' => 'Nairobi', 'applications_count' => 6, 'slots_filled' => 3]);
        $this->makeOpportunity(['location' => 'Nairobi', 'county' => 'Nairobi', 'town' => 'Nairobi', 'applications_count' => 4, 'slots_filled' => 2]);
        $this->makeOpportunity(['location' => 'Mombasa', 'county' => 'Mombasa', 'town' => 'Mombasa', 'applications_count' => 5, 'slots_filled' => 1]);

        $component = Livewire::test(OpportunityAnalytics::class);
        $locations = $component->get('locationAnalysis');

        // Nairobi grouped
        $nairobi = collect($locations)->firstWhere('location', 'Nairobi');
        $this->assertEquals(2, $nairobi['opportunity_count']);
        $this->assertEquals(10, $nairobi['total_applications']);
        $this->assertEquals(5, $nairobi['total_filled']);
        $this->assertEquals(25.0, $nairobi['fill_rate']); // (5 / (2 * 10)) * 100
        $this->assertEquals(5.0, $nairobi['avg_applications']);
    }
}
