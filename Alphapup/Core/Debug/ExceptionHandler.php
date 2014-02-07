<?php
namespace Alphapup\Core\Debug;

use Alphapup\Core\Debug\Event\UncaughtExceptionEvent;
use Alphapup\Core\DependencyInjection\Container;
use Alphapup\Core\Http\Response;

class ExceptionHandler
{
	private 
		$_container,
		$_debug;
	
	public function __construct(Container $container,$debug=false)
	{
		$this->setContainer($container);
		$this->setDebug($debug);
	}
	
	private function _decorate($content, $title)
    {
        return <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title>{$title}</title>
        <style>
            /* Copyright (c) 2010, Yahoo! Inc. All rights reserved. Code licensed under the BSD License: http://developer.yahoo.com/yui/license.html */
            html{color:#000;background:#FFF;}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}fieldset,img{border:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}q:before,q:after{content:'';}abbr,acronym{border:0;font-variant:normal;}sup{vertical-align:text-top;}sub{vertical-align:text-bottom;}input,textarea,select{font-family:inherit;font-size:inherit;font-weight:inherit;}input,textarea,select{*font-size:100%;}legend{color:#000;}

            html { background: #eee; padding: 10px }
            body { font: 11px Verdana, Arial, sans-serif; color: #333 }
            img { border: 0; }
            .clear { clear:both; height:0; font-size:0; line-height:0; }
            .clear_fix:after { display:block; height:0; clear:both; visibility:hidden; }
            .clear_fix { display:inline-block; }
            * html .clear_fix { height:1%; }
            .clear_fix { display:block; }
            #content { width:970px; margin:0 auto; }
            .sf-exceptionreset, .sf-exceptionreset .block { margin: auto }
            .sf-exceptionreset abbr { border-bottom: 1px dotted #000; cursor: help; }
            .sf-exceptionreset p { font-size:14px; line-height:20px; color:#868686; padding-bottom:20px }
            .sf-exceptionreset strong { font-weight:bold; }
            .sf-exceptionreset a { color:#6c6159; }
            .sf-exceptionreset a img { border:none; }
            .sf-exceptionreset a:hover { text-decoration:underline; }
            .sf-exceptionreset em { font-style:italic; }
            .sf-exceptionreset h1, .sf-exceptionreset h2 { font: 20px Georgia, "Times New Roman", Times, serif }
            .sf-exceptionreset h2 span { background-color: #fff; color: #333; padding: 6px; float: left; margin-right: 10px; }
            .sf-exceptionreset .traces li { font-size:12px; padding: 2px 4px; list-style-type:decimal; margin-left:20px; }
            .sf-exceptionreset .block { background-color:#FFFFFF; padding:10px 28px; margin-bottom:20px;
                -webkit-border-bottom-right-radius: 16px;
                -webkit-border-bottom-left-radius: 16px;
                -moz-border-radius-bottomright: 16px;
                -moz-border-radius-bottomleft: 16px;
                border-bottom-right-radius: 16px;
                border-bottom-left-radius: 16px;
                border-bottom:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
            }
            .sf-exceptionreset .block_exception { background-color:#ddd; color: #333; padding:20px;
                -webkit-border-top-left-radius: 16px;
                -webkit-border-top-right-radius: 16px;
                -moz-border-radius-topleft: 16px;
                -moz-border-radius-topright: 16px;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                border-top:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
            }
            .sf-exceptionreset li a { background:none; color:#868686; text-decoration:none; }
            .sf-exceptionreset li a:hover { background:none; color:#313131; text-decoration:underline; }
            .sf-exceptionreset ol { padding: 10px 0; }
            .sf-exceptionreset h1 { background-color:#FFFFFF; padding: 15px 28px; margin-bottom: 20px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <div id="content" class="sf-exceptionreset">
            <h1>$title</h1>
            $content
        </div>
    </body>
</html>
EOF;
    }
	
	public function handle(\Exception $e) {
		//$this->_eventCenter->fire(new UncaughtExceptionEvent($e));
		
		// turn into mini-controller and create response
		$response = new Response();
		$title = "Darn it..someone call the nerds.";
		try {
			$view = $this->_container->get('view');
			$view->title($title);
			$view->theme('Alphapup','Core/Debug/Theme');
			
			if($this->_debug) {
				$view->exception = $e;
				$view->addView('Alphapup','Core/Debug/Views/Exception.php');
			}else{
				$view->addView('Alphapup','Core/Debug/Views/GeneralError.php');
			}
			$response->append($view->render());
		}catch(\Exception $e) {
			if($this->_debug) {
				$content = sprintf('Exception thrown when handling an exception (%s: %s)', get_class($e), $e->getMessage());
			}else{
				$content = "Something went wrong, but we'll get it patched up in a jiff.";
			}
			$response->append($this->_decorate($title,$content));
		}
		$response->render();
		return true;
	}
	
	public function register()
	{
		set_exception_handler(array($this,'handle'));
	}
	
	public function setContainer(Container $container)
	{
		$this->_container = $container;
	}
	
	public function setDebug($debug=false)
	{
		$this->_debug = $debug;
	}
}