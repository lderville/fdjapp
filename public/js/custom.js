// =============  Data Table - (Start) ================= //

$(document).ready(setTimeout (function(){
    var table = $('#data-table').DataTable({
        
        buttons:['copy', 'csv', 'excel', 'pdf', 'print']
        
    });

    table.buttons().container()
    .appendTo('#export');
    $("div.dataTables_length select").val(100).trigger('change');


}, 200));




// =============  Data Table - (End) ================= //
