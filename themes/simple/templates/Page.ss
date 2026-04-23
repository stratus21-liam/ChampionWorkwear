<!DOCTYPE html>
<!--
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
Simple. by Sara (saratusar.com, @saratusar) for Innovatif - an awesome Slovenia-based digital agency (innovatif.com/en)
Change it, enhance it and most importantly enjoy it!
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
-->

<!--[if !IE]><!-->
<html lang="$ContentLocale">
	<!--<![endif]-->
	<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
	<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
	<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->

	<% include Head %>

	
	<body class="$ClassName.ShortName<% if not $Menu(2) %> no-sidebar<% end_if %>" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
		<div class="page-wraper">
			
			<%-- Without this check error messages appear for non logged in users trying to reset their password --%>
			<% if ControllerClass == "SilverStripe\ErrorPage\ErrorPage" %>
				$Layout
			<% else %>
				<%-- Does the customer logging in have a customer account? --%>
				<% if CurrentMember.HasCustomerAccount %>
					<%-- Is that account active? --%>
					<% if CurrentMember.HasActiveCustomerAccount && CurrentMember.MemberAccountActive %>
						<% include Header %>
							<div class="page-content">
								$Layout
							</div>
						<% include Footer %>
					<% else %>
						<% include CustomerAccountNotActiveError %>
					<% end_if %>				
				<% else %>
					<div class="page-content">
						<% include CustomerAccountError %>
					</div>
				<% end_if %>
				<%-- <% require javascript('//code.jquery.com/jquery-3.7.1.min.js') %> --%>
				<%-- <% require themedJavascript('script') %> --%>			
			<% end_if %>


		</div>
		<% include Scripts %>

	</body>
</html>
