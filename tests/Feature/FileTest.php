<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_upload()
    {
        $response = $this->json('POST', '/api/demo/upload', [
            'file' => UploadedFile::fake()->image('test.jpg')->size(600)
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'name',
            'type',
            'size',
        ]);
    }
}
