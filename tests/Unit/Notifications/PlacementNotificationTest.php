<?php

namespace Tests\Unit\Notifications;

use App\Notifications\PlacementNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class PlacementNotificationTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
    }

    private function makePlacement(array $overrides = []): object
    {
        $organization = (object) [
            'name' => $overrides['organization_name'] ?? 'Acme Org',
        ];

        return (object) array_merge([
            'id' => $overrides['id'] ?? $this->faker->uuid(),
            'status' => $overrides['status'] ?? 'processing',
            'organization' => isset($overrides['organization_name']) && $overrides['organization_name'] === null
                ? null
                : $organization,
        ], $overrides);
    }

    private function makeNotifiable(string $firstName = 'John'): object
    {
        return new class($firstName) {
            public string $first_name;
            public function __construct($firstName) { $this->first_name = $firstName; }
        };
    }

    public function test_via_returns_database_and_mail_channels(): void
    {
        $placement = $this->makePlacement();
        $notification = new PlacementNotification($placement, 'processing');

        $channels = $notification->via($this->makeNotifiable());

        $this->assertIsArray($channels);
        $this->assertEquals(['database', 'mail'], $channels);
    }

    public function test_to_array_contains_expected_keys_and_values_for_processing(): void
    {
        $placement = $this->makePlacement(['status' => 'processing', 'organization_name' => 'Globex']);
        $notification = new PlacementNotification($placement, 'processing');

        $data = $notification->toArray($this->makeNotifiable());

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('placement_id', $data);
        $this->assertArrayHasKey('placement_status', $data);
        $this->assertArrayHasKey('organization_name', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertArrayHasKey('type', $data);

        $this->assertSame($placement->id, $data['placement_id']);
        $this->assertSame('processing', $data['placement_status']);
        $this->assertSame('Globex', $data['organization_name']);
        $this->assertSame('/student/placement/status', $data['url']);
        $this->assertSame('placement', $data['type']);
        $this->assertSame('Placement Processing Started', $data['title']);
        $this->assertStringContainsString('started processing your placement request', $data['message']);
    }

    public function test_to_array_handles_missing_organization_name_and_unknown_type(): void
    {
        $placement = $this->makePlacement(['status' => 'placed', 'organization_name' => null]);
        $notification = new PlacementNotification($placement, 'unknown_type');

        $data = $notification->toArray($this->makeNotifiable());

        $this->assertNull($data['organization_name']);
        $this->assertSame('Placement Update', $data['title']);
        $this->assertSame('There is an update regarding your placement.', $data['message']);
        $this->assertSame('fas\ fa-bell', str_replace(' ', '\\ ', $data['icon']));
    }

    public function test_to_mail_builds_expected_subject_greeting_and_action(): void
    {
        $placement = $this->makePlacement(['status' => 'placed', 'organization_name' => 'Wayne Enterprises']);
        $notification = new PlacementNotification($placement, 'placed');

        $notifiable = $this->makeNotifiable('Alice');
        $mail = $notification->toMail($notifiable);

        // Subject includes title
        $this->assertStringContainsString('Jiattach Placement Update: Placement Confirmed!', $mail->subject);

        // The greeting is set when rendering, but the MailMessage stores intro lines with the greeting as the first line content
        $introLines = (new \ReflectionProperty($mail, 'introLines'));
        $introLines->setAccessible(true);
        $lines = $introLines->getValue($mail);

        $this->assertIsArray($lines);
        $this->assertNotEmpty($lines);
        $this->assertStringContainsString('Congratulations! You have been placed at Wayne Enterprises.', $lines[0] . ($lines[1] ?? ''));

        // The action URL should be set to placement status page
        $actionUrlProperty = new \ReflectionProperty($mail, 'actionUrl');
        $actionUrlProperty->setAccessible(true);
        $actionUrl = $actionUrlProperty->getValue($mail);
        $this->assertSame(url('/student/placement/status'), $actionUrl);
    }

    public function test_message_variants_for_known_types(): void
    {
        $types = ['processing', 'placed', 'admin_assigned', 'review', 'complete'];

        foreach ($types as $type) {
            $placement = $this->makePlacement(['status' => $type]);
            $notification = new PlacementNotification($placement, $type);
            $data = $notification->toArray($this->makeNotifiable());

            $this->assertNotEmpty($data['title']);
            $this->assertNotEmpty($data['message']);
            $this->assertNotEmpty($data['icon']);
        }
    }
}
