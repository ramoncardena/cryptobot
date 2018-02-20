<?php

namespace App\Http\Controllers;

use App\Mail\NewInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


class InviteController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('invitations');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         // Validate form
        $validatedData = $request->validate([
            'invitation_email' => 'required',
        ]);

        $name = Auth::user()->name;
        $email = Auth::user()->email;
        $invitation = json_decode(\Invi::generate($request->invitation_email, "7 day", true));
      
        if ( property_exists($invitation,'error') ) {
            // SESSION FLASH: Error generating invitations
            $request->session()->flash('status-text', 'Error: ' . $invitation->error );
            $request->session()->flash('status-class', 'alert');

            return redirect('/invite');
        }
        else {
            return view('invitations', ['invitation_code' => $invitation->code, 'invitation_email' => $invitation->email, 'invitation_expiration' => $invitation->expiration]);

            Mail::to($invitation->email)->send(new NewInvite($invitation, $name, $email));

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
