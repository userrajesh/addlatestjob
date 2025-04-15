jQuery(document).ready(function ($) {
    $('#jobTable').DataTable({
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        }
    });
});
