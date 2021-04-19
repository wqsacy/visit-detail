<?php

	namespace Wangqs\VisitDetail;


	use GuzzleHttp\Client;

	class Visit
	{

		/**
		 * @author     :  Wangqs  2021/4/19
		 * @description:  获取详情
		 */
		public static function detail ( string $url ) : array {

			if ( !$url ) {
				$url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['QUERY_STRING'];
			}

			$referer = $_SERVER['HTTP_REFERER'] ?? '';


			$origin = self::origin( $referer );

			if ( isset( $data['origin']['id'] ) )
				$keyword = self::keyword( $referer , $origin );
			else
				$keyword = '';


			return [
				'recruit' => self::recruit( $url ) ,
				'origin'  => $origin ,
				'keyword' => $keyword ,
			];

		}


		/**
		 * @author     :  Wangqs  2021/4/19
		 * @description:  是否收录
		 */
		protected static function recruit ( string $url ) {
			$url = 'http://www.baidu.com/s?wd=' . $url;
			$curl = curl_init();
			curl_setopt( $curl , CURLOPT_URL , $url );
			curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
			$rs = curl_exec( $curl );
			curl_close( $curl );
			if ( !strpos( $rs , '抱歉没有找到与' ) ) {
				return true;
			}
			else {
				return false;
			}
		}


		/**
		 * @author     :  Wangqs  2021/4/19
		 * @description:    来源
		 */
		protected static function origin ( string $referer ) {

			if ( !$referer || !strlen( $referer ) )
				return false;

			$origin = self::identifier();

			if ( is_array( $origin ) ) {
				foreach ( $origin as $key => $val ) {
					if ( strpos( $referer , $key ) !== false ) {
						return $val;
					}
				}
			}

			return false;
		}


		protected static function keyword ( string $referer , int $identifierId ) {

			switch ( $identifierId ) {
				case 1:     //百度PC
				case 2:     //百度移动
					return self::baiduEqid( $referer );
					break;


				default:
					return '';
			}

		}


		protected static function baiduEqid ( string $refer ) {
			$client = new Client();

			$param['headers'] = [
				'User-Agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36' ,
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
			];

			$refer = str_replace( '&amp;' , '&' , $refer );

			$param['body'] = 'refer=' . rawurlencode( $refer );

			$response = $client->post( 'https://chat.rainbowred.com/baiduAction.jsp' , $param );

			$body = $response->getBody(); //获取响应体，对象

			$bodyStr = (string) $body; //对象转字串

			return trim( str_replace( [ '关键字：' , '<br>' ] , '' , $bodyStr ) );
		}


		/**
		 * @author     :  Wangqs  2021/4/19
		 * @description:  搜索引擎类型
		 */
		protected static function identifier () : array {

			return [
				'www.baidu.com' => [
					'id'   => 1 ,
					'name' => '百度PC' ,
				] ,

				'm.baidu.com' => [
					'id'   => 2 ,
					'name' => '百度移动' ,
				] ,

				'www.sogou.com' => [
					'id'   => 3 ,
					'name' => '搜狗PC' ,
				] ,

				'wap.sogou.com' => [
					'id'   => 4 ,
					'name' => '搜狗移动' ,
				] ,

				'www.so.com' => [
					'id'   => 5 ,
					'name' => '360PC' ,
				] ,

				'm.so.com' => [
					'id'   => 6 ,
					'name' => '360移动' ,
				] ,

				'bing.com' => [
					'id'   => 7 ,
					'name' => 'bing' ,
				] ,

				'quark.sm.cn' => [
					'id'   => 8 ,
					'name' => '神马' ,
				] ,

				'google.com' => [
					'id'   => 9 ,
					'name' => '谷歌' ,
				] ,

			];
		}


	}