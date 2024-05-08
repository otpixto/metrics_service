<?php

namespace App\Http\Controllers;


use App\Helpers\DataboxHelper;
use App\Services\FacebookMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetricsController extends Controller
{
    public function handleGithubMetrics(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['user' => $user], 201);
    }


    public function handleFacebookMetrics(Request $request): JsonResponse
    {
        $data = (new FacebookMetricsService($request))->handle();

        return response()->json(['data' => $data]);

        $c = new DataboxHelper('6af7aae3bafc452491b0347811ca1dd0');

        $ok = $c->push('My_metric', 100);

        dd($ok);

        $response = Http::get('https://push.databox.com', [
            'facebook_friends_amount' => 100
        ]);

        dd($response);

        $fb = new Facebook([
            'app_id' => '1073501516940495',
            'app_secret' => '3f2cdbd4397a56addc95a994d69d06a7',
            'default_graph_version' => 'v18.0',
            //'default_access_token' => '{access-token}', // optional
        ]);

        $fb->setDefaultAccessToken('EAAPQWAyVRM8BO9LIOGJc2fIuN6FeEDbAO20UR5cZA5BrFW3S7QxS2tWqCYZCVNKx80BeLVqVzlNTpNCVkTjZAsIWIPSvM2UBZCmvgTczX5CFuIGZCgcD3oSZCsunZCo6zZAFZCRe7TR7bGkge1hSZCS3LJfGZBWeKvJ1u1Q8eKTKECjzFZCpmHV6JxoqfHOZAkj8BmzTu4UgXVZBg9SE05iKDTwijQmmLlZBxXH');

        /**
         * Generate some requests and then send them in a batch request.
         */

        // Get the name of the logged in user
        $requestUserName = $fb->request('GET', '/me?fields=id,name');

        // Get user likes
        $requestUserLikes = $fb->request('GET', '/me/likes?fields=id,name&limit=1');

        dd($fb->get('/me/feed')->getBody());

        // Get user events
        $requestUserEvents = $fb->request('GET', '/me/events?fields=id,name&limit=2');

        // Post a status update with reference to the user's name
        $message = 'My name is {result=user-profile:$.name}.' . "\n\n";
        $message .= 'I like this page: {result=user-likes:$.data.0.name}.' . "\n\n";
        $message .= 'My next 2 events are {result=user-events:$.data.*.name}.';
        $statusUpdate = ['message' => $message];
        $requestPostToFeed = $fb->request('POST', '/me/feed', $statusUpdate);

        // Get user photos
        $requestUserPhotos = $fb->request('GET', '/me/photos?fields=id,source,name&limit=2');

        $batch = [
            'user-profile' => $requestUserName,
            'user-likes' => $requestUserLikes,
            'user-events' => $requestUserEvents,
            'post-to-feed' => $requestPostToFeed,
            'user-photos' => $requestUserPhotos,
        ];

        echo '<h1>Make a batch request</h1>' . "\n\n";

        try {
            $responses = $fb->sendBatchRequest($batch);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        foreach ($responses as $key => $response) {
            if ($response->isError()) {
                $e = $response->getThrownException();
                echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
                echo '<p>Graph Said: ' . "\n\n";
                var_dump($e->getResponse());
            } else {
                echo "<p>(" . $key . ") HTTP status code: " . $response->getHttpStatusCode() . "<br />\n";
                echo "Response: " . $response->getBody() . "</p>\n\n";
                echo "<hr />\n\n";
            }
        }
    }
}
