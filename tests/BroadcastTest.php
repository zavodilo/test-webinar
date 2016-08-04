<?php

class BroadcastTest extends TestCase
{
    public function testStore()
    {
        $user = factory(App\User::class)->create();
        $this->actingAs($user);

        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_1',
            'leader' => 'leader_1',
            'description' => 'description_1',
            'status' => '1',
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;
        $this->assertTrue((int)$id > 0, 'Model save');
    }

    public function testShow()
    {
        $user = factory(App\User::class)->create();
        $this->actingAs($user);

        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_2',
            'leader' => 'leader_2',
            'description' => 'description_2',
            'status' => '3',
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;
        $this->assertTrue((int)$id > 0, 'Model save');

        $this->visit(route('broadcast.show', ['id' => $id]))
            ->see($id);
    }


    public function testIndex()
    {
        $user = factory(App\User::class)->create();
        $this->actingAs($user);

        //Проверяем создание и нахождение в списке трансляции
        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_1',
            'leader' => 'leader_1',
            'description' => 'description_1',
            'status' => \App\Broadcast::START,
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;
        $this->assertTrue((int)$id > 0, 'Model save');

        $this->visit(route('broadcast.index'))
            ->see($id);

        //Проверяем создание и нахождение в списке второй трансляции
        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_1',
            'leader' => 'leader_1',
            'description' => 'description_1',
            'status' => \App\Broadcast::START,
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;
        $this->assertTrue((int)$id > 0, 'Model save');

        $this->visit(route('broadcast.index'))
            ->see($id);
    }

    public function testUpdate()
    {
        $user = factory(App\User::class)->create();
        $this->actingAs($user);

        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_2',
            'leader' => 'leader_2',
            'description' => 'description_2',
            'status' => '3',
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;
        $this->assertTrue((int)$id > 0, 'Model save');
        $new_name = 'new_name';
        $new_leader = 'new_leader';
        $new_description = 'new_description';
        $new_status = \App\Broadcast::START;
        $response = $this->request(route('broadcast.update', ['id' => $id]), [
            'name' => $new_name,
            'status' => $new_status,
            'leader' => $new_leader,
            'description' => $new_description,
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ], 'PUT');
        $content = json_decode($response->content());
        $this->assertEquals($new_name, $content->name, 'name save');
        $this->assertEquals($new_status, $content->status, 'status save');
    }

    public function testDelete()
    {
        $user = factory(App\User::class)->create();
        $this->actingAs($user);

        $response = $this->request(route('broadcast.store'), [
            'name' => 'name_2',
            'leader' => 'leader_2',
            'description' => 'description_2',
            'status' => '3',
            'started_at' => \Carbon\Carbon::today(),
            'finished_at' => \Carbon\Carbon::tomorrow(),
        ]);

        $content = json_decode($response->content());

        $id = $content->id;

        $this->request(route('broadcast.destroy', ['id' => $id]), [], 'DELETE');
        $this->assertNull(\App\Broadcast::find($id));
    }

    /**
     * @param string $route
     * @param array $data
     * @param string $type
     * @param bool $json
     * @return \Illuminate\Http\Response
     */
    protected function request($route, $data, $type = 'POST', $json = false, $headers = [])
    {
        $headers['HTTP_X-Requested-With'] = 'XMLHttpRequest';
        if ($json) {
            $headers['Accept'] = 'application/json';
            $headers['HTTP_Accept'] = 'application/json';
        }

        $data['_token'] = csrf_token();
        $method = strtolower($type);
        if ($method == 'get') {
            $request = $this->get($route, $headers)->response;
        } else {
            $request = $this->{$method}($route, $data, $headers)->response;
        }

        $this->assertNotEquals(500, $request->getStatusCode(), 'Ошибка 500 : ' . $route . PHP_EOL . $request->getContent() . PHP_EOL . print_r($data, true) . PHP_EOL
            . print_r($request->headers->all(), true)) ;
        return $request;
    }
}
