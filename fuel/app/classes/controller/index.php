<?php
/* 
 * ホームページ
 */

class Controller_Index extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//共通ヘッダー取得
		$data['header'] = Request::forge('common/header')->execute()->response();
		//共通フッター取得
		$data['footer'] = Request::forge('common/footer')->execute()->response();

		//使う言語
		$data['language'] = $this->language;

		switch($this->language) {
			case 'ja':
				//TDK
				$data['title'] = '株式会社O2H';
				$data['description'] = 'O2Hのコーポレートサイトです。企業理念やプロジェクト、会社概要、イベント情報などを掲載しております。';
				$data['keywords'] = 'O2H,オーツーエッチ,中国,日本,旅行';
				$data['canonical'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
				break;
			default:
				//TDK
				$data['title'] = 'O2H有限公司';
				$data['description'] = '本网站为旅游公司O2H的官方网站。网站中登载着企业理念，服务范围，公司概要，活动情报等信息。';
				$data['keywords'] = 'O2H,中国,日本,旅游';
				$data['canonical'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
				break;
		}

		//メインビジュアルアリア
		$data['mainv'] = View::forge($this->template . '/parts/index/mainv_' . $this->language);

		//目標アリア
		$data['mission'] = View::forge($this->template . '/parts/index/mission_' . $this->language);

		//プロジェクトアリア
		$data['project'] = View::forge($this->template . '/parts/index/project_' . $this->language);

		//会社情報
		$data['company'] = View::forge($this->template . '/parts/index/company_' . $this->language);
		
		//View呼び出す
		return Response::forge(View::forge($this->template . '/index', $data, false));
	}
	
}