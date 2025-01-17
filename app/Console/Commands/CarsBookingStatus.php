<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CarsBookingDateStatus;
use Carbon\Carbon;

class CarsBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carstatus:everyMinute';

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
     * @return int
     */
    public function handle()
    {
        
        $carHoldStatus = CarsBookingDateStatus::get();
        
        if ($carHoldStatus) {
           
            foreach ($carHoldStatus as $value) {
                $created_at = Carbon::parse($value->created_at);
                $currentTime = Carbon::now(); 
    
                $timeDifference = $currentTime->diffInMinutes($created_at);
    
                if ($timeDifference >= 9) {
                    
                    CarsBookingDateStatus::where('carId', '=', $value->carId)->delete();
                  
                } else {
                   
                }
            }
        }
        $this->info('Deleted successfully');
    }
}
