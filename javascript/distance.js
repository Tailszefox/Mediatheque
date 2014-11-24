(function($) {
		$.fn.distance = function() {
			return this.each(function() {
					$(this).mousemove(function(e){
							var x = e.pageX - this.offsetLeft;
							var milieu = (this.offsetWidth/2);
							var distance = Math.abs(x - milieu);
							
							if((100-distance) > 70)
							{
								$("#gauche, #milieu, #droite").css("opacity", (distance/100));
								
								$(this).css("font-size", (100-distance) + "%");
								$(this).css("opacity", 100);
							}
							else
							{
								$(this).css("font-size", "70%");
								$("#gauche, #milieu, #droite ").css("opacity", "1");
							}
					});
					
					$(this).mouseout(function(){
							$(this).css("font-size", "70%");
							$("#gauche, #milieu, #droite").css("opacity", "1");
					});
			});
		};
})(jQuery);
