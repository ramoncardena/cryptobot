<?php

namespace App\Console\Commands;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use App\Library\Console;
use Illuminate\Console\Command;

class CryptocompareWebsocketCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'cryptobot:websocket_cryptocompare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to the CryptoCompare websocket and store OHLC data';

    /**
     * @var currency pairs
     */
    protected $instrument;

    /**
     * @var
     */
    protected $console;

    /**
     * @var current book
     */
    public $book;

    /**
     * @var array
     */
    public $channels = array();

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
        $client = new Client(new Version2X('https://streamer.cryptocompare.com/'));
        $client->initialize();

        $client->emit('SubAdd',['subs'=>['5~CCCAGG~BTC~USD']]);
        // $client->keepAlive();
        while (true) {
            $r = $client->read();
            if (!empty($r)) {
                var_dump($r);
            }
        }
        $client->close();
        
        
    }
}
