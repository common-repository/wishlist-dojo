jQuery(function($){
	// INSTANTIATE MIXITUP ON THE DECK
    $('#wlm_plugins').mixitup({
				animation: {
				duration: 240,
				effects: 'fade translateZ(-360px) stagger(138ms) translateX(10%) scale(0.47)',
				easing: 'cubic-bezier(0.39, 0.575, 0.565, 1)'
			}
		});
});


jQuery(document).ready(function($){

    $(".license_info").hide();
    $(".show_hide").show();

    $('.show_hide').click(function(){
        $(this).next().slideToggle();
    });

	$('.license_hide').click(function(){
		$(".license_info").slideUp();
	});

});




