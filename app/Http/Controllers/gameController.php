<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Round;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class gameController extends Controller
{
    public function index()
    {
        return view('index')->with([
            'a' => 'a'
        ]);
    }

    public function saveGame(Request $request)
    {
        $NewRound = new Round;
        $NewRound->qtyX = $request->qtyX;
        $NewRound->qtyY = $request->qtyY;
        $NewRound->qtyWin = $request->qtyWin;
        $NewRound->winner = $request->winner;
        $NewRound->type = $request->type;
        $NewRound->save();

        foreach($request->actions as $key => $action){
            $newAction = new Action;
            $newAction->orderNO = $key +1;
            if($action[2] == 1){
                $newAction->action = 'x';
            }else{
                $newAction->action = 'o';
            }
            $newAction->x = $action[0];
            $newAction->y = $action[1];
            $newAction->round_id = $NewRound->id;
            $newAction->save();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function history()
    {
        $data = Round::all();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function show(Request $request)
    {
        $actions = Action::where('round_id' , $request->id)->get();
        $round = Round::find($request->id);
        return response()->json([
            'status' =>'success',
            'actions' => $actions,
            'round' => $round
        ]);
    }
}
