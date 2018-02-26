<template>
    <section id="coin-card">
        <div class="grid-container fluid">
            <div class="grid-x grid-padding-x align-middle">

                <div class="small-4 cell coin-header">
                    <div class="nowrap coin-symbol"><img :src="coinObj.imageurl" alt="" width="20"> {{ coinObj.symbol }} </div>
                </div>

                <div class="small-8 cell coin-header text-right">
                    <span class="coin-name"> {{ coinObj.coinname }}</span>
                    <span class="coin-card-close" aria-hidden="true" v-on:click="removeTicker()">&times;</span>
                </div>

                <div class="small-8 cell">
                    <div :class="'ct-chart-coin' + coinObj.symbol + ' ct-major-eleventh'"></div>
                </div>

                <div class="small-4 cell text-center">
                    <span class="coin-data-change profit-box"> +67.856% </span> 
                    <div class="coin-data-fiat">€8235.47</div>
                    <div class="coin-data-btc">B0.00032783</div>
                </div>

                <div class="small-4 cell text-center">
                    <!-- <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div> -->
                    <div class="coin-footer-title">Market Cap</div>
                    <div class="coin-footer-value">€6.566.732.210</div>
                </div>
                <div class="small-4 cell text-center">
                    <!-- <div><i class="fa fa-bar-chart" aria-hidden="true"></i></div> -->
                    <div class="coin-footer-title">24h Vol.</div>
                    <div class="coin-footer-value">€514.987.168,00</div>
                </div>
                <div class="small-4 cell text-center">
                    <!-- <div><i class="fa fa-money" aria-hidden="true"></i></div> -->
                    <div class="coin-footer-title">Supply</div>
                    <div class="coin-footer-value">€25.927.070.538</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

