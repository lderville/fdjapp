
getGameValue()

    function getGameValue(){
    let gameSelected = document.getElementById("book_game")
        console.log(gameSelected.value)
        getGame(gameSelected.value)
 }

     function getGame(value) {
    $.get("/book/getGame/" + value,
        data => {
            $("#name").text('Nom : '+data.name)
            $("#ref").text('Référence : '+data.codeFdj)
            $("#ticketnumber").text('Nombre : '+data.ticketNumber)
            $("#ticketPrice").text('Prix unitaire : '+data.ticketPrice +' € ')
            $("#totalPrice").text('Prix total : '+data.totalPrice +' € ')
        }
    );
}

