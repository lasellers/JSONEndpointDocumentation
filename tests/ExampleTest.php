<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    /*  public function testExample()
      {
          $this->get('/');

          $this->assertEquals(
              $this->app->version(), $this->response->getContent()
          );

          $content = $this->get('/v1/users/1')->seeStatusCode(200)->response->getContent();
          $data = $this->get('/api')->seeStatusCode(200)->decodeResponseJson();
      }
  */


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
