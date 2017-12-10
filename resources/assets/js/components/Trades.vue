<div class="trades-area">

    <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">

        <li class="accordion-item is-active" data-accordion-item>

            <a href="#" class="accordion-title text-center">Trading Panel</a>

            <div class="accordion-content" data-tab-content >
                <tradepanel></tradepanel>
            </div>

        </li>

        <li class="accordion-item is-active" data-accordion-item>

            <a href="#" class="accordion-title  text-center">Opened Trades</a>

            <div class="accordion-content" data-tab-content >
                <tradelist2 type="opened" :trades="{{ $tradesOpened }}"></tradelist2>
            </div>

        </li>

        <li class="accordion-item is-active" data-accordion-item>

            <a href="#" class="accordion-title text-center">Conditional Trades</a>

            <div class="accordion-content" data-tab-content >
                <tradelist2 type="waiting" :trades="{{ $tradesWaiting }}"></tradelist2>
            </div>

        </li>

        <li class="accordion-item is-active" data-accordion-item>

            <a href="#" class="accordion-title text-center">Trade History</a>
            
            <div class="accordion-content" data-tab-content >
                <tradelist2 type="history" :trades="{{ $tradesHistory }}"></tradelist2>
            </div>

        </li>
    </ul>
</div>