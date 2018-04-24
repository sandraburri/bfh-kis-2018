window.chartColors = {
	red: 'rgb(255, 99, 132, 600)',
	blue: 'rgb(54, 162, 235, 800)',
	grey: 'rgb(201, 203, 207, 66)'
};

function newDate(days) {
    return moment().add(days, 'd').toDate();
}

function newDateString(days) {
    return moment().add(days, 'd').format();
}

var color = Chart.helpers.color;
var config = {
    type: 'line',
    data: {
        datasets: [{
            label: 'Puls',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
            borderColor: window.chartColors.red,
            fill: false,
            data: vitalSignsData.pulse
        }, {
            label: 'Temperatur',
            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.blue,
            fill: false,
            data: vitalSignsData.temperature
        }]
    },
    options: {
        responsive: true,
        title: {
            display: false,
            text: 'Chart.js Time Point Data'
        },
        scales: {
            xAxes: [{
                type: 'time',
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: ''
                },
                ticks: {
                    major: {
                        fontStyle: 'bold',
                        fontColor: '#FF0000'
                    }
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: ''
                }
            }]
        }
    }
};

window.onload = function() {
    var ctx = document.getElementById('vitalsigns-chart').getContext('2d');
    window.myLine = new Chart(ctx, config);
};
