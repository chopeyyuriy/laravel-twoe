<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-active:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = DB::table('users')->select('id', 'active')->get();
        foreach ($users as $user) {
            if (!$user->active == 1) {
                $user_orders = DB::table('orders')->where('user_id', $user->id)->where('status', 'done')->count();
                if ($user_orders >= 50) {
                    DB::table('users')->where('id', $user->id)->update(['active' => 1]);
                } else {
                    DB::table('users')->where('id', $user->id)->update(['active' => 0]);
                }
            }
        }
    }
}
