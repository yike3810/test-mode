<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class Jsl extends Admin
{
	public function svn_do() {
		$last_line = passthru('/bin/svn up /opt/lampp/htdocs/ybh --username fuwuqi --password fuwuqi@1238347834!@!@ --no-auth-cache >> /tmp/08.txt', $retval);
		//$last_line = system('/bin/svn up /opt/lampp/htdocs/ybh', $retval);
		b($last_line);
		a($retval);
	}
}