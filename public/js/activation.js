
let booksTotal
let gameValue

    function getGameValue(){
    let gameSelected = document.getElementById("book_game")
        console.log(gameSelected.value)
        getGame(gameSelected.value)
       // generateTable(gameSelected.value)
        generatedataTable(gameSelected.value)
        $('#data-table').DataTable().ajax.reload()
 }

     function getGame(value) {
    $.get("/book/getGame/" + value,
        data => {
            $("#name").text('Nom : '+data.name)
            $("#ref").text('Référence : '+data.codeFdj)
            $("#ticketnumber").text('Nombre : '+data.ticketNumber)
            $("#ticketPrice").text('Prix unitaire : '+data.ticketPrice +' € ')
            $("#totalPrice").text('Prix total : '+data.totalPrice +' € ')

            gameValue = data.totalPrice
            console.log(gameValue)
        }
    );
}

function generateTable(value){
    $.get("/book/activation/" + value,
        data => {
            booksTotal = data.length
            console.log(booksTotal)

            setTimeout(() => { $("#totalBooks").text("Valeur total = " + booksTotal*gameValue +'€') }, 500);
            setTimeout(() => { $("#numberBooks").text("nombre de carnet = " + booksTotal) }, 500);





            // $("#tableAdd").empty()
            // for (let i = 0; i < data.length; i++) {
            //     let trimmedstr = data[i].modificationdate.replace(/\s+$/, '');
            //     let modififDate = trimmedstr.replace(/ /g, '/' );
            //
            //     let route = "https://127.0.0.1:8000/book/" + data[i].id +"/activation";
            //     console.log(route)
            //
            //     //generate link a
            //     let a = $('<a />');
            //     a.attr('href',route);
            //     a.text('Activer')
            //
            //     $("#tableAdd").append('<tr>\n' +
            //         '            <th scope="row">'+ (i+1)  +'</th>\n' +
            //         '            <td>'+ data[i].reference +'</td>\n' +
            //         '            <td>'+ modififDate+'</td>' +
            //         '            <td><a class="btn btn-success" href="'+route +'">Activer</a></td>' +
            //
            //
            //         '            </tr>'
            //
            //
            //     )
            //
            //
            // }

        }
    );


}

function generatedataTable(value){

    $('#data-table').DataTable({
        "ajax": {
            url: "/book/activation/"+value,
            type: "POST",
            dataType: 'json',
            dataSrc: "",
        },
        order: [],
        columns: [
            { data: "reference" },
            { data: "modificationdate" },
            { data: "addingDate" },
            { data: "id",
                'render': function ( data, type, row, meta ) {
                    return '<a class="btn btn-success" href="https://127.0.0.1:8000/book/'+ data +'/activation">Activer</a>';
                }
            },
        ],
        destroy: true,
    });


}




