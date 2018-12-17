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
                    <span v-model="prop24hChangePercent" :class="'coin-data-change ' + percentColor +'-box'"> {{ prop24hChangePercent }} </span> 

                    <div v-model="display['PRICE']" :class="priceColor + ' coin-data-fiat'">{{ display['PRICE'] }}</div>
                    <div class="coin-data-btc">B0.00032783</div>
                </div>

                <div class="small-4 cell text-center">
                    <!-- <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div> -->
                    <div v-model="display['HIGHHOUR']" class="coin-footer-title">High Hour: {{ display['HIGHHOUR'] }}</div>
                    <div v-model="display['LOWHOUR']" class="coin-footer-title">Low Hour: {{ display['LOWHOUR'] }}</div>
                </div>
                <div class="small-4 cell text-center">
                    <!-- <div><i class="fa fa-bar-chart" aria-hidden="true"></i></div> -->
                     <div v-model="display['HIGH24HOUR']" class="coin-footer-title">High 24h: {{ display['HIGH24HOUR'] }}</div>
                    <div v-model="display['LOW24HOUR']" class="coin-footer-title">Low 24h: {{ display['LOW24HOUR'] }}</div>
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
            display :[],
            priceColor: '',
            percentColor: '',
            propPrice: 0,
            propOpen24Hour:0 ,
            prop24hChangeTo: "",
            prop24hChangePercent: "",
            propPriceDirection: "",
            prop24hChangeDirection: "",
            propLastMarket: "",
            propTradeID: "",
            propOpenHour: 0,
            propHighHour: 0,
            propLowHour: 0,
            propOpenDay: 0,
            propHighDay: 0,
            propLowDay: 0,
            propLastTradeVolume: 0,
            propLastTradeVolumeTo: 0,
            prop24hVolume: 0,
            prop24hVolumeTo: 0,
            propUpdateData: false,
            csrf: "",
            errors: [],
            subscription: [],
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
            },
            CccCurrentFlags: {
                'PRICEUP'        : 0x1    // hex for binary 1
                , 'PRICEDOWN'      : 0x2    // hex for binary 10
                , 'PRICEUNCHANGED' : 0x4    // hex for binary 100
                , 'BIDUP'          : 0x8    // hex for binary 1000
                , 'BIDDOWN'        : 0x10   // hex for binary 10000
                , 'BIDUNCHANGED'   : 0x20   // hex for binary 100000
                , 'OFFERUP'        : 0x40   // hex for binary 1000000
                , 'OFFERDOWN'      : 0x80   // hex for binary 10000000
                , 'OFFERUNCHANGED' : 0x100  // hex for binary 100000000
                , 'AVGUP'          : 0x200  // hex for binary 1000000000
                , 'AVGDOWN'        : 0x400  // hex for binary 10000000000
                , 'AVGUNCHANGED'   : 0x800  // hex for binary 100000000000
            }
        }
    },
    props: [
    'coin',
    'fiat',
    'compact'
    ],
    computed: {

    },
    watch: {

    },
    mounted() {
        this.coinObj = JSON.parse(this.coin);

        if (this.compact) this.compactMode = true;

        this.subscription = [
            '5~CCCAGG~' + this.coinObj.symbol + '~' + this.fiat
        ];

        this.$socket.emit('SubAdd',{ subs: this.subscription });

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
    sockets:{
        connect: function(){
            console.log('socket connected')
        },
        m: function(message){     
            // console.log(message);      
            var messageType = message.substring(0, message.indexOf("~"));
            var res = {};
            var messageSplit = message.split("~");
            // console.log(messageSplit);
            if (messageSplit[2] == this.coinObj.symbol) {
                if (messageType == 5) {
                    res = this.CccCurrentUnpack(message);
                    console.log(res);
                    this.dataUnpack(res);
                    // this.$socket.disconnect();
                 }
                //console.log(message);
            }
            else {
                // this.$socket.emit('SubAdd',{ subs: ['5~CCCAGG~' + this.coinObj.symbol + '~' + 'BTC'] });
            }
            
        }
    },
    methods: {
        removeTicker: (function () {
            let uri = '/dashboard/ticker/' + this.coinObj.symbol;
            axios.delete(uri)
            .then(response => {
               window.location.replace("/dashboard");
           })
            .catch(e => {
                this.errors.push(e);
                console.log("Error: " + e.message);
            });
        }),
        dataUnpack: ( function (data) {
            var from = data['FROMSYMBOL'];
            var to = data['TOSYMBOL'];
            var fsym = from;
            var tsym = to;
            var pair = from + to;
            var currentPrice = {};
            var valid = true;

            if (!currentPrice.hasOwnProperty(pair)) {
                currentPrice[pair] = {};
            }

            for (var key in data) {
                currentPrice[pair][key] = data[key];
            }

            if (currentPrice[pair]['LASTTRADEID']) {
                currentPrice[pair]['LASTTRADEID'] = parseInt(currentPrice[pair]['LASTTRADEID']).toFixed(0);
            }

            if (currentPrice[pair]['OPEN24HOUR']) {
                this.propOpen24Hour = currentPrice[pair]['OPEN24HOUR'];
            } 
            else {
                this.propOpen24Hour = this.propOpen24Hour;
            }
            if (currentPrice[pair]['PRICE']) {
                this.propPrice = currentPrice[pair]['PRICE'];
            } 
            else {
                this.propPrice = this.propPrice;
            }
           
           currentPrice[pair]['CHANGE24HOUR'] = this.CccConvertValueToDisplay(tsym, (this.propPrice - this.propOpen24Hour));

            currentPrice[pair]['CHANGE24HOURPCT'] = ((this.propPrice - this.propOpen24Hour) / this.propOpen24Hour * 100).toFixed(2) + "%";

            this.displayData(currentPrice[pair], from, tsym, fsym);
            
        }),
        displayData: ( function (current, from, tsym, fsym) {
            console.log(current);
            var priceDirection = current['FLAGS'];
            for (var key in current) {
                if (key == 'CHANGE24HOURPCT') {
                    this.prop24hChangePercent = current[key].toString();
                    // console.log(current[key].toString());
                }
                else if (key == 'LASTVOLUMETO' || key == 'VOLUME24HOURTO') {
                    this.display[key] = this.CccConvertValueToDisplay(tsym, current[key]);
                    // console.log(key + ': ' + this.CccConvertValueToDisplay(tsym, current[key]));
                }
                else if (key == 'LASTVOLUME' || key == 'VOLUME24HOUR' || key == 'OPEN24HOUR' || key == 'OPENHOUR' || key == 'HIGH24HOUR' || key == 'HIGHHOUR' || key == 'LOWHOUR' || key == 'LOW24HOUR') {
                    this.display[key] = this.CccConvertValueToDisplay(tsym, current[key]);
                    // console.log(key + ': ' + this.CccConvertValueToDisplay(fsym, current[key]));
                }
                else {
                    if (tsym == 'BTC') {
                        this.display[key + '_BTC'] = current[key];
                    }
                    else {
                        if (key == 'PRICE') console.log('PRICE');
                        this.display[key] = current[key];
                    }
                    // console.log(key + ': ' + current[key].toString());
                }

                if (priceDirection & 1) {
                    this.propPriceDirection = "up";
                    this.priceColor = 'green';
                }
                else if (priceDirection & 2) {
                   this.propPriceDirection = "down";
                   this.priceColor = 'red';
                }
                if (current['PRICE'] > current['OPEN24HOUR']) {
                    this.prop24hChangeDirection = "up";
                    this.percentColor = 'profit';
                }
                else if (current['PRICE'] < current['OPEN24HOUR']) {
                    this.prop24hChangeDirection = "down";
                    this.percentColor = 'loss';
                }
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
        }),
        CccConvertValueToDisplay: ( function (symbol,value,type,fullNumbers) {
            var prefix = '';
            var valueSign=1;
            value = parseFloat(value);
            var valueAbs=Math.abs(value);
            var decimalsOnBigNumbers = 2;
            var decimalsOnNormalNumbers = 2;
            var decimalsOnSmallNumbers = 4;
            if(fullNumbers===true){
                decimalsOnBigNumbers =2;
                decimalsOnNormalNumbers = 0;
                decimalsOnSmallNumbers= 4;
            }
            if(symbol!=''){
                prefix = symbol+' ';
            }
            if(value<0){
                valueSign = -1;
            }

            if(value==0){
                return prefix+'0';
            }

            if(value<0.00001000 && value>=0.00000100 && decimalsOnSmallNumbers>3){
                decimalsOnSmallNumbers=3;
            }
            if(value<0.00000100 && value>=0.00000010 && decimalsOnSmallNumbers>2){
                decimalsOnSmallNumbers=2;
            }
            if(value<0.00000010 && value>=0.00000001 && decimalsOnSmallNumbers>1){
                decimalsOnSmallNumbers=1;
            }

            if(type=="short"){
                if(valueAbs>10000000000){
                    valueAbs=valueAbs/1000000000;
                    return prefix+this.CccFilterNumberFunctionPolyfill(valueSign*valueAbs,decimalsOnBigNumbers)+' B';
                }
                if(valueAbs>10000000){
                    valueAbs=valueAbs/1000000;
                    return prefix+this.CccFilterNumberFunctionPolyfill(valueSign*valueAbs,decimalsOnBigNumbers)+' M';
                }
                if(valueAbs>10000){
                    valueAbs=valueAbs/1000;
                    return prefix+this.CccFilterNumberFunctionPolyfill(valueSign*valueAbs,decimalsOnBigNumbers)+' K';
                }
                if(valueAbs>=1){
                    return prefix+this.CccFilterNumberFunctionPolyfill(valueSign*valueAbs,decimalsOnNormalNumbers);
                }
                return prefix+(valueSign*valueAbs).toPrecision(decimalsOnSmallNumbers);
            }else{
                if(valueAbs>=1){
                    return prefix+this.CccFilterNumberFunctionPolyfill(valueSign*valueAbs,decimalsOnNormalNumbers);
                }

                return prefix+this.CccNoExponents((valueSign*valueAbs).toPrecision(decimalsOnSmallNumbers));
            }
        }),
        CccFilterNumberFunctionPolyfill: ( function (value,decimals) {
            var decimalsDenominator = Math.pow(10,decimals);
            var numberWithCorrectDecimals = Math.round(value*decimalsDenominator)/decimalsDenominator;
            var parts = numberWithCorrectDecimals.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }),
        CccNoExponents: ( function (value) {
            var data= String(value).split(/[eE]/);
            if(data.length== 1) return data[0]; 

            var  z= '', sign= value<0? '-':'',
            str= data[0].replace('.', ''),
            mag= Number(data[1])+ 1;

            if(mag<0){
                z= sign + '0.';
                while(mag++) z += '0';
                return z + str.replace(/^\-/,'');
            }
            mag -= str.length;  
            while(mag--) z += '0';
            return str + z;
        })
    }
}
</script>
