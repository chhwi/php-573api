<?php
/**
 * 573.php - 573 API-like something
 * Copyright (C) 2015 koreapyj koreapyj0@gmail.com
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

class EAgate {
	function __construct() {
		// do something
		$this->domain = 'p.eagate.573.jp';
		$this->protocol = 'https';
		$this->port = '443';
		$this->user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25';
		$this->cookie = (object)array();
		$this->response_header = null;
	}

	public function auth($id, $password, $otp = null) {
		$this->post('gate/p/login.html', array('KID' => $id, 'pass' => $password, 'OTP' => $otp), 'https://p.eagate.573.jp/gate/p/login.html');
	}

	public function get($uri, $parameter=null, $referer=null) {
		if(empty($parameter))
			$query = '';
		elseif(is_string($parameter))
			$query = $parameter;
		elseif(is_array($parameter)) {
			$query = '';
			foreach($parameter as $key=>$value) {
				$query.=$key.'='.$value.'&';
			}
			$query = substr($query, 0, -1);
		}
		else
			$query = '';

		$cookie = '';
		if(!empty($this->cookie)) {
			foreach($this->cookie as $key => $value) {
				$cookie .= $key.'='.$value.'; ';
			}
			$cookie = substr($cookie, 0, -2);
		}

		$opts = array(
			'http'=>array(
				'follow_location' => false,
				'protocol_version'=> '1.1',
				'method'=> 'GET',
				'user_agent'=> $this->user_agent,
				'header'=> "Accept: text/html, */*\r\n"
					.(empty($referer)?'':"Referer: $referer\r\n")
					.(empty($cookie)?'':"Cookie: $cookie\r\n")
			)
		);
		$context = stream_context_create($opts);
		$url = $this->protocol.'://'.$this->domain.'/'.$uri.(empty($query)?'':'?'.$query);
		$response = @file_get_contents($url, false, $context);
		$header_list = [];
		foreach($http_response_header as $header) {
			if(!preg_match('/^(.*?): (.*)$/', $header, $header))
				continue;
			$header_list[$header[1]] = $header[2];
			switch(strtolower($header[1])) {
				case 'set-cookie':
					if(preg_match('/^(.*?)=(.*?); /', $header[2], $cookie))
						$this->cookie->$cookie[1] = $cookie[2];
					break;
			}
		}
		$this->response_header = (object)$header_list;
		return $response;
	}

	public function post($uri, $parameter=null, $referer=null) {
		if(empty($parameter))
			$query = '';
		elseif(is_string($parameter))
			$query = $parameter;
		elseif(is_array($parameter)) {
			$query = '';
			foreach($parameter as $key=>$value) {
				$query.=$key.'='.$value.'&';
			}
			$query = substr($query, 0, -1);
		}
		else
			$query = '';

		$cookie = '';
		if(!empty($this->cookie)) {
			foreach($this->cookie as $key => $value) {
				$cookie .= $key.'='.$value.'; ';
			}
			$cookie = substr($cookie, 0, -2);
		}

		$opts = array(
			'http'=>array(
				'follow_location' => false,
				'protocol_version'=>'1.1',
				'method'=> 'POST',
				'user_agent'=> $this->user_agent,
				'header'=> "Accept: text/html, */*\r\n"
					.(empty($referer)?'':"Referer: $referer\r\n")
					.(empty($cookie)?'':"Cookie: $cookie\r\n")
					.(empty($query)?'':"Content-Type: application/x-www-form-urlencoded\r\n"),
				'content'=> $query
			)
		);
		$context = stream_context_create($opts);
		$url = $this->protocol.'://'.$this->domain.'/'.$uri;
		$response = file_get_contents($url, false, $context);
		$header_list = [];
		foreach($http_response_header as $header) {
			if(!preg_match('/^(.*?): (.*)$/', $header, $header))
				continue;
			$header_list[$header[1]] = $header[2];
			switch(strtolower($header[1])) {
				case 'set-cookie':
					if(preg_match('/^(.*?)=(.*?); /', $header[2], $cookie))
						$this->cookie->$cookie[1] = $cookie[2];
					break;
			}
		}
		$this->response_header = (object)$header_list;
		return $response;
	}
}
