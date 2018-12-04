<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Events\TestEvent;

class LineController extends Controller
{
    public function app()
    {
        $messages = Message::all();
        return view('line', compact('messages'));
    }

    public function index()
    {
        $messages = Message::all();
        return response()->json($messages);
    }

    public function store(Request $request){
        $this->validate($request, [
            'object' => 'required',
            'value' => 'required'
        ]);
        $message = Message::create($request->all());
        event(
            new TestEvent($message)
        );
        return response()->json($message, 200);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'value' => 'required'
        ]);
        $message = Message::find($id);
        $message->update($request->all());
        event(
            new TestEvent($message)
        );
        return response()->json($message, 200);
    }
}
