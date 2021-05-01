<?php

namespace Tests\Feature;
use App\Models\User;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use Vinkla\Hashids\Facades\Hashids;

class LinkTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->link = Link::factory()-ofUser($this->user)->create();
    }

    /** @test */
    function link_creation()
    {
        $this->actingAs($this->user)
            ->get(route('links.create'))
            ->assertSeeLivewire('create-link');
    }

    /** @test */
    function link_editing()
    {
        $this->actingAs($this->user)
            ->get(route('links.edit', $this->link))
            ->assertSeeLivewire('edit-link', ['link' => $this->link]);
    }

    /** @test */
    function link_index()
    {
        $this->actingAs($this->user)
            ->get(route('links'))
            ->assertSeeLivewire('link-table');
    }

     /** @test */
     function link_with_slug()
     {
         $this->assertDatabaseCount('links', 1);
 
         Livewire::actingAs($this->user)
             ->test('create-link')
             ->set('url', 'https://google.ca')
             ->set('slug', 'test-slug')
             ->call('saveLink')
             ->assertRedirect(route('links'));
 
         $this->assertDatabaseCount('links', 2);
         $this->assertDatabaseHas('links', [
             'url' => 'https://google.ca',
             'slug' => 'test-slug',
         ]);
     }

      /** @test */
    function link_without_slug()
    {
        $this->assertDatabaseCount('links', 1);

        Livewire::actingAs($this->user)
            ->test('create-link')
            ->set('url', 'https://google.ca')
            ->set('slug', null)
            ->call('saveLink')
            ->assertRedirect(route('links'));

        $this->assertDatabaseCount('links', 2);
        $this->assertDatabaseHas('links', [
            'url' => 'https://google.ca',
            'slug' => Hashids::encode(2),
        ]);
    }

    /** @test */
    function link_with_non_unique_slug()
    {
        $this->assertDatabaseCount('links', 1);

        Livewire::actingAs($this->user)
            ->test('create-link')
            ->set('url', 'https://google.ca')
            ->set('slug', $this->link->slug)
            ->call('saveLink')
            ->assertHasErrors(['slug' => 'unique']);

        $this->assertDatabaseCount('links', 1);
    }

    /** @test */
    function can_update_link()
    {
        $this->assertNotEquals($this->link->url, 'https://google.ca');
        $this->assertNotEquals($this->link->slug, 'test-slug');
        $this->assertFalse($this->link->is_enabled);

        Livewire::actingAs($this->user)
            ->test('edit-link', ['link' => $this->link])
            ->set('url', 'https://google.ca')
            ->set('slug', 'test-slug')
            ->set('is_enabled', true)
            ->call('saveLink')
            ->assertRedirect(route('links'));

        $this->assertEquals($this->link->refresh()->url, 'https://google.ca');
        $this->assertEquals($this->link->refresh()->slug, 'test-slug');
        $this->assertTrue($this->link->refresh()->is_enabled);
    }

    /** @test */
    function can_update_link_with_same_slug()
    {
        $this->assertNotEquals($this->link->url, 'https://google.ca');

        Livewire::actingAs($this->user)
            ->test('edit-link', ['link' => $this->link])
            ->set('url', 'https://google.ca')
            ->set('slug', $this->link->slug)
            ->call('saveLink')
            ->assertRedirect(route('links'));

        $this->assertEquals($this->link->refresh()->url, 'https://google.ca');
    }

    /** @test */
    function cannot_update_link_with_non_unique_slug()
    {
        Link::factory()->create(['slug' => 'test-slug']);

        Livewire::actingAs($this->user)
            ->test('edit-link', ['link' => $this->link])
            ->set('url', 'https://google.ca')
            ->set('slug', 'test-slug')
            ->call('saveLink')
            ->assertHasErrors('slug');
    }

    /** @test */
    function can_redirect()
    {
        $this->actingAs($this->user)
            ->get(route('redirect', $this->link->slug))
            ->assertRedirect($this->link->url);
    }

    /** @test */
    function cannot_redirect_if_link_not_enabled()
    {
        $this->link->update(['is_enabled' => false]);

        $this->assertEquals($this->link->redirect, 0);

        $this->actingAs($this->user)
            ->get(route('redirect', $this->link->slug))
            ->assertStatus(403);

        $this->assertEquals($this->link->redirect, 0);
    }

    /** @test */
    function link_redirect_count_incremented_on_successful_redirection()
    {
        $this->assertEquals($this->link->redirect, 0);

        $this->actingAs($this->user)
            ->get(route('redirect', $this->link->slug))
            ->assertRedirect($this->link->url);

        $this->assertEquals($this->link->redirect, 1);
    }
}
