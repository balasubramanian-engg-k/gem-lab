<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CertificateClickTest extends DuskTestCase
{
    /** @test */
    public function it_clicks_the_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8002/search_certificate/GHCNSGIXPZ')
                    ->press('Click Me')   // or ->click('#btn-id')
                    ->waitForText('Button clicked!'); 
        });
    }
}
