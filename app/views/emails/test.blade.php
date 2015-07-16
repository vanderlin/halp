<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="format-detection" content="address=no;email=no;telephone=no">
    @include('emails.style')
    <title>Halp</title>
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">

    <div class="emailSummary">
        {{$task->claimer->getShortName()}} has claimed one of your tasks!    
    </div>

    <table id="emailBody" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody><tr>
    <td align="center" valign="top" class="emailBodyCell"><table width="544" border="0" cellpadding="0" cellspacing="0" class="eBox">
        <tbody><tr>
          <td class="topCorners"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody><tr>
                <td align="left" valign="top" class="crn_lftp"><img src="images/header_left.png" alt="" width="8" height="8"></td>
                <td class="emptyCell space16">&nbsp;</td>
                <td align="right" valign="top" class="crn_rgtp"><img src="images/header_right.png" alt="" width="8" height="8"></td>
              </tr>
            </tbody></table></td>
        </tr>
        <tr>
          <td class="eHeader"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody><tr>
                <td class="eHeaderLogo"><a class="logo" href="#"><img class="imageFix" src="images/logo.png" width="200" height="48" alt="SimpleApp"></a></td>
                <!-- end .eHeaderLogo-->
                <td class="eHeaderOptions"><table border="0" cellpadding="0" cellspacing="0" class="optionsButton" align="right">
                    <tbody><tr>
                      <td align="left" valign="top" class="btnLfTp"><img src="images/opBtn_lftp.png" width="4" height="4"></td>
                      <td class="emptyCell">&nbsp;</td>
                      <td class="emptyCell mobileHide">&nbsp;</td>
                      <td width="4" height="8" align="right" valign="top" class="btnRgTp"><img src="images/opBtn_rgtp.png" width="4" height="4"></td>
                    </tr>
                    <tr>
                      <td class="emptyCell">&nbsp;</td>
                      <td class="btnIcon"><a href="#"><img src="images/sub_options.png" width="16" height="16" alt="Options"></a></td>
                      <td class="btnMain mobileHide"><a href="#"><span>Notification Options</span></a></td>
                      <td class="emptyCell">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="left" valign="bottom" class="btnLfBt"><img src="images/opBtn_lfbt.png" width="4" height="4"></td>
                      <td class="emptyCell">&nbsp;</td>
                      <td class="emptyCell mobileHide">&nbsp;</td>
                      <td align="right" valign="bottom" class="btnRgBt"><img src="images/opBtn_rgbt.png" width="4" height="4"></td>
                    </tr>
                  </tbody></table></td>
                <!-- end .eHeaderOptions--> 
              </tr>
            </tbody></table></td>
        </tr>
        <tr>
          <td class="highlight pdTp32">
            <h1>{{$task->claimer->getShortName()}} has claimed one of your tasks!</h1>
            <hr>
            <h3>You asked for help with:</h3>
            
            <h1>{{link_to($task->getURL(), $task->title)}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
            <p>You estimated this task would take {{$task->duration}}. Go talk to {{link_to($task->creator->getURL(), $task->creator->firstname)}} or reply direclty to this e-mail, and happy task-ing!</p>
        </td>
          <!-- end .highlight--> 
        </tr>
        <tr>
          <td class="eBody alignCenter pdTp32"><p>You are ready to setup your new SimpleApp account.<br>
              Click the button below  to...</p>
            <table border="0" cellpadding="0" cellspacing="0" class="mainBtn">
              <tbody><tr>
                <td width="4" height="8" align="left" valign="top" class="btnLfTp"><img src="images/mainBtn_lftp.png" width="4" height="4"></td>
                <td class="emptyCell">&nbsp;</td>
                <td width="4" height="8" align="right" valign="top" class="btnRgTp"><img src="images/mainBtn_rgtp.png" width="4" height="4"></td>
              </tr>
              <tr>
                <td class="emptyCell">&nbsp;</td>
                <td class="btnMain"><a href="#"><span>Activate your Account</span></a></td>
                <td class="emptyCell">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="bottom" class="btnLfBt"><img src="images/mainBtn_lfbt.png" width="4" height="4"></td>
                <td class="emptyCell">&nbsp;</td>
                <td align="right" valign="bottom" class="btnRgBt"><img src="images/mainBtn_rgbt.png" width="4" height="4"></td>
              </tr>
            </tbody></table>
            <table border="0" cellpadding="0" cellspacing="0" class="subtleBtn">
              <tbody><tr>
                <td><a href="#"><span>Cancel subscription request</span></a></td>
              </tr>
            </tbody></table></td>
          <!-- end .eBody--> 
        </tr>
        <tr>
          <td class="bottomCorners"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody><tr>
                <td align="left" valign="bottom" class="crn_lfbt"><img src="images/body_lfbt.png" alt="" width="16" height="16"></td>
                <td class="emptyCell">&nbsp;</td>
                <td align="right" valign="bottom" class="crn_rgbt"><img src="images/body_rgbt.png" alt="" width="16" height="16"></td>
              </tr>
            </tbody></table></td>
        </tr>
        <tr>
          <td class="eFooter">© 2015 SimpleApp. All Rights Reserved. <br>
            <a href="#" class="highFix"><span>4170 Haymond St. • Mcdermitt • PA 18503 USA</span></a></td>
        </tr>
      </tbody></table>
      
      <!-- end .eBox --></td>
    <!-- end .emailBodyCell --> 
  </tr>
</tbody></table>
<!-- end #emailBody -->

</body></html>