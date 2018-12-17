//////////////////////////////////////////////////////////////////
// Cases Carousel 
$('.reveal').on('open.zf.reveal', function() {
    console.log('Modal opened!');
    // Resize window to fit content
    $(window).trigger('resize');
});

$('#notificationsModal').on('closed.zf.reveal', function() { 
    console.log('Modal closed!');
});