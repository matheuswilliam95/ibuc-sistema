<?php

namespace Tests\Feature;

use Tests\TestCase;

class JourneyPreviewTest extends TestCase
{
    public function test_journeys_render_with_fictional_people_and_disabled_forms_before_javascript(): void
    {
        $this->get('/jornadas')
            ->assertOk()
            ->assertSee('dados fictícios')
            ->assertSee('Bruno Exemplo')
            ->assertSee('Ana Exemplo')
            ->assertSee('Eva Exemplo')
            ->assertSee('disabled data-prototype-controls', false)
            ->assertSee('notice-confirm-dialog', false)
            ->assertSee('attendance-confirm-dialog', false)
            ->assertSee('delivery-confirm-dialog', false);
    }

    public function test_journeys_have_no_form_submission_endpoint(): void
    {
        $this->post('/jornadas', [])->assertStatus(405);
    }
}
