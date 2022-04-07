<?php

namespace App\Http\Controllers;

use App\Mail\TicketMail;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function saveTicket(Request $request){
        $request->validate([
            'subject' => 'required|regex:/^[a-zA-Z0-9_ ]*$/',
        ]);
        $code = Ticket::select('uid')->orderBy('id','desc')->first();
        if (!empty($code)) {
            $exuid = explode('-', $code->uid);
            $uid = 'TKT-'.($exuid[1]+1);
        } else {
            $uid = 'TKT-1';
        }

        $ticket = new Ticket();
        $ticket->subject = $request->subject;
        $ticket->user_name = Auth::user()->name;
        $ticket->user_email = Auth::user()->email;
        $ticket->uid = $uid;
        $ticket->save();
        if($ticket){
            $details = [
                'title' => 'Mail from assignment',
                'body' => $request->subject,
            ];

            Mail::to(Auth::user()->email)->send(new TicketMail($details));
        }

        return redirect()->route('home')->with('success','Ticket added.');
    }

    public function testReqres(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://reqres.in/api/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'name=puneet&email=test%40rest.com&movies=%5B%22I%20Love%20You%20Man%22%2C%20%22Role%20Models%22%5D',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
dd($response);
        curl_close($curl);
        print_r(json_decode($response ,true));
    }
}
