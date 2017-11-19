<?php

namespace App\Console\Commands;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use App\Library\Console;
use Illuminate\Console\Command;

class BitfinexWebsocketCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptobot:websocket_bitfinex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to the bitfinex websocket and store OHLC data';

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
        $client = new Client(new Version2X('wss://streamer.cryptocompare.com/socket.io/'));
        
        $client->initialize();
        $client->emit("SubAdd", "{ subs: ['0~Poloniex~BTC~USD'] }");
        while (true) {
            $r = $client->read();
            if (!empty($r)) {
                var_dump($r);
            }
        }
        $client->close();

        // $this->console = $util = new Console();

        // #\Cache::flush();
        // #\DB::insert("DELETE FROM orca_bitfinex_ohlc WHERE instrument = 'BTC/USD'");

        // /**
        //  *  YOU CANNOT DO MULTIPLE SYMBOLS HERE.
        //  *  THEY DON'T COME IN TAGGED.
        //  */
        // $this->instruments = ['BTCUSD'];
        // $loop = \React\EventLoop\Factory::create();
        // $connector = new \Ratchet\Client\Connector($loop);

        // $connector('wss://streamer.cryptocompare.com')
        // ->then(function(\Ratchet\Client\WebSocket $conn) {
        //     foreach($this->instruments as $ins) {
        //         $conn->send('SubAdd');
        //     }
        //     $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
        //         $data = json_decode($msg,1);
        //         print_r($data);
        //     });

        //     $conn->on('close', function($code = null, $reason = null) {
        //         /** log errors here */
        //         echo "Connection closed ({$code} - {$reason})\n";
        //     });

        // }, function(\Exception $e) use ($loop) {
        //     /** hard error */
        //     echo "Could not connect: {$e->getMessage()}\n";
        //     $loop->stop();
        // });
        // $loop->run();
    }
}
