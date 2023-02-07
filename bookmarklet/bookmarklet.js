/**
 * Dieses Bookmarklet kann einfach als Lesezeichen zu deinem Browser hinzugefuegt werden. Befindest du dich auf einer Webseite die du
 * speichern moechtest, klickst du auf das Bookmarklet. Daraufhin öffnet sich ein Popup, in welchem du den automatisch erfassten Titel
 * und die Url sowie die Tags und die Kategorie anpassen kannst. Mit einem Klick auf hinzufuegen speicherst du das Bookmark ab und das
 * Fenster wird wieder geschlossen.
 *
 * Um den ganzen Komfort dieses Bookmarklets nutzen zu koennen, solltest du Cookies aktiviert haben damit du dich beim Oeffnen des Popups
 * nicht erneut anmelden musst.
 *
 * Im Browser z.B. in deiner Lesezeichenleiste mit der rechten Maustaste klicken und "Lesezeichen hinzufuegen" waehlen. Den Titel kannst
 * du frei waehlen und als URL traegst du den hier gezeigten Code ein. Fertig!
 */
javascript:function p(a,w,h){var b=window.screenLeft!=undefined?window.screenLeft:screen.left;var c=window.screenTop!=undefined?window.screenTop:screen.top;width=window.innerWidth?window.innerWidth:document.documentElement.clientWidth?document.documentElement.clientWidth:screen.width;height=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:screen.height;var d=((width/2)-(w/2))+b;var e=((height/2)-(h/2))+c;var f=window.open(a,new Date().getTime(),'width='+w+', height='+h+', top='+e+', left='+d+'location=yes,resizable=yes,status=no,scrollbars=no,personalbar=no,toolbar=no,menubar=no');if(window.focus){f.focus()}}p('https://bookmarks.magicmarcy.de/addBookmark.php?title='+document.title+'&url='+document.URL,800,500);
