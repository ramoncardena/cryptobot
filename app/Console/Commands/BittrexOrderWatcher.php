<?php

namespace App\Console\Commands;

use App\Library\Console;
use Illuminate\Console\Command;
use Exception;

use App\User;
use App\Order;
use App\Events\OrderCompleted;
use App\Library\Services\Facades\Bittrex;

class BittrexOrderWatcher extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:bittrex_order_watcher';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watch orders at Bittrex.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->console = $util = new Console();

		stream_set_blocking(STDIN, 0);
		while(1) {
			if(ord(fgetc(STDIN)) == 113) {
				echo "QUIT detected...";
				return null;
			}

			try {
				// Get orders to watch completion
				$orders = Order::where('exchange', 'bittrex')
						->get();

				// Iterate through all pairs to check last price
				foreach ($orders as $order) {
					// Get user
					$user = User::find($order->user_id);

					// Initialize Bittrex API
					Bittrex::setAPI($user->settings()->get('bittrex_key'), $user->settings()->get('bittrex_secret'));

					// Get order from Bittrex
					$onlineOrder = Bittrex::getOrder($order->order_id)->result;

					// If order is closed then close trade
					if ($onlineOrder->Closed != null) {
						event(new OrderCompleted($order, $onlineOrder->Price));
					}
					
				}
				print_r(".");

			} catch(\Exception $e) {
				var_dump( $e->getMessage());
				sleep(1);
				continue;
			}
			sleep(10);
		}
	}
}
