{if $MENU != ''}
	
	<!-- Menu -->
	<div class="sf-contener clearfix">
    	<div class="cat-title">{l s="Categories" mod="blocktopmenu"}</div>
		<ul class="sf-menu clearfix menu-content">
			{$MENU}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')|escape:'html'}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>

	<!--/ Menu -->
    {literal}
    <script type="text/javascript">
		$(document).ready(function() {
			categoryMenu = $('ul.sf-menu');        //var rich menu
			categoryMenu.superfish();				   //menu initialization
			$('.sf-menu > li > ul').addClass('container'); //add class for width define
			i = 0;
			$('.sf-menu > li > ul > li:not(#category-thumbnail)').each(function() {  //add classes for clearing
                i++;
				if(i%2==1)
					$(this).addClass('first-in-line-xs');
				else if (i%5==1)
					$(this).addClass('first-in-line-lg');
            });
        });
		
		// accordion for definition smaller that 767px
		
		responsiveflagMenu = false;
		function menuChange(status){
			if(status == 'enable'){
				$('.sf-menu > li > ul').removeAttr('style');
				$('.sf-menu').removeAttr('style');
				$('.sf-contener .cat-title').on('click', function(){
					$(this).toggleClass('active').parent().find('ul.menu-content').stop().slideToggle('medium');
				}),
				$('.sf-menu > li:has(ul)').each(function() {
                    $(this).prepend('<span></span>'),
					$(this).find('span').on('click touchend', function(){
					  	categoryMenu.superfish('hide');
					});
                });
			}else{
				$('.sf-contener .cat-title').off();	
				$('.sf-menu').removeAttr('style');
				$('.sf-menu > li > ul').removeAttr('style');
				$('.sf-contener .cat-title').removeClass('active');
			}
		}
		
		function responsiveMenu(){
		   if ($(document).width() <= 767 && responsiveflagMenu == false){
				menuChange('enable'),
				responsiveflagMenu = true	
			}
			else if ($(document).width() >= 768){
				menuChange('disable'),
				responsiveflagMenu = false
			}
		}
		$(document).ready(responsiveMenu);
		$(window).resize(responsiveMenu);
	</script>
     {/literal}
{/if}