export default {
    name: 'coin-card',
    data: () => {
        return {
            coinObj: {},
            chartClass: "",
            chartistCoinChart: {},
            responsiveOptions: [],
            chartistCoinChartData: {labels: [], series: []},
            chartistCoinChartOptions: {},
            compactMode: false,
            updating: false,
            csrf: "",
            CccCurrentFields: {
                  'TYPE'            : 0x0       // hex for binary 0, it is a special case of fields that are always there
                , 'MARKET'          : 0x0       // hex for binary 0, it is a special case of fields that are always there
                , 'FROMSYMBOL'      : 0x0       // hex for binary 0, it is a special case of fields that are always there
                , 'TOSYMBOL'        : 0x0       // hex for binary 0, it is a special case of fields that are always there
                , 'FLAGS'           : 0x0       // hex for binary 0, it is a special case of fields that are always there
                , 'PRICE'           : 0x1       // hex for binary 1
                , 'BID'             : 0x2       // hex for binary 10
                , 'OFFER'           : 0x4       // hex for binary 100
                , 'LASTUPDATE'      : 0x8       // hex for binary 1000
                , 'AVG'             : 0x10      // hex for binary 10000
                , 'LASTVOLUME'      : 0x20      // hex for binary 100000
                , 'LASTVOLUMETO'    : 0x40      // hex for binary 1000000
                , 'LASTTRADEID'     : 0x80      // hex for binary 10000000
                , 'VOLUMEHOUR'      : 0x100     // hex for binary 100000000
                , 'VOLUMEHOURTO'    : 0x200     // hex for binary 1000000000
                , 'VOLUME24HOUR'    : 0x400     // hex for binary 10000000000
                , 'VOLUME24HOURTO'  : 0x800     // hex for binary 100000000000
                , 'OPENHOUR'        : 0x1000    // hex for binary 1000000000000
                , 'HIGHHOUR'        : 0x2000    // hex for binary 10000000000000
                , 'LOWHOUR'         : 0x4000    // hex for binary 100000000000000
                , 'OPEN24HOUR'      : 0x8000    // hex for binary 1000000000000000
                , 'HIGH24HOUR'      : 0x10000   // hex for binary 10000000000000000
                , 'LOW24HOUR'       : 0x20000   // hex for binary 100000000000000000
                , 'LASTMARKET'      : 0x40000   // hex for binary 1000000000000000000, this is a special case and will only appear on CCCAGG messages
            }
        }
    },
    props: [
    'coin',
    'compact'
    ],
    computed: {

    },
    watch: {

    },
    sockets:{
        connect: function(){
            console.log('socket connected')
        },
        m: function(message){

            var messageType = message.substring(0, message.indexOf("~"));
            var res = {};
            if (messageType == 5) {
                res = this.CccCurrentUnpack(message);
                console.log(res);
                 //console.log(this.dataUnpack(res));
            }
            //console.log(message);
        }
    },
    mounted() {
        this.coinObj = JSON.parse(this.coin);

        if (this.compact) this.compactMode = true;

        this.$socket.emit('SubAdd',{ subs: ['5~CCCAGG~' + this.coinObj.symbol+ '~EUR'] });

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log('Component CoinCard mounted.');
    },
    updated(){

        this.chartistCoinChartOptions = {
            low: 0,
            showArea: true,
            showLine: true,
            showPoint: false,
            fullWidth: true,
            chartPadding: { 
                top: 5,
                bottom: -35,
                right: 5,
                left: -35 
            },
            axisX: {
                showLabel: false,
                showGrid: false
            },
            axisY: {
                showLabel: false,
                showGrid: false
            }
        };
        this.chartistCoinChartData.series = [
        [5, 9, 7, 8, 15, 11, 9, 14]
        ]
        // this.chartistCoinChart = new Chartist.Line('.ct-chart-coin', this.chartistCoinChartData, this.chartistCoinChartOptions,this.responsiveOptions);ç


        this.chartClass = '.ct-chart-coin' + this.coinObj.symbol;

        this.chartistCoinChart = new Chartist.Line(this.chartClass, this.chartistCoinChartData, this.chartistCoinChartOptions);
    },
    methods: {
        removeTicker: (function () {

        }),
        dataUnpack: ( data => {
            var from = data['FROMSYMBOL'];
            var to = data['TOSYMBOL'];
            var fsym = CCC.STATIC.CURRENCY.getSymbol(from);
            var tsym = CCC.STATIC.CURRENCY.getSymbol(to);
            var pair = from + to;

            if (!currentPrice.hasOwnProperty(pair)) {
                currentPrice[pair] = {};
            }

            for (var key in data) {
                currentPrice[pair][key] = data[key];
            }

            if (currentPrice[pair]['LASTTRADEID']) {
                currentPrice[pair]['LASTTRADEID'] = parseInt(currentPrice[pair]['LASTTRADEID']).toFixed(0);
            }
            currentPrice[pair]['CHANGE24HOUR'] = CCC.convertValueToDisplay(tsym, (currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']));
            currentPrice[pair]['CHANGE24HOURPCT'] = ((currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']) / currentPrice[pair]['OPEN24HOUR'] * 100).toFixed(2) + "%";;
            displayData(currentPrice[pair], from, tsym, fsym);
        }),
        displayData: ( (current, from, tsym, fsym) => {
            var priceDirection = current.FLAGS;
            for (var key in current) {
                if (key == 'CHANGE24HOURPCT') {
                    console.log(current[key]);
                }
                else if (key == 'LASTVOLUMETO' || key == 'VOLUME24HOURTO') {
                    console.log(CCC.convertValueToDisplay(tsym, current[key]));
                }
                else if (key == 'LASTVOLUME' || key == 'VOLUME24HOUR' || key == 'OPEN24HOUR' || key == 'OPENHOUR' || key == 'HIGH24HOUR' || key == 'HIGHHOUR' || key == 'LOWHOUR' || key == 'LOW24HOUR') {
                    console.log(CCC.convertValueToDisplay(fsym, current[key]));
                }
                else {
                    console.log(current[key]);
                }
            }

            $('#PRICE_' + from).removeClass();
            if (priceDirection & 1) {
                $('#PRICE_' + from).addClass("up");
            }
            else if (priceDirection & 2) {
                $('#PRICE_' + from).addClass("down");
            }
            if (current['PRICE'] > current['OPEN24HOUR']) {
                $('#CHANGE24HOURPCT_' + from).removeClass();
                $('#CHANGE24HOURPCT_' + from).addClass("up");
            }
            else if (current['PRICE'] < current['OPEN24HOUR']) {
                $('#CHANGE24HOURPCT_' + from).removeClass();
                $('#CHANGE24HOURPCT_' + from).addClass("down");
            }
        }),
        CccCurrentUnpack: ( function (value) {
            var valuesArray = value.split("~");
            var valuesArrayLenght = valuesArray.length;
            var mask = valuesArray[valuesArrayLenght-1];
            var maskInt = parseInt(mask,16);
            var unpackedCurrent = {};
            var currentField = 0;

            for(var property in this.CccCurrentFields)
            {
                if(this.CccCurrentFields[property] === 0)
                {
                    unpackedCurrent[property] = valuesArray[currentField];
                    currentField++;
                }
                else if(maskInt&this.CccCurrentFields[property])
                {

                    if(property === 'LASTMARKET'){
                        unpackedCurrent[property] = valuesArray[currentField];
                    }else{
                        unpackedCurrent[property] = parseFloat(valuesArray[currentField]);
                    }
                    currentField++;
                }
            }

            return unpackedCurrent;
        })
    }
}
</script>
