<?php

namespace App\Http\Controllers;

use App\Broadcast;
use App\Http\Requests\BroadcastStoreRequest as BroadcastStoreRequest;
use App\Http\Requests\BroadcastUpdateRequest as BroadcastUpdateRequest;
use App\Http\Requests\RequestInterface;
use Illuminate\Support\Facades\Auth;
use Redis;
use \Illuminate\Http\Request;

class BroadcastController extends Controller
{

    protected $request_classes = [
        'default' => Request::class,
        'store' => BroadcastStoreRequest::class,
        'update' => BroadcastUpdateRequest::class,
    ];

    public function __construct(Request $request)
    {
        //parent::__construct($request);
        $action = str_replace(static::class . '@', '', $this->getRouter()->currentRouteAction());
        if (isset($this->request_classes[$action])) {
            \App::bind(RequestInterface::class, $this->request_classes[$action]);
        } else {
            \App::bind(RequestInterface::class, $this->request_classes['default']);
        }
    }

    /**
     * Список всех активных трансляций
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Redis::get('broadcasts');
        if (!$models) {
            $models = Broadcast::where('status', Broadcast::START)->get();
            //Записываем в кеш
            Redis::set('broadcasts', json_encode($models->toArray()));
        }

        //Возвращаем из базы все модели в статусе начата
        return $models;
    }


    /**
     * Создает новую трансляцию
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BroadcastStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestInterface $request)
    {
        //Создавать трансляцию может только авторизоватнный пользователь
        if (!Auth::check()) {
            abort(403, 'Нужно авторизоваться');
        }

        $model = new Broadcast();
        //получаем данные из реквеста
        $data = $request->all();
        //заполняем модель данными
        $model->fill($data);
        if ($model->started_at >= $model->finished_at) {
            abort(400, 'Дата начала не может быть больше даты окончания');
        }
        //сохраняем модель
        $model->save();
        //Записываем в кеш
        $this->redisSet($model);
        //Очищаю кеш редиса для всех задач
        Redis::del('broadcasts');
        return $model;
    }

    protected function redisSet(Broadcast $model) {
        //Записываем в кеш
        Redis::set('broadcast:' . $model->id, json_encode($model->toArray()));
    }

    /**
     * Получаем данные о трансляции из базы
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Получаем из кеша
        $model = Redis::get('broadcast:' . $id);
        if (!$model) {
            $model = Broadcast::findOrFail($id);
            //Записываем в кеш
            $this->redisSet($model);
        }

        return $model;
    }


    /**
     * Обновляем трансляцию
     * Чтоб запустить, остановить или удалить трансляцию, надо поменять ее статус
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BroadcastUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RequestInterface $request, $id)
    {
        //Редактировать трансляцию может только авторизоватнный пользователь
        if (!Auth::check()) {
            abort(403, 'Нужно авторизоваться');
        }
        $model = Broadcast::findOrFail($id);
        //получаем данные из реквеста
        $data = $request->all();
        //заполняем модель данными
        $model->fill($data);
        if ($model->started_at >= $model->finished_at) {
            abort(400, 'Дата начала не может быть больше даты окончания');
        }
        //сохраняем модель
        $model->save();
        //обновляем модель
        $model->fresh();
        //Записываем в кеш
        $this->redisSet($model);
        //Очищаю кеш редиса для всех задач
        Redis::del('broadcasts');
        return $model;
    }

    /**
     * Удаление модели
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Удалить трансляцию может только авторизоватнный пользователь
        if (!Auth::check()) {
            abort(403, 'Нужно авторизоваться');
        }
        $model = Broadcast::findOrFail($id);
        $model->delete();
        //Удаляем из кеша
        Redis::del('broadcast:' . $model->id);
        //Очищаю кеш редиса для всех задач
        Redis::del('broadcasts');
        return $model;
    }
}
