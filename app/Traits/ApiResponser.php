<?php

namespace App\Traits;

trait ApiResponser
{

	/**
	 * Build success responses.
	 *
	 * @param string $message
	 * @param int $code
	 * @param array $data
	 * @return Illuminate\Http\JsonResponse
	 */
	public function successResponse($message, $code, $data = '') {
		$params = ['flag' => $code, 'msg' => $message];
		if (!empty(($data))) {
			$params = ['flag' => $code, 'msg' => $message, 'data' => $data];
		}
		return response()->json($params);
	}
	/**
	 * Build success responses for total posts
	 *
	 * @param string $message
	 * @param int $code
	 * @param array $data
	 * @return Illuminate\Http\JsonResponse
	 */
	public function successResponseOfTotalPosts($message, $code,$total_posts='', $data = '') {
		$params = ['flag' => $code, 'msg' => $message];
		if (!empty(($total_posts))) {
			$params = ['flag' => $code, 'msg' => $message,'total_posts'=>$total_posts, 'data' => $data];
		}
		return response()->json($params);
	}
	

	/**
	 * Build error responses.
	 *
	 * @param string $message
	 * @param int $code
	 * @return Illuminate\Http\JsonResponse
	 */
	public function errorResponse($message, $code) {
		$params = ['flag' => $code, 'msg' => $message];
		return response()->json($params);
	}

	/**
	 * Build error responses.
	 *
	 * @param string|array $message
	 * @param int $code
	 * @return Illuminate\Http\Response
	 */
	public function errorMessage($message, $code) {
		return response($message, $code)->header('Content-Type', 'application/json');
	}

	/**
	 * Note this function is same as the below function but instead of errorResponse with error below function returns error json.
	 *
	 * Throw Validation.
	 * @param string $message
	 * @param int $code
	 * @return mix
	 */
	public function throwValidation($message, $code) {
		return $this->errorResponse($message, $code);
	}

}
