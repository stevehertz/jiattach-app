<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_title_message_icon_url_and_type_label_accessors(): void
    {
        $n = Notification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\PlacementNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => [
                'title' => 'Custom Title',
                'message' => 'Body',
                'icon' => 'fas fa-briefcase',
                'url' => '/foo',
            ],
        ]);

        $this->assertSame('Custom Title', $n->title);
        $this->assertSame('Body', $n->message);
        $this->assertSame('fas fa-briefcase', $n->icon);
        $this->assertSame('/foo', $n->url);
        $this->assertSame('Placement', $n->type_label);
    }

    /** @test */
    public function it_marks_as_read_and_unread_and_reports_state(): void
    {
        $n = Notification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\PlacementNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => [],
        ]);

        $this->assertTrue($n->unread());
        $this->assertFalse($n->read());

        $n->markAsRead();
        $this->assertNotNull($n->read_at);
        $this->assertTrue($n->read());
        $this->assertFalse($n->unread());

        $n->markAsUnread();
        $this->assertNull($n->read_at);
        $this->assertTrue($n->unread());
    }

    /** @test */
    public function query_scopes_work_as_expected(): void
    {
        $base = [
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => [],
        ];

        Notification::create(array_merge($base, [
            'id' => (string) Str::uuid(),
            'type' => 'Foo',
            'read_at' => null,
        ]));
        Notification::create(array_merge($base, [
            'id' => (string) Str::uuid(),
            'type' => 'Bar',
            'read_at' => Carbon::now(),
        ]));
        Notification::create(array_merge($base, [
            'id' => (string) Str::uuid(),
            'type' => 'Foo',
            'read_at' => Carbon::now(),
        ]));

        $this->assertCount(1, Notification::unread()->get());
        $this->assertCount(2, Notification::read()->get());
        $this->assertCount(2, Notification::ofType('Foo')->get());
        $this->assertCount(3, Notification::forUser(1)->get());
        $this->assertCount(0, Notification::forUser(2)->get());
    }

    /** @test */
    public function time_ago_accessor_formats_human_readable_time(): void
    {
        $n = Notification::create([
            'id' => (string) Str::uuid(),
            'type' => 'Foo',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => [],
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        $this->assertStringContainsString('ago', $n->time_ago);
    }
}
