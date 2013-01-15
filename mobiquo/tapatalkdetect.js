
if (typeof tapatalk_detected_loaded === 'undefined')
{
    function detectTapatalk() {
        if (document.cookie.indexOf("tapatalk_redirect4=false") < 0)
        {
            if (!navigator.userAgent.match(/Opera/i))
            {
                if (((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) && has_iphone_url) {
                    setTapatalkCookies();
                    if (confirm(iphone_description))
                        window.location = iphone_url;
                } else if(navigator.userAgent.match(/iPad/i) && has_ipad_url) {
                    setTapatalkCookies();
                    if (confirm(ipad_description))
                        window.location = ipad_url;
                } else if(navigator.userAgent.match(/Kindle Fire/i) && has_KF_url) {
                    setTapatalkCookies();
                    if (confirm(KF_description))
                        window.location = KF_url;
                } else if(navigator.userAgent.match(/Android/i) && has_Android_url) {
                    setTapatalkCookies();
                    if (confirm(Android_description))
                        window.location = Android_url;
                } else if(navigator.userAgent.match(/BlackBerry/i)) {
                    setTapatalkCookies();
                    if (confirm("This forum has an app for BlackBerry! Click OK to learn more about Tapatalk."))
                        window.location = "http://appworld.blackberry.com/webstore/content/46654?lang=en";
                } else if(window.chrome) {
                    setTapatalkCookies();
                    var script1 = document.createElement('script');
                    var script2;
                    script1.setAttribute('src','mobiquo/tapatalkdetect/jquery-1.7.min.js');
                    script1.setAttribute('type','text/javascript');
                    var loaded=false;
                    var loaded2=false;
                    var loaded3=false;
                    var loadFunction3 = function() {
                        var notice = '<div class="notice">'
                            + '<div class="notice-body">'
                            + '<p>Download <a href="https://chrome.google.com/webstore/detail/plfhcjljnfjpfcbjpgnflfofmahljkjj" target="_new">Tapatalk Notifier</a> for Chrome to keep notified of new Private Messages from this forum. <br /></p>'
                            + '</div>'
                            + '</div>';
                        $( notice ).purr(
                            {
                                usingTransparentPNG: true,
                                removeTimer:12000
                            }
                        );
                    }

                    var loadFunction = function()
                    {
                        var script2=document.createElement("link");
                        script2.setAttribute("rel", "stylesheet");
                        script2.setAttribute("type", "text/css");
                        script2.setAttribute("href", 'mobiquo/tapatalkdetect/notice.css');
                        script2.media='screen';

                        document.getElementsByTagName("head")[0].appendChild(script2);
                        var script3 = document.createElement('script');
                        script3.setAttribute('src','mobiquo/tapatalkdetect/jquery.purr.js');
                        script3.setAttribute('type','text/javascript');
                        script3.onload=loadFunction3;
                        document.getElementsByTagName("head")[0].appendChild(script3);
                    };
                    
                    script1.onload = loadFunction;
                    document.getElementsByTagName("head")[0].appendChild(script1);
                }
            }
        }
    }

    function setTapatalkCookies() {
        var date = new Date();
        var days = 90;
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+ date.toGMTString();
        var domain = "; path=/";
        document.cookie = "tapatalk_redirect4=false" + expires + domain;
    }

    detectTapatalk();

    var tapatalk_detected_loaded = true;
}
