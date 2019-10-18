<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Message;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userlist()
    {
        $userList = User::latest()->where('id','!=',auth()->user()->id)->get();

        if (\Request::ajax()){
            return response()->json($userList,200);
        }
        return back();

    }

    public function get_user_message_byId($id){
        $userMessage = Message::where(function($q) use($id){
            $q->where('from',auth()->user()->id);
            $q->where('to',$id);
            $q->where('type',0);
        })->orWhere(function($q) use ($id){
            $q->where('from',$id);
            $q->where('to',auth()->user()->id);
            $q->where('type',1);
        })->with('user')->get();
        return $userMessage;
    }

    public function usermessage($id=null){
        $user = User::find($id);
        $userMessage = $this->get_user_message_byId($id);
        return response()->json([
            'user'=>$user,
            'userMessage'=>$userMessage
            ,200]);

    }

    public function sendmessage(Request $request){
        $message = new Message();
        $message->from  = auth()->user()->id;
        $message->to    = $request->user_id;
        $message->message= $request->message;
        $message->type= 0;
        $message->save();

        $message = new Message();
        $message->from  = auth()->user()->id;
        $message->to    = $request->user_id;
        $message->message= $request->message;
        $message->type= 1;
        $message->save();

        broadcast(new SendMessage($message));

        return response()->json($message,201);
    }

    public function deleteMessage($id){
        $message = Message::find($id)->delete();
        return response()->json($message,200);
    }

    public function deleteMessageAll($id){
       $message =  $this->get_user_message_byId($id);

       foreach ($message as $value){
          $deleteAll = Message::findOrFail($value->id)->delete();
       }
        return response()->json($deleteAll,200);
    }
}
