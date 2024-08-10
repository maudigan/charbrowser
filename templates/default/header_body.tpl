<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir='ltr'>
   <head>
      <title>{TITLE} {SUBTITLE}</title>
      <link rel='stylesheet' href='{ROOT_URL}templates/default/style-1.9.css' type='text/css'/>
      <link rel='stylesheet' href='{ROOT_URL}templates/default/style-item-icons-1.0.css' type='text/css'/>
      <link rel='stylesheet' href='{ROOT_URL}templates/default/style-spell-icons-1.0.css' type='text/css'/>
      <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
      <meta http-equiv='Content-Style-Type' content='text/css'>
      <!-- For Chrome for Android: -->
      <link rel="icon" sizes="192x192" href="{ROOT_URL}images/favicons/touch-icon-192x192.png">
      <!-- For iPhone 6 Plus with @3× display: -->
      <link rel="apple-touch-icon-precomposed" sizes="180x180" href="{ROOT_URL}images/favicons/apple-touch-icon-180x180-precomposed.png">
      <!-- For iPad with @2× display running iOS ? 7: -->
      <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{ROOT_URL}images/favicons/apple-touch-icon-152x152-precomposed.png">
      <!-- For iPad with @2× display running iOS ? 6: -->
      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{ROOT_URL}images/favicons/apple-touch-icon-144x144-precomposed.png">
      <!-- For iPhone with @2× display running iOS ? 7: -->
      <link rel="apple-touch-icon-precomposed" sizes="120x120" href="{ROOT_URL}images/favicons/apple-touch-icon-120x120-precomposed.png">
      <!-- For iPhone with @2× display running iOS ? 6: -->
      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{ROOT_URL}images/favicons/apple-touch-icon-114x114-precomposed.png">
      <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ? 7: -->
      <link rel="apple-touch-icon-precomposed" sizes="76x76" href="{ROOT_URL}images/favicons/apple-touch-icon-76x76-precomposed.png">
      <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ? 6: -->
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{ROOT_URL}images/favicons/apple-touch-icon-72x72-precomposed.png">
      <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
      <link rel="apple-touch-icon-precomposed" href="{ROOT_URL}images/favicons/apple-touch-icon-precomposed.png"><!-- 57×57px -->  
      <link rel="icon" type="image/png" sizes="48x48" href="{ROOT_URL}images/favicons/favicon-48x48.png">
      <link rel="icon" type="image/png" sizes="32x32" href="{ROOT_URL}images/favicons/favicon-32x32.png">
      <link rel="icon" type="image/png" sizes="16x16" href="{ROOT_URL}images/favicons/favicon-16x16.png">      
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script language='JavaScript' type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js'></script>
      <script language='JavaScript' type='text/javascript' src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  
   </head>
   <body class='CB_Body'>
      <div id='charbrowser'>
         <header>
            <span class='CB_imghelper'></span>
            <a href='{INDEX_URL}'><img src="{ROOT_URL}title.php"></a>
            <form method='GET' action='{INDEX_URL}'>
               <input type='hidden' name='page' value='search'>
               <label for='name'>{L_NAME}:</label><input id='name' type='text' name='name' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{SEARCH_NAME}">
               <label for='guild'>{L_GUILD}:</label><input id='guild' type='text' name='guild' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{SEARCH_GUILD}">
               <input type='submit' value='Go'>
            </form>
            <nav>
               <ul>
                  <li><a href='{INDEX_URL}?page=signaturebuilder'>{L_SIGBUILD}</a></li>
                  <li><a href='{INDEX_URL}?page=charmove'>{L_CHARMOVE}</a></li>
                  <li><a href='{INDEX_URL}?page=bazaar'>{L_BAZAAR}</a></li>
                  <li><a href='{INDEX_URL}?page=barter'>{L_BARTER}</a></li>
                  <li><a href='{INDEX_URL}?page=adventure'>{L_LEADERBOARD}</a></li>
                  <li><a href='{INDEX_URL}?page=server'>{L_SERVER}</a></li>
                  <li><a href='{INDEX_URL}?page=settings'>{L_SETTINGS}</a></li>
                  <li><a href='http://mqemulator.net/forum2/viewforum.php?f=20'>{L_REPORT_ERRORS}</a></li>
                  <li><a href='{INDEX_URL}?page=help'>{L_HELP}</a></li>
               </ul>
            </nav>
         </header>
         <main>
            <div>
            {ADVERTISEMENT}
            </div>

  