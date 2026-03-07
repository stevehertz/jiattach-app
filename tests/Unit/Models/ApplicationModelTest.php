<?php

namespace Tests\Unit\Models;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function status_is_cast_to_enum_and_accepts_valid_values()
    {
        $app = new Application([
            'user_id' => 1,
            'attachment_opportunity_id' => 1,
            'status' => ApplicationStatus::PENDING,
        ]);

        $this->assertInstanceOf(ApplicationStatus::class, $app->status);
        $this->assertTrue(in_array($app->status, ApplicationStatus::active()));

        $app->status = ApplicationStatus::REJECTED;

        $this->assertSame(ApplicationStatus::REJECTED, $app->status);
        $this->assertTrue($app->status->isNegative());
    }

    /** @test */
    public function date_and_numeric_fields_are_cast_correctly_without_persisting()
    {
        $now = now();
        $app = new Application([
            'user_id' => 1,
            'attachment_opportunity_id' => 1,
            'submitted_at' => $now,
            'accepted_at' => $now,
            'declined_at' => $now,
            'match_score' => '82.75',
            'status' => ApplicationStatus::PENDING,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $app->submitted_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $app->accepted_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $app->declined_at);
        $this->assertIsFloat($app->match_score);
        $this->assertSame(82.75, $app->match_score);
    }

    /** @test */
    public function relationships_are_defined()
    {
        $app = new Application();
        $this->assertTrue(method_exists($app, 'student'));
        $this->assertTrue(method_exists($app, 'opportunity'));
        $this->assertTrue(method_exists($app, 'placement'));
        $this->assertTrue(method_exists($app, 'feedbacks'));
    }

    /**
     * New behavior 1: status_badge accessor renders correct HTML (color + label)
     * using enum metadata.
     * @test
     */
    public function status_badge_attribute_renders_expected_html()
    {
        $app = new Application(['status' => ApplicationStatus::UNDER_REVIEW]);

        $badge = $app->status_badge; // HTML string

        $this->assertIsString($badge);
        $this->assertStringContainsString("badge-" . ApplicationStatus::UNDER_REVIEW->color(), $badge);
        $this->assertStringContainsString(ApplicationStatus::UNDER_REVIEW->label(), $badge);
        $this->assertStringContainsString('<span', $badge);
        $this->assertStringContainsString('</span>', $badge);
    }

    /**
     * New behavior 2: canTransitionTo honors enum transition map and string inputs.
     * @test
     */
    public function can_transition_to_allows_and_blocks_expected_transitions()
    {
        $app = new Application(['status' => ApplicationStatus::PENDING]);

        // Allowed from pending
        $this->assertTrue($app->canTransitionTo(ApplicationStatus::UNDER_REVIEW));

        // Disallowed direct jump
        $this->assertFalse($app->canTransitionTo(ApplicationStatus::HIRED));

        // Accept strings for valid statuses
        $this->assertTrue($app->canTransitionTo('under_review'));

        // Invalid string should be rejected
        $this->assertFalse($app->canTransitionTo('not_a_valid_status'));
    }

    /**
     * New behavior 3: getAvailableNextStatuses returns metadata for allowed transitions.
     * @test
     */
    public function get_available_next_statuses_returns_expected_metadata()
    {
        $app = new Application(['status' => ApplicationStatus::UNDER_REVIEW]);

        $next = $app->getAvailableNextStatuses();

        // Keys are values of allowed transitions
        $expectedKeys = array_map(fn($s) => $s->value, ApplicationStatus::UNDER_REVIEW->allowedTransitions());
        $this->assertSameCanonicalizing($expectedKeys, array_keys($next));

        // Each entry contains required metadata
        foreach ($next as $value => $meta) {
            $this->assertArrayHasKey('value', $meta);
            $this->assertArrayHasKey('label', $meta);
            $this->assertArrayHasKey('color', $meta);
            $this->assertArrayHasKey('icon', $meta);
            $this->assertArrayHasKey('description', $meta);
            $this->assertSame($value, $meta['value']);
        }
    }

    /**
     * New behavior 4: status_label, status_icon, status_color proxy to enum accessors.
     * @test
     */
    public function status_accessors_proxy_to_enum_metadata()
    {
        $status = ApplicationStatus::SHORTLISTED;
        $app = new Application(['status' => $status]);

        $this->assertSame($status->label(), $app->status_label);
        $this->assertSame($status->icon(), $app->status_icon);
        $this->assertSame($status->color(), $app->status_color);
    }

    /**
     * New behavior 5: canTransitionTo handles terminal statuses with no next steps.
     * @test
     */
    public function terminal_statuses_have_no_available_transitions_and_block_changes()
    {
        foreach ([ApplicationStatus::HIRED, ApplicationStatus::REJECTED, ApplicationStatus::OFFER_REJECTED] as $terminal) {
            $app = new Application(['status' => $terminal]);

            $this->assertSame([], $app->getAvailableNextStatuses());
            $this->assertFalse($app->canTransitionTo(ApplicationStatus::PENDING));
        }
    }
}
