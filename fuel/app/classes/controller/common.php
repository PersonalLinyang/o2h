<?php
/* 
 * 通常共通パーツ
 */

class Controller_Common extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_header($param = null)
	{
		$data = array();

		//使う言語
		$data['language'] = $this->language;
		
		return Response::forge(View::forge($this->template . '/common/header_' . $this->language, $data, false));
	}

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_footer($param = null)
	{
		$data = array();
		return Response::forge(View::forge($this->template . '/common/footer_' . $this->language, $data, false));
	}
	
}