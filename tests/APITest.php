<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class APITest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWebServer()
    {
        $response = $this->get('/');
        $response->seeStatusCode(200);
        //  $response->assertStatus(200);
    }

    public function testAPI()
    {
        $data = $this->get('/api');
        $json = (array)$data->response->getData();
        $this->assertEquals(
            $json['post']->url, 'api/post'
        );
    }

    public function testPosts()
    {
        $data = $this->get('/api/posts');
        $json = $data->response->getData();
        $this->assertEquals(
            $json[0]->id, '1'
        );
    }

    public function testPost1()
    {
        $data = $this->get('/api/post?id=1');
        $json = $data->response->getData();
        $this->assertEquals(
            $json->id, '1'
        );
    }

}
