// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

var clockID = 0;
var clockDIV='';

function FormatClockNumber(number)
{
	if (number<10)
	{
		number="0" + number;
	}
	return number;
}

function UpdateClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }

   var tDate = new Date();

   document.getElementById(clockDIV).innerHTML = ""
                            + FormatClockNumber(tDate.getHours()) + ":"
                            + FormatClockNumber(tDate.getMinutes()) + ":"
                            + FormatClockNumber(tDate.getSeconds());

   clockID = setTimeout("UpdateClock()", 1000);
}
function StartClock(divName)
{
   clockDIV=divName;
   clockID = setTimeout("UpdateClock()", 500);
}

function KillClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }
}
