function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function adjust_popup()
{
        var w, h, fixedW, fixedH, diffW, diffH;
        if (document.documentElement && document.body.clientHeight==0) {     // Catches IE6 and FF in DOCMODE
                fixedW = document.documentElement.clientWidth;
                fixedH = document.documentElement.clientHeight;
                window.resizeTo(fixedW, fixedH);
                diffW = fixedW - document.documentElement.clientWidth;
                diffH = fixedH - document.documentElement.clientHeight;
                w = fixedW + diffW + 16; // Vert Scrollbar Always On in DOCMODE.
                h = fixedH + diffH;
                if (w >= screen.availWidth) h += 16;
        } else if (document.all) {
                fixedW = document.body.clientWidth;
                fixedH = document.body.clientHeight;
                window.resizeTo(fixedW, fixedH);
                diffW = fixedW - document.body.clientWidth;
                diffH = fixedH - document.body.clientHeight;
                w = fixedW + diffW;
                h = fixedH + diffH;
                if (h >= screen.availHeight) w += 16;
                if (w >= screen.availWidth)  h += 16;
        } else {
                fixedW = window.innerWidth;
                fixedH = window.innerHeight;
                window.resizeTo(fixedW, fixedH);
                diffW = fixedW - window.innerWidth;
                diffH = fixedH - window.innerHeight;
                w = fixedW + diffW;
                h = fixedH + diffH;
                if (w >= screen.availWidth)  h += 16;
                if (h >= screen.availHeight) w += 16;
        }
        w = Math.min(w,screen.availWidth);
        h = Math.min(h,screen.availHeight);
        window.resizeTo(w,h);
        window.moveTo((screen.availWidth-w)/2, (screen.availHeight-h)/2);
}
function show_section(e) {
    if (document.getElementById(e).style.display == 'none') {
        document.getElementById(e).style.display = 'block';
    } else {
        document.getElementById(e).style.display = 'none';
    }
}
function expand()
{
        var Nodes = document.getElementsByTagName("table")
        var max = Nodes.length
        for(var i = 0;i < max;i++) {
                var nodeObj = Nodes.item(i)
                var str = nodeObj.id
                if (str.match("section")) {
                        nodeObj.style.display = 'block';
                }
        }
}
function hideall()
{
        var Nodes = document.getElementsByTagName("table")
        var max = Nodes.length
        for(var i = 0;i < max;i++) {
                var nodeObj = Nodes.item(i)
                var str = nodeObj.id
                if (str.match("section")) {
                        nodeObj.style.display = 'none';
                }
        }
}