<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Order;
use App\Events\OrderCompleted;
use App\Library\Services\Facades\Bittrex;
use Illuminate\Support\Facades\Log;

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


		// Log INFO: BitttrexOrderWatcher launched
		Log::info("Bittrex Order Watcher launched.");

		stream_set_blocking(STDIN, 0);

		while(1) {

			if(ord(fgetc(STDIN)) == 113) {

				// Log INFO: BitttrexOrderWatcher stopped
				Log::info("Bittrex Order Watcher halted by user (CTRL+C).");
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
					$onlineOrder = Bittrex::getOrder($order->order_id);

					// Check for success on API call
					if (! $onlineOrder->success) {

						// Log ERROR: Bittrex API returned error
						Log::error("Bittrex API: " . $onlineOrder->message);

					}
					else {

						$onlineOrder = $onlineOrder->result;

						// If order is closed then close trade
						if ($onlineOrder->Closed != null) {

							// Event: OrderCompleted
							event(new OrderCompleted($order, $onlineOrder->PricePerUnit));

							// Log NOTICE: Order closed at Bittrex 
							Log::notice("Order Completed: Order " . $order->order_id . " at " . $order->exchange . " closed with closing price " . $onlineOrder->PricePerUnit);
						}
					}
					
				}

			} catch(Exception $e) {
				
				// Log CRITICAL: Exception
				Log::critical("BittrexOrderWatcher Exception: " . $e->getMessage());

				sleep(1);
				continue;
			}
			
			sleep(10);
		}
	}
}
