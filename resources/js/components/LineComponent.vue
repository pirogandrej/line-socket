<template>
    <div class="row justify-content-center">

        <div class="col-md-2">

            <button class="btn btn-primary" v-on:click="toggleButton">{{ buttonTitle }}</button>

        </div>
        <div class="col-md-10">

            <div id="chartdiv" v-if="seen"></div>

            <div class="input-group mb-3" v-if="seen">
                <input type="text" class="ml-3 mr-3" placeholder="" v-model="message"/>
                <br/>
                <div class="input-group-append">
                    <button @click="sendMessage" class="btn btn-outline-secondary" type="button">Изменить</button>
                </div>
            </div>

            <div v-if="seen">
                <br>
                <hr>
                <h6>Новые поля</h6>
                <br>
                <form @submit.prevent="addObject">
                    <div class="form-group">
                        <input type="text" class="" id="object" name="object"  placeholder="Object" v-model="formData.object">
                    </div>
                    <div class="form-group">
                        <input type="text" class="" id="value" name="value"  placeholder="Value" v-model="formData.value">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</template>

<script>
    import io from 'socket.io-client';
    import AmExportCss from 'amcharts3/amcharts/plugins/export/export.css';
    import AmCharts from 'amcharts3/amcharts/amcharts';
    import AmSerial from 'amcharts3/amcharts/serial';
    import AmExport from 'amcharts3/amcharts/plugins/export/export.min';
    var socket = io(':6001');

    export default {
        props: ['messages'],
        data() {
            return {
                chart: {},
                message: '',
                seen: true,
                buttonTitle: "Спрятать график",
                formData: {
                    object: '',
                    value: ''
                }
            }
        },

        mounted() {
            this.showChartGraph(this.messages);
            var obj = this;
            socket.on('object:message', function (data) {
                console.log(data);
                obj.messages.forEach(function(item, i) {
                    if(item.object == data.object)
                    {
                        obj.messages[i].value = data.value;
                    }
                });
//                obj.chart.dataProvider = obj.messages;
//                obj.chart.validateData();
            });
        },

        methods: {
            toggleButton: function () {
                this.seen = !this.seen;
                if(this.seen)
                {
                    this.buttonTitle = 'Спрятать график';
                    axios.get('messages')
                    .then(response => {
                        this.showChartGraph(response.data);
                    });
                }
                else
                {
                    this.buttonTitle = 'Показать график';
                }
            },
            addObject: function() {
                axios.post('messages', {
                    object: this.formData.object,
                    value: this.formData.value
                })
                .then(response => {
                    console.log(response.data);
                    this.formData.object = '';
                    this.formData.value = '';
                })
            },
            sendMessage: function () {
                var nameObject = this.message.split('=')[0];
                var nameObjectUpper = nameObject.toUpperCase();
                var valueObject = this.message.split('=')[1];
                var valueObjectUpper = valueObject.toUpperCase();
                var obj = this;
                var indexField = 0;
                this.messages.forEach(function(item, i) {
                    if(item.object == nameObjectUpper)
                    {
                        indexField = i;
                        axios.patch('messages/' + obj.messages[i].id, {
                            value: valueObjectUpper
                        })
                        .then(response=>{
//                            console.log(response.data);
                        })
                    }
                });
                this.animeGraph(this.messages, indexField, valueObjectUpper);
            },
            pauseBrowser: function(millis) {
                var date = Date.now();
                var curDate = null;
                do {
                    curDate = Date.now();
                } while (curDate-date < millis);
            },
            animeGraph: function(data, index, value){
                console.log('datadatadatadatadatadatadata');
                console.log(data);
                console.log('indexindexindexindexindexindex');
                console.log(index);
                console.log('old value');
                console.log(data[index].value);
                console.log('new value');
                console.log(value);
                var delta = Math.round((parseInt(value) - parseInt(data[index].value))/10);
                var obj = this;
                for(var i = 1;i<=10;i++){
                    obj.messages[index].value = parseInt(this.messages[index].value) + parseInt(delta);
//                    obj.chart.dataProvider = this.messages;
//                    obj.chart.validateData();
                    console.log('changechangechangechangechangechange------------');
                    console.log(obj.messages[index].value);
                    obj.pauseBrowser(1000);
                }
//                this.messages[index].value = value;
//                this.chart.dataProvider = this.messages;
//                this.chart.validateData();
            },
            showChartGraph: function (data) {
                this.chart = window.AmCharts.makeChart("chartdiv", {
                    "type": "serial",
                    "theme": "light",
                    "marginRight": 40,
                    "marginLeft": 40,
                    "autoMarginOffset": 20,
                    "mouseWheelZoomEnabled":true,
                    "valueAxes": [{
                        "id": "v1",
                        "axisAlpha": 0,
                        "position": "left",
                        "ignoreAxisWidth":true
                    }],
                    "balloon": {
                        "adjustBorderColor": true,
                        "color": "#000000",
                        "cornerRadius": 5,
                        "fillColor": "#56b2d4",
                        "fillAlpha": 0.5
                    },
                    "graphs": [{
                        "id": "g1",
                        "lineColor": '#0089b7',
                        "lineThickness": 1,
                        "title": "red line",
                        "fillAlphas": 0.1,
                        "fontSize": 12,
                        "useLineColorForBulletBorder": true,
                        "valueField": "value",
                        "balloonText": "<span>Value : <b>[[value]]</b></span>"
                    }],
                    "chartScrollbar": {
                        "graph": "g1",
                        "oppositeAxis":true,
                        "offset":30,
                        "scrollbarHeight": 80,
                        "backgroundAlpha": 0,
                        "selectedBackgroundAlpha": 0.1,
                        "selectedBackgroundColor": "#888888",
                        "graphFillAlpha": 0,
                        "graphLineAlpha": 0.5,
                        "selectedGraphFillAlpha": 0,
                        "selectedGraphLineAlpha": 1,
                        "autoGridCount":true,
                        "color":"#AAAAAA"
                    },
                    "chartCursor": {
                        "pan": true,
                        "valueLineEnabled": false,
                        "valueLineBalloonEnabled": false,
                        "cursorAlpha":0.2,
                        "cursorColor":"#000000",
                        "limitToGraph":"g1",
                        "valueLineAlpha":0.2,
                        "valueZoomable":false
                    },
                    "valueScrollbar":{
                        "oppositeAxis":false,
                        "offset":50,
                        "scrollbarHeight":10
                    },
                    "categoryField": "object",
                    "categoryAxis": {
                        "parseDates": false,
                        "dashLength": 1,
                        "minorGridEnabled": true
                    },
                    "export": {
                        "enabled": true
                    },
                    "dataProvider": data
                });
            }
        }
    }
</script>

<style scoped>
    #chartdiv {
        width	: 100%;
        height	: 500px;
    }
</style>