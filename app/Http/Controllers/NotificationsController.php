<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class NotificationsController extends Controller
{

    public function index()
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function delete($id)
    {
        //
    }


    public function deleteall()
    {
        //
    }


    public function read($id)
    {
        try {

            Auth::user()->Notifications->find($id)->update(['read_at' => Carbon::now()]);   

            return ["success" => true];

        } catch (Exception $e) {

            return ["success" => false, 'message' => $e->getMessage()];

        }
        

    }

    public function readall($id)
    {
        try {

            Auth::user()->unreadNotifications->markAsRead(); 

            return ["success" => true];

        } catch (Exception $e) {

            return ["success" => false, 'message' => $e->getMessage()];
            
        }
        
    }
}
