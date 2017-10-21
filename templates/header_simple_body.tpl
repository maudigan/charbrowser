<link rel='stylesheet' href='{ROOT_URL}templates/style.css' type='text/css'/>

<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_NAVIGATE}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner'>
    
      <br>
      <center>
         <form method='GET' action='{INDEX_URL}'>
            <input type='hidden' name='page' value='search'>
            {L_NAME}: <input class='SigBuilder' type='text' name='name' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {L_GUILD}: <input class='SigBuilder' type='text' name='guild' >&nbsp;&nbsp;&nbsp;&nbsp;
            <input style='vertical-align:middle' type='image' src='{ROOT_URL}images/go.gif'>
         </form>
      </center>
      <br>

      <font style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;'>
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}?page=signaturebuilder'>{L_SIGBUILD}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}?page=charmove'>{L_CHARMOVE}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}?page=bazaar'>{L_BAZAAR}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}'>{L_HOME}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}?page=settings'>{L_SETTINGS}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='http://mqemulator.net/forum2/viewforum.php?f=20'>{L_REPORT_ERRORS}</a> &nbsp;|&nbsp; 
      <a style='font-family:arial;font-size:7pt;text-decoration:none;font-weight;none;color:#FFFFFF;' href='{INDEX_URL}?page=help'>{L_HELP}</a>
    </div>
  </div>
</center>






<div class='body_simple'>
<br><br>


  