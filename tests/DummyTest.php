<?php

class DummyTest extends TestCase
{
    /** @test */
    public function it_does_nothing()
    {
        $code = Artisan::call('foo');
        $output = Artisan::output();

        $this->assertEquals(0, $code);
        $this->assertContains('Yep!', $output);
    }
}
