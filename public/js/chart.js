
generateTable()

function generateTable(){
    $.get("/graphique/data",
        data => {
            console.log(data['years'])

            new Chart(document.getElementById("line-chart"), {


                type: 'line',
                data: {
                    labels: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
                    datasets: getCharts(data,'years')
                },
                options: {
                    title: {
                        display: true,
                        text: 'Chiffre d\'affaire : activation'
                    }
                }
            });

            new Chart(document.getElementById("line-chart2"), {

                type: 'line',
                data: {
                    labels: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
                    datasets: getCharts(data, 'count')
                },
                options: {
                    title: {
                        display: true,
                        text: 'Nombre de ticket activé'
                    }
                }
            });
console.log(data['bestSeller'][1]['name'])
            google.charts.load("current", {packages:["corechart"]});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                let datas = google.visualization.arrayToDataTable([
                    ['package', 'number'],
                    [data['bestSeller'][0]['name'], data['bestSeller'][0][1]],
                    [data['bestSeller'][1]['name'], data['bestSeller'][1][1]],
                    [data['bestSeller'][2]['name'], data['bestSeller'][2][1]],
                    [data['bestSeller'][3]['name'], data['bestSeller'][3][1]],
                    [data['bestSeller'][4]['name'], data['bestSeller'][4][1]],

                ]);

                var options = {
                    title: 'Meilleurs Ventes',
                    is3D: true,
                    backgroundColor: 'transparent',
                    'width':600,
                    'height':450,

                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                chart.draw(datas, options);
            }




        }
    );

}


function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function getCharts(data, libelle){
    let datasets = []
    for (var year in data[libelle]) {


        let dataset = {
            data: [data[libelle][year][1],data[libelle][year][2],data[libelle][year][3],
                data[libelle][year][4],data[libelle][year][5],data[libelle][year][6],
                data[libelle][year][7],data[libelle][year][8],data[libelle][year][9],
                data[libelle][year][10],data[libelle][year][11],data[libelle][year][12]],
            label: year.toString(),
            borderColor: getRandomColor(),
            fill: false
        }
        datasets.push(dataset)
    }
    // console.log(datasets)
    return datasets
}


