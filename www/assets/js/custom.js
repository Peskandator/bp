$(document).ready(function(){
    const flashTimeout = setTimeout(flashMessage, 4200);
    function flashMessage() {
        $(".alert").alert('close');
    }
});

