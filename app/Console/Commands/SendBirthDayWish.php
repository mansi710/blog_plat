<?php

namespace App\Console\Commands;

use App\Mail\BirthDayWish;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendBirthDayWish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:send-birth-day-wish';
    protected $signature = 'send:birthdaywish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For Sending Birthday Wish to User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    
         $users = User::whereMonth('dob', date('m'))
                    ->whereDay('dob', date('d'))
                    ->get();

        if ($users->count() > 0) {
            foreach ($users as $user) {
                Mail::to($user)->send(new BirthDayWish($user));
            }
        }
  
        return 0;
        // $users = User::select('*')->get();
        // foreach ($users as $user) {
        //     //write your own logics
        //     Mail::to($user['email'])->send(new BirthDayWish($user));
        // }

        // return 0;
    }
}
