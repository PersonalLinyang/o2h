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
		return Response::forge(View::forge($this->template . '/common/header', array(), false));
	}

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_footer($param = null)
	{
		return Response::forge(View::forge($this->template . '/common/footer', array(), false));
	}
	
}