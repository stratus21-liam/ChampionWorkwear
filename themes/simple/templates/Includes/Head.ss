<head>
	<% base_tag %>
	<title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> &raquo; $SiteConfig.Title</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	$MetaTags(false)
	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

    <%-- Turned off Liam --%>
        <%-- <% require themedCSS('reset') %> --%>
        <%-- <% require themedCSS('typography') %> --%>
        <%-- <% require themedCSS('form') %> --%>
        <%-- <% require themedCSS('layout') %> --%>
        <%-- <link rel="shortcut icon" href="$resourceURL('themes/simple/images/favicon.ico')" /> --%>
    <%-- Turned off Liam --%>

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- STYLESHEETS -->
    <link rel="stylesheet" href="$themedResourceURL('css/bootstrap-select.min.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/all.min.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/themify-icons.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/flaticon_mooncart.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/swiper-bundle.min.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/nouislider.min.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/animate.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/lightgallery.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/lg-thumbnail.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/lg-zoom.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/login.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/style.css')" />
    <link rel="stylesheet" href="$themedResourceURL('css/custom.css')" />
	
	<!-- GOOGLE FONTS-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="$themedResourceURL('images/favicon/favicon-96x96.png')" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="$themedResourceURL('/favicon.svg')" />
    <link rel="shortcut icon" href="$themedResourceURL('/favicon.ico')" />
    <link rel="apple-touch-icon" sizes="180x180" href="$themedResourceURL('/apple-touch-icon.png')" />
    <link rel="manifest" href="$themedResourceURL('/site.webmanifest')" />
</head>
