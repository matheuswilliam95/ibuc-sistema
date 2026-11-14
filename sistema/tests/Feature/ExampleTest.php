<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_opens_the_visual_preview(): void
    {
        $this->get('/')->assertRedirect('/identidade-visual');
        $this->get('/identidade-visual')->assertOk();
    }
}
