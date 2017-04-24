<?php namespace App\Http\Controllers;

/**
 * This is a text.
 */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\JSONEndpointDocumentation;

class APIController extends Controller
{
    public $restful = true;

    private $posts = [
        ['id' => 1, 'content' => "Hello"],
        ['id' => 2, 'content' => "Lorem Ispum"],
        ['id' => 3, 'content' => "Lorem Ispum Ad Astar"],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //  $this->middleware('auth:api');
        //    $this->categories=Products::get_categories();
    }

    /**
     * @URL
     * @semanticVersion 1.0.7,
     * @title Index
     * @description Lorem Ipsum,
     * @method GET
     * @urlParams
     * @required
     * @optional
     * @dataParams
     * @successResponse Code: 200 Content: { id: 1, name: "Lorem" }
     * @successResponse Code: 200 Content: { id: 1, name: "Lorem", body: "Lorem Ipsum" }
     * @errorResponse Code: 401 UNAUTHORIZED Content: { error: "Lorem Ipsum" }
     * @sampleCall curl http://JSONEndpointDocumentation.app/api/posts
     * @notes Lorem Ipsum
     * @cached No
     * @authentication NTLM-Digest
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        static $results;
        if ($results == null) {
            $o = new JSONEndpointDocumentation();
            $results = $o->createJSONForAllClassFunctions(__CLASS__, "api/");
        }

        return response()
            ->json((array)$results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @semanticVersion 1.0.0
     * @title Posts
     * @successResponse Code: 200 Content: [ { id: 1,...} ]
     * @errorResponse Code: 401 UNAUTHORIZED Content: { error: "Lorem Ipsum" }
     */
    public function posts(Request $request)
    {
        $posts = (array)$this->posts;
        return response()
            ->json($posts);
    }

    private function arraySearchByField($id, string $field = 'id', array &$array)
    {
        foreach ($array as $key => $item) {
            if ($item[$field] == $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @semanticVersion 1.0.0
     * @title Post
     * @successResponse Code: 200 Content: { id: 1, name: "Lorem" }
     * @successResponse Code: 200 Content: { id: 2, name: "Lorem", body: "Lorem Ipsum" }
     * @errorResponse Code: 401 UNAUTHORIZED Content: { error: "Lorem Ipsum" }
     */
    public function post(Request $request)
    {
        $id = $request->input('id');

        $post = $this->arraySearchByField($id,'id', $this->posts);

        return response()
            ->json($post);
    }

}
